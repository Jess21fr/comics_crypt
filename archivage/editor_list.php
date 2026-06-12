<?php
ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', 0);
while (ob_get_level() > 0) ob_end_flush();
flush();
set_time_limit(0);

$run = isset($_POST["run"]);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Import Publishers</title>
<style>
body {
    margin: 0;
    font-family: Arial;
    display: flex;
    height: 100vh;
}
#sidebar {
    width: 300px;
    background: #222;
    color: #fff;
    padding: 15px;
    overflow-y: auto;
}
#sidebar h3 {
    margin-top: 0;
    color: #4CAF50;
}
#main {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
}
button {
    padding: 12px 20px;
    font-size: 18px;
    cursor: pointer;
}
</style>

<script>
// Fonctions JS appelées par le PHP
function addLog(html) {
    document.getElementById('main').innerHTML += html;
}
function addPublisher(name) {
    document.getElementById('publisherList').innerHTML += "<div>• " + name + "</div>";
}
</script>

</head>
<body>

<div id="sidebar">
    <h3>Éditeurs importés</h3>
    <div id="publisherList"></div>
</div>

<div id="main">
<?php if (!$run): ?>
    <h2>Import des Publishers (Comics.org)</h2>
    <form method="post">
        <button type="submit" name="run" value="1">Lancer l'import</button>
    </form>
<?php else: ?>
    <h2>Import en cours…</h2>
<?php endif; ?>
</div>

</body>
</html>

<?php
// ------------------------------------------------------------
// STOP ici si on n'a pas cliqué sur "Lancer l'import"
// ------------------------------------------------------------
if (!$run) exit;

// ------------------------------------------------------------
// À partir d'ici : IMPORT (tout est envoyé après le HTML)
// ------------------------------------------------------------

$pdo = new PDO(
    "mysql:host=localhost;dbname=comics_crypt;charset=utf8mb4",
    "root",
    "",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

function log_msg($msg) {
    echo "<script>addLog(" . json_encode("<p>$msg</p>") . ");</script>";
    flush();
}

function log_pub($name) {
    echo "<script>addPublisher(" . json_encode($name) . ");</script>";
    flush();
}

function api_get_json(string $url): ?array {
    log_msg("Requête API : $url");

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_USERAGENT => "ComicsCrypt/1.0"
    ]);

    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code == 429) {
        log_msg("<span style='color:red;'>429 détecté → pause 60s…</span>");
        sleep(60);
        return api_get_json($url);
    }

    if ($code !== 200 || !$response) {
        return null;
    }

    return json_decode($response, true);
}

$insert = $pdo->prepare("
    INSERT INTO publishers (name, country, publisher_id)
    VALUES (:name, :country, :publisher_id)
    ON DUPLICATE KEY UPDATE name = VALUES(name), country = VALUES(country)
");

$progressFile = "progress.txt";
$startPage = file_exists($progressFile) ? (int)trim(file_get_contents($progressFile)) : 1;

log_msg("Reprise à la page <strong>$startPage</strong>");

$baseUrl = "https://www.comics.org/api/publisher/?format=json";
$requestCount = 0;
$currentPage = $startPage;

while (true) {

    $url = $baseUrl . "&page=" . $currentPage;
    $requestCount++;

    if ($requestCount % 20 === 0) {
        log_msg("<span style='color:orange;'>20 requêtes → pause 20 minutes…</span>");
        sleep(20 * 60);
    }

    $json = api_get_json($url);

    if (!$json || empty($json["results"])) {
        log_msg("<span style='color:red;'>Page $currentPage vide → pause 120s…</span>");
        sleep(120);

        $json = api_get_json($url);

        if (!$json || empty($json["results"])) {
            log_msg("<span style='color:red;'>Toujours vide → arrêt.</span>");
            file_put_contents($progressFile, $currentPage);
            exit;
        }
    }

    foreach ($json["results"] as $pub) {
        if (!preg_match('~/publisher/(\d+)/~', $pub["api_url"], $m)) continue;

        $insert->execute([
            ":name"         => $pub["name"],
            ":country"      => $pub["country"] ?? null,
            ":publisher_id" => (int)$m[1]
        ]);

        log_pub($pub["name"]);
    }

    log_msg("<span style='color:green;'>Page $currentPage importée.</span>");

    file_put_contents($progressFile, $currentPage);
    $currentPage++;

    sleep(1);
}
?>