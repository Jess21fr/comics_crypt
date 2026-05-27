<?php
// ============================================================================
// series_json_importer.php
// Lecture JSON côté PHP + loader indéterminé + pictos FontAwesome
// ============================================================================

ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', 0);
while (ob_get_level() > 0) ob_end_flush();
flush();
set_time_limit(0);

// ---------------------------------------------------------------------------
// CONNEXION BDD
// ---------------------------------------------------------------------------
$pdo = new PDO(
    "mysql:host=localhost;dbname=comics_crypt;charset=utf8mb4",
    "root",
    "",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// ============================================================================
// TRAITEMENT AJAX : AJOUT EN BDD
// ============================================================================
if (!empty($_POST["ajax_action"]) && $_POST["ajax_action"] === "add") {

    $required = ["id_comicsorg", "name", "volume", "debut", "fin", "issues", "publisher"];
    foreach ($required as $r) {
        if (!isset($_POST[$r])) {
            echo "Données manquantes ($r).";
            exit;
        }
    }

    $id_comicsorg = (int)$_POST["id_comicsorg"];
    $nom          = trim($_POST["name"]);
    $volume       = trim($_POST["volume"]);
    $date_debut   = $_POST["debut"] ?: null;
    $date_fin     = $_POST["fin"] ?: null;
    $nbre_issues  = $_POST["issues"] !== "" ? (int)$_POST["issues"] : null;
    $publisher_id = (int)$_POST["publisher"];

    if ($id_comicsorg === 0 || $nom === "" || $publisher_id === 0) {
        echo "Données invalides.";
        exit;
    }

    // Vérifier éditeur
    $checkPub = $pdo->prepare("SELECT 1 FROM publishers WHERE publisher_id = :pid");
    $checkPub->execute([":pid" => $publisher_id]);
    if (!$checkPub->fetch()) {
        echo "Éditeur introuvable en base.";
        exit;
    }

    $sql = "
    INSERT INTO series (nom, volume, nbre_episodes, date_debut, date_fin, id_comicsorg, publisher_id, univers_id, logo)
    VALUES (:nom, :volume, :nbre_episodes, :date_debut, :date_fin, :id_comicsorg, :publisher_id, NULL, NULL)
    ON DUPLICATE KEY UPDATE
        nom = VALUES(nom),
        volume = VALUES(volume),
        nbre_episodes = VALUES(nbre_episodes),
        date_debut = VALUES(date_debut),
        date_fin = VALUES(date_fin),
        publisher_id = VALUES(publisher_id)
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":nom"           => $nom,
        ":volume"        => $volume !== "" ? $volume : null,
        ":nbre_episodes" => $nbre_issues,
        ":date_debut"    => $date_debut,
        ":date_fin"      => $date_fin,
        ":id_comicsorg"  => $id_comicsorg,
        ":publisher_id"  => $publisher_id
    ]);

    echo "OK";
    exit;
}

// ============================================================================
// LECTURE JSON CÔTÉ PHP (ancienne méthode)
// ============================================================================
$series = [];
$error = "";

if (!empty($_FILES["json_file"]["tmp_name"])) {

    // On flush pour éviter l’alerte “page bloquée”
    echo str_repeat(" ", 4096);
    flush();

    $content = file_get_contents($_FILES["json_file"]["tmp_name"]);

    if (!$content) {
        $error = "Impossible de lire le fichier JSON.";
    } else {
        $series = json_decode($content, true);
        if (!is_array($series)) {
            $error = "JSON invalide.";
            $series = [];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Import Séries via JSON (Comics.org)</title>

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

<h1>Import des Séries via JSON Comics.org</h1>

<!-- FORMULAIRE -->
<form method="post" enctype="multipart/form-data" onsubmit="showLoader()">
    <label class="form-label">Sélectionnez le fichier JSON exporté depuis Comics.org</label>
    <input type="file" name="json_file" accept=".json" class="form-control" required>

    <button class="btn btn-primary mt-3" type="submit">Analyser le fichier</button>
</form>

<!-- LOADER INDETERMINE -->
<div id="loader" class="mt-4">
    <div class="alert alert-info">Chargement du fichier…</div>
    <div class="progress">
        <div class="progress-bar progress-bar-striped progress-bar-animated" 
             role="progressbar" style="width: 100%"></div>
    </div>
</div>

<hr>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
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

            <!-- Consulter -->
            <a href="https://www.comics.org/series/<?= htmlspecialchars($id_comicsorg) ?>/" 
               target="_blank" 
               class="btn btn-secondary btn-sm"
               title="Consulter la fiche">
                <i class="fa-solid fa-up-right-from-square"></i>
            </a>

            <!-- Ajouter -->
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
// Affiche le loader indéterminé
function showLoader() {
    document.getElementById("loader").style.display = "block";
}

// Active DataTables
$(document).ready(function() {
    if ($('#seriesTable').length) {
        $('#seriesTable').DataTable();
    }
});

// Ajout en BDD
$(document).on("click", ".addBtn", function() {

    $.post("series_json_importer.php", {
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
</script>

</body>
</html>
