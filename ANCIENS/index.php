<?php

// ------------------------------------------------------------
// Fonction utilitaire pour appeler une URL et récupérer du JSON
// ------------------------------------------------------------
function api_get_json(string $url): ?array {
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_USERAGENT => "ComicsCrypt/1.0"
    ]);

    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code !== 200 || !$response) return null;

    return json_decode($response, true);
}

// ------------------------------------------------------------
// Convertir "September 2014" → "01/09/2014"
// ------------------------------------------------------------
function convertDateToFR($dateStr) {

    $months = [
        "January" => "01", "February" => "02", "March" => "03",
        "April" => "04", "May" => "05", "June" => "06",
        "July" => "07", "August" => "08", "September" => "09",
        "October" => "10", "November" => "11", "December" => "12"
    ];

    foreach ($months as $en => $num) {
        if (stripos($dateStr, $en) !== false) {
            $year = preg_replace('/[^0-9]/', '', $dateStr);
            return "01/" . $num . "/" . $year;
        }
    }

    return $dateStr;
}

// ------------------------------------------------------------
// Récupération des résultats
// ------------------------------------------------------------
$rows = [];
$error = null;

if (!empty($_POST)) {

    $serieName = trim($_POST['series']);
    $issueNum  = trim($_POST['issue']);

    $url = "https://www.comics.org/api/series/name/" . urlencode($serieName)
         . "/issue/" . urlencode($issueNum) . "/?format=json";

    $json = api_get_json($url);

    if (!$json || empty($json['results'])) {
        $error = "Aucun résultat trouvé.";
    } else {

        foreach ($json['results'] as $item) {

            // 1. Filtrer les variantes
            if (strpos($item['descriptor'], '[') !== false) {
                continue;
            }

            // 2. Récupérer le nombre total d’épisodes
            $serieJson = api_get_json($item['series']);
            $totalIssues = $serieJson['issue_count'] ?? '?';

            // 3. Récupérer la couverture
            $issueJson = api_get_json($item['api_url']);
            $cover = $issueJson['cover']['thumb_url'] ?? '';

            // 4. Convertir la date
            $dateFR = convertDateToFR($item['publication_date']);

            // 5. Ajouter la ligne
            $rows[] = [
                "series" => $item['series_name'],
                "number" => $item['descriptor'],
                "total"  => $totalIssues,
                "date"   => $dateFR,
                "cover"  => $cover
            ];
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Comics Crypt – Recherche</title>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- FancyTable -->
<script src="https://cdn.jsdelivr.net/npm/fancytable/dist/fancyTable.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fancytable/dist/fancyTable.min.css">

<style>
    body { font-family: Arial; padding: 20px; }
    input[type=text] { width: 300px; padding: 6px; margin-bottom: 10px; }
    table img { max-height: 120px; }
    button.add-btn { padding: 6px 12px; }
</style>

</head>
<body>

<h2>Recherche d'épisodes</h2>

<form method="post">
    <label>Nom de la série :</label><br>
    <input type="text" name="series" value="<?= htmlspecialchars($_POST['series'] ?? 'avengers') ?>"><br>

    <label>Numéro de l'épisode :</label><br>
    <input type="text" name="issue" value="<?= htmlspecialchars($_POST['issue'] ?? '1') ?>"><br>

    <button type="submit">Rechercher</button>
</form>

<?php if ($error): ?>
    <p style="color:red;"><?= $error ?></p>
<?php endif; ?>

<?php if (!empty($rows)): ?>

<h3>Résultats</h3>

<table id="issuesTable" class="fancyTable">
    <thead>
        <tr>
            <th>Nom de la série</th>
            <th>Numéro</th>
            <th>Date</th>
            <th>Couverture</th>
            <th>Ajouter</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['series']) ?></td>
            <td><?= htmlspecialchars($row['number']) ?> / <?= htmlspecialchars($row['total']) ?></td>
            <td><?= htmlspecialchars($row['date']) ?></td>
            <td>
                <?php if ($row['cover']): ?>
                    <img src="<?= $row['cover'] ?>">
                <?php endif; ?>
            </td>
            <td><button class="add-btn">Ajouter</button></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
$(document).ready(function() {
    $("#issuesTable").fancyTable({
        sortColumn: 0,
        pagination: true,
        perPage: 10,
        searchable: true
    });
});
</script>

<?php endif; ?>

</body>
</html>
