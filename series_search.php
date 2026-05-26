<?php
// ============================================================================
// series_search.php — VERSION FINALE AVEC URL TOUJOURS AFFICHÉE + OUVERTURE AUTO
// ============================================================================

ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', 0);
while (ob_get_level() > 0) ob_end_flush();
flush();
set_time_limit(0);

$pdo = new PDO(
    "mysql:host=localhost;dbname=comics_crypt;charset=utf8mb4",
    "root",
    "",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// ---------------------------------------------------------------------------
// RÉCUPÉRATION DES ÉDITEURS ACTIFS
// ---------------------------------------------------------------------------
$publishers = $pdo->query("
    SELECT publisher_id, name, country 
    FROM publishers 
    WHERE actif = 1 
    ORDER BY name ASC
")->fetchAll(PDO::FETCH_ASSOC);

// ---------------------------------------------------------------------------
// TRAITEMENT RECHERCHE
// ---------------------------------------------------------------------------
$series = [];
$error = "";
$url = "";
$open_url = false;

if (!empty($_POST["action"]) && $_POST["action"] === "search") {

    $pub_name = trim($_POST["publisher_name"]);
    $country  = trim($_POST["publisher_country"]);
    $keyword  = trim($_POST["keyword"]);

    if ($pub_name === "" || $country === "" || $keyword === "") {
        $error = "Veuillez remplir tous les champs.";
    } else {

        // Construction URL Comics.org
        $params = http_build_query([
            "target" => "series",
            "method" => "icontains",
            "in_selected_collection" => "on",
            "order1" => "series",
            "order2" => "date",
            "pub_name" => $pub_name,
            "country" => $country,
            "series" => $keyword,
            "_export" => "db_json"
        ]);

        $url = "https://www.comics.org/search/advanced/process/?" . $params;

        // On demandera au JS d’ouvrir l’URL
        $open_url = true;

        // Requête cURL
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_USERAGENT => "ComicsCrypt/1.0"
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        // Détection JSON robuste
        $invalid = false;

        if (!$response) $invalid = true;
        if (strlen($response) < 10) $invalid = true;
        if (stripos($response, "<html") !== false) $invalid = true;
        if (trim($response)[0] !== '[') $invalid = true;

        $decoded = json_decode($response, true);
        if (!is_array($decoded)) $invalid = true;

        if ($invalid) {
            $error = "Aucun résultat trouvé ou réponse invalide de Comics.org. Essayez un mot‑clé plus précis.";
        } else {
            $series = $decoded;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Recherche Séries Comics.org</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body { padding: 20px; }
#loader { display:none; }
.text-nowrap button, .text-nowrap a { margin-right: 4px; }
</style>
</head>
<body>

<h1>Recherche de Séries (Comics.org)</h1>

<!-- FORMULAIRE -->
<form method="post" id="searchForm">
    <input type="hidden" name="action" value="search">

    <div class="row g-3">

        <div class="col-md-4">
            <label class="form-label">Éditeur</label>
            <select name="publisher_name" class="form-select" required>
                <option value="">-- Choisir un éditeur --</option>
                <?php foreach ($publishers as $p): ?>
                    <option value="<?= htmlspecialchars($p["name"]) ?>"
                            data-country="<?= htmlspecialchars($p["country"]) ?>">
                        <?= htmlspecialchars($p["name"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <input type="hidden" name="publisher_country" id="publisher_country">

        <div class="col-md-4">
            <label class="form-label">Mot-clé série</label>
            <input type="text" name="keyword" class="form-control" required>
        </div>

        <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-primary w-100">Rechercher</button>
        </div>
    </div>
</form>

<!-- LOADER -->
<div id="loader" class="mt-4">
    <div class="alert alert-info">Recherche en cours…</div>
    <div class="progress">
        <div class="progress-bar progress-bar-striped progress-bar-animated" 
             role="progressbar" style="width: 100%"></div>
    </div>
</div>

<hr>

<!-- MESSAGE D’ERREUR -->
<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- AFFICHAGE DE L’URL DE LA REQUÊTE (TOUJOURS) -->
<?php if (!empty($_POST["action"]) && $_POST["action"] === "search"): ?>
    <?php if (!empty($url)): ?>
        <div class="alert alert-secondary mt-4">
            <strong>Requête utilisée :</strong><br>
            <a href="<?= htmlspecialchars($url) ?>" target="_blank">
                <?= htmlspecialchars($url) ?>
            </a>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if (!empty($series)): ?>
<h2>Résultats</h2>

<table id="seriesTable" class="table table-striped">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Volume</th>
            <th>Début</th>
            <th>Fin</th>
            <th>Issues</th>
            <th>Publisher</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>

<?php foreach ($series as $s): ?>

    <?php
        $id_comicsorg = $s["id"] ?? null;
        $nom          = $s["name"] ?? "";
        $volume       = $s["year_began"] ?? "";
        $issue_count  = $s["issue_count"] ?? "";
        $publisher    = $s["publisher"] ?? "";
        $pub_dates    = $s["publication_dates"] ?? "";

        $date_debut = "";
        $date_fin   = "";
        if (strpos($pub_dates, " - ") !== false) {
            list($d1, $d2) = explode(" - ", $pub_dates);
            $date_debut = date("Y-m-01", strtotime($d1));
            $date_fin   = date("Y-m-01", strtotime($d2));
        }
    ?>

    <tr>
        <td><?= htmlspecialchars($nom) ?></td>
        <td><?= htmlspecialchars($volume) ?></td>
        <td><?= htmlspecialchars($date_debut) ?></td>
        <td><?= htmlspecialchars($date_fin) ?></td>
        <td><?= htmlspecialchars($issue_count) ?></td>
        <td><?= htmlspecialchars($publisher) ?></td>

        <td class="text-nowrap">

            <a href="https://www.comics.org/series/<?= htmlspecialchars($id_comicsorg) ?>/" 
               target="_blank" 
               class="btn btn-secondary btn-sm"
               title="Consulter la fiche">
                <i class="fa-solid fa-up-right-from-square"></i>
            </a>

            <button class="btn btn-success btn-sm addBtn"
                data-id="<?= htmlspecialchars($id_comicsorg) ?>"
                data-name="<?= htmlspecialchars($nom) ?>"
                data-volume="<?= htmlspecialchars($volume) ?>"
                data-debut="<?= htmlspecialchars($date_debut) ?>"
                data-fin="<?= htmlspecialchars($date_fin) ?>"
                data-issues="<?= htmlspecialchars($issue_count) ?>"
                data-publisher="<?= htmlspecialchars($publisher) ?>"
                title="Ajouter en base">
                <i class="fa-solid fa-plus"></i>
            </button>

        </td>
    </tr>

<?php endforeach; ?>

    </tbody>
</table>

<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
// Loader indéterminé
document.getElementById("searchForm").addEventListener("submit", function() {
    document.getElementById("loader").style.display = "block";
});

// Remplit automatiquement le pays de l’éditeur
document.querySelector("select[name='publisher_name']").addEventListener("change", function() {
    let opt = this.selectedOptions[0];
    document.getElementById("publisher_country").value = opt.dataset.country;
});

// Active DataTables
$(document).ready(function() {
    if ($('#seriesTable').length) {
        $('#seriesTable').DataTable();
    }
});

// Ajout en BDD
$(document).on("click", ".addBtn", function() {

    $.post("series_search.php", {
        ajax_action: "add",
        id_comicsorg: $(this).data("id"),
        name: $(this).data("name"),
        volume: $(this).data("volume"),
        debut: $(this).data("debut"),
        fin: $(this).data("fin"),
        issues: $(this).data("issues"),
        publisher: $(this).data("publisher")
    }, function(msg) {
        alert(msg);
    });
});

// OUVERTURE AUTOMATIQUE DE L’URL DANS UN NOUVEL ONGLET
<?php if ($open_url && !empty($url)): ?>
window.open("<?= htmlspecialchars($url) ?>", "_blank");
<?php endif; ?>
</script>

</body>
</html>
