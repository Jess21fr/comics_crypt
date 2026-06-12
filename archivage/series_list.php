<?php
ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', 0);
while (ob_get_level() > 0) ob_end_flush();
flush();
set_time_limit(0);

$run = isset($_POST["run"]);
$search_name = isset($_POST["search_name"]) ? trim($_POST["search_name"]) : "";
$search_year = isset($_POST["search_year"]) ? trim($_POST["search_year"]) : "";
$selected_publisher_id = isset($_POST["publisher_filter"]) ? (int)$_POST["publisher_filter"] : 0;

// Connexion DB (nécessaire dès maintenant pour charger les éditeurs actifs)
$pdo = new PDO(
    "mysql:host=localhost;dbname=comics_crypt;charset=utf8mb4",
    "root",
    "",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Charger les éditeurs actifs
$activePublishers = $pdo->query("
    SELECT publisher_id, name 
    FROM publishers 
    WHERE actif = 1 
    ORDER BY name ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Import Series (API Comics.org)</title>
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
input[type="text"], select {
    padding: 8px;
    font-size: 16px;
    width: 300px;
    margin-bottom: 10px;
}
label {
    display: block;
    margin-top: 10px;
}
</style>

<script>
function addLog(html) {
    document.getElementById('main').innerHTML += html;
}
function addSeries(name) {
    document.getElementById('seriesList').innerHTML += "<div>• " + name + "</div>";
}
</script>

</head>
<body>

<div id="sidebar">
    <h3>Séries importées</h3>
    <div id="seriesList"></div>
</div>

<div id="main">
<?php if (!$run): ?>
    <h2>Import des Séries (API Comics.org)</h2>

    <form method="post">

        <label for="search_name">Nom de la série recherchée</label>
        <input type="text" id="search_name" name="search_name" required>

        <label for="search_year">Année de publication (optionnelle)</label>
        <input type="text" id="search_year" name="search_year">

        <label for="publisher_filter">Éditeur (filtrage obligatoire)</label>
        <select id="publisher_filter" name="publisher_filter" required>
            <option value="">-- Choisir un éditeur --</option>
            <?php foreach ($activePublishers as $pub): ?>
                <option value="<?= htmlspecialchars($pub['publisher_id']) ?>">
                    <?= htmlspecialchars($pub['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <br><br>
        <button type="submit" name="run" value="1">Lancer la recherche et l'import</button>
    </form>

<?php else: ?>
    <h2>Import en cours…</h2>
    <p>
        <strong>Mot-clé :</strong> <?= htmlspecialchars($search_name) ?>
        <?php if ($search_year !== ""): ?>
            — <strong>Année :</strong> <?= htmlspecialchars($search_year) ?>
        <?php endif; ?>
        — <strong>Éditeur :</strong> <?= htmlspecialchars($selected_publisher_id) ?>
    </p>
<?php endif; ?>
</div>

</body>
</html>

<?php
if (!$run) exit;

// ------------------------------------------------------------
// LOG + API
// ------------------------------------------------------------
function log_msg($msg) {
    echo "<script>addLog(" . json_encode("<p>$msg</p>") . ");</script>";
    flush();
}

function log_series($name) {
    echo "<script>addSeries(" . json_encode($name) . ");</script>";
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
        log_msg("<span style='color:red;'>Erreur API (code $code).</span>");
        return null;
    }

    return json_decode($response, true);
}

// ------------------------------------------------------------
// INSERT SQL
// ------------------------------------------------------------
$insert = $pdo->prepare("
    INSERT INTO series (nom, volume, nbre_episodes, date_debut, date_fin, id_comicsorg, publisher_id, univers_id, logo)
    VALUES (:nom, :volume, :nbre_episodes, :date_debut, :date_fin, :id_comicsorg, :publisher_id, :univers_id, :logo)
    ON DUPLICATE KEY UPDATE
        nom = VALUES(nom),
        date_debut = VALUES(date_debut),
        date_fin = VALUES(date_fin),
        publisher_id = VALUES(publisher_id)
");

// ------------------------------------------------------------
// CONSTRUCTION URL API
// ------------------------------------------------------------
$encoded_name = rawurlencode($search_name);
$baseUrl = "https://www.comics.org/api/series/name/" . $encoded_name . "/";

if ($search_year !== "" && ctype_digit($search_year)) {
    $baseUrl .= "year/" . $search_year . "/";
}

$baseUrl .= "?format=json";

$currentUrl = $baseUrl;
$requestCount = 0;
$totalImported = 0;

// ------------------------------------------------------------
// BOUCLE PAGINATION
// ------------------------------------------------------------
while ($currentUrl) {

    $requestCount++;
    if ($requestCount % 20 === 0) {
        log_msg("<span style='color:orange;'>20 requêtes → pause 20 minutes…</span>");
        sleep(20 * 60);
    }

    $json = api_get_json($currentUrl);
    if (!$json || empty($json["results"])) {
        log_msg("<span style='color:red;'>Aucun résultat ou réponse vide.</span>");
        break;
    }

    foreach ($json["results"] as $serie) {

        // Filtrer pays = US
        if (($serie["country"] ?? "") !== "us") continue;

        // Extraire publisher_id Comics.org
        $publisher_id = null;
        if (!empty($serie["publisher"]) && preg_match('~/publisher/(\d+)/~', $serie["publisher"], $mp)) {
            $publisher_id = (int)$mp[1];
        }

        // Filtrer sur l'éditeur choisi
        if ($publisher_id !== $selected_publisher_id) continue;

        // Extraire id_comicsorg
        if (empty($serie["api_url"]) || !preg_match('~/series/(\d+)/~', $serie["api_url"], $m)) continue;
        $id_comicsorg = (int)$m[1];

        $nom = $serie["name"] ?? null;

        // Dates
        $date_debut = !empty($serie["year_began"]) ? sprintf("%04d-01-01", (int)$serie["year_began"]) : null;
        $date_fin   = !empty($serie["year_ended"]) ? sprintf("%04d-01-01", (int)$serie["year_ended"]) : null;

        // Champs non renseignés pour l'instant
        $volume = null;
        $nbre_episodes = null;
        $univers_id = null;
        $logo = null;

        // INSERT
        $insert->execute([
            ":nom"           => $nom,
            ":volume"        => $volume,
            ":nbre_episodes" => $nbre_episodes,
            ":date_debut"    => $date_debut,
            ":date_fin"      => $date_fin,
            ":id_comicsorg"  => $id_comicsorg,
            ":publisher_id"  => $publisher_id,
            ":univers_id"    => $univers_id,
            ":logo"          => $logo
        ]);

        $totalImported++;
        log_series($nom);
    }

    log_msg("<span style='color:green;'>Page traitée. Séries importées cumulées : $totalImported.</span>");

    // Pagination
    if (!empty($json["next"])) {
        $currentUrl = $json["next"];
        log_msg("Page suivante → " . htmlspecialchars($currentUrl));
        sleep(1);
    } else {
        $currentUrl = null;
    }
}

log_msg("<strong>Import terminé. Total séries importées : $totalImported.</strong>");
?>
