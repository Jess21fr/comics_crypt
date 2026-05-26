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
<title>Liste des Séries (Marvel)</title>

<style>
body {
    margin: 0;
    font-family: Arial;
    display: flex;
    height: 100vh;
}
#sidebar {
    width: 350px;
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
function addLog(html) {
    document.getElementById('main').innerHTML += html;
}
function addSerie(name) {
    document.getElementById('serieList').innerHTML += "<div>• " + name + "</div>";
}
</script>

</head>
<body>

<div id="sidebar">
    <h3>Séries trouvées</h3>
    <div id="serieList"></div>
</div>

<div id="main">
<?php if (!$run): ?>
    <h2>Recherche des séries Marvel</h2>

    <form method="post">
        <button type="submit" name="run" value="1">Lancer la recherche</button>
    </form>

<?php else: ?>
    <h2>Recherche en cours…</h2>
<?php endif; ?>
</div>

</body>
</html>

<?php
if (!$run) exit;

// ------------------------------------------------------------
// Fonction Playwright
// ------------------------------------------------------------
function get_html($url) {
    log_msg("Requête Playwright : $url");

    $cmd = "node fetch_html.js " . escapeshellarg($url);
    $html = shell_exec($cmd);

    if (!$html || strlen(trim($html)) < 50) {
        log_msg("<span style='color:red;'>HTML vide → retry 5s…</span>");
        sleep(5);
        return get_html($url);
    }

    return $html;
}

// ------------------------------------------------------------
// Fonctions d'affichage
// ------------------------------------------------------------
function log_msg($msg) {
    echo "<script>addLog(" . json_encode("<p>$msg</p>") . ");</script>";
    flush();
}

function log_serie($name) {
    echo "<script>addSerie(" . json_encode($name) . ");</script>";
    flush();
}

// ------------------------------------------------------------
// Boucle de pagination
// ------------------------------------------------------------
$page = 1;

while (true) {

    $url = "https://www.comics.org/search/advanced/process/?" .
           "target=series" .
           "&method=icontains" .
           "&in_selected_collection=on" .
           "&order1=series" .
           "&order2=date" .
           "&pub_name=marvel" .
           "&country=us" .
           "&page=" . $page;

    $html = get_html($url);

    if (!preg_match_all('#<tr>(.*?)</tr>#s', $html, $rows)) {
        log_msg("<strong>Fin des résultats.</strong>");
        break;
    }

    $count = 0;

    foreach ($rows[1] as $row) {

        if (!preg_match('#/series/(\d+)/#', $row, $m)) continue;
        $count++;

        preg_match('#<a href="/series/\d+/">(.*?)</a>#', $row, $m);
        $nom = trim(strip_tags(html_entity_decode($m[1] ?? "")));

        log_serie($nom);
    }

    if ($count === 0) {
        log_msg("<span style='color:red;'>Aucune série trouvée → arrêt.</span>");
        break;
    }

    log_msg("<span style='color:green;'>Page $page traitée ($count séries).</span>");

    $page++;
    sleep(1);
}

log_msg("<h3>Recherche terminée !</h3>");
?>
