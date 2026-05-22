<?php

// ------------------------------------------------------------
// Fonction utilitaire pour appeler une URL et récupérer du JSON
// ------------------------------------------------------------
function api_get_json(string $url): ?array {
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_USERAGENT => "ComicsCrypt/1.0"
    ]);

    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code !== 200 || !$response) return null;

    return json_decode($response, true);
}

// ------------------------------------------------------------
// ROUTAGE SIMPLE PAR ÉTAPE
// ------------------------------------------------------------
$step = $_GET['step'] ?? 'search';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Comics Crypt – Recherche</title>
<style>
    body { font-family: Arial; padding: 20px; }
    input[type=text] { width: 300px; padding: 6px; margin-bottom: 10px; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 10px; }
    img { max-height: 250px; }
    a { text-decoration: none; color: #0066cc; }
</style>
</head>
<body>

<?php
// ============================================================================
// ÉTAPE 1 — FORMULAIRE + LISTE DES SÉRIES
// ============================================================================
if ($step === 'search'):

    $results = [];

    if (!empty($_POST)) {
        $editor = trim($_POST['editor']);
        $series = trim($_POST['series']);

        // Recherche large (publisher ne fonctionne pas dans l'API)
        $url = "https://www.comics.org/search/advanced/?format=json"
             . "&series=" . urlencode($series);

        $raw = api_get_json($url);

        // Filtrage manuel par éditeur
        if ($raw && !empty($raw['results'])) {
            foreach ($raw['results'] as $item) {
                if (stripos($item['publisher_name'], $editor) !== false) {
                    $results[] = $item;
                }
            }
        }
    }
?>

<h2>Recherche d'une série</h2>

<form method="post" action="?step=search">
    <label>Éditeur :</label><br>
    <input type="text" name="editor" value="<?= htmlspecialchars($_POST['editor'] ?? 'marvel') ?>"><br>

    <label>Série (mot-clé) :</label><br>
    <input type="text" name="series" value="<?= htmlspecialchars($_POST['series'] ?? '') ?>"><br>

    <button type="submit">Rechercher</button>
</form>

<?php if (!empty($_POST) && empty($results)): ?>
    <p>Aucune série trouvée.</p>
<?php endif; ?>

<?php if (!empty($results)): ?>

    <h3>Séries trouvées :</h3>
    <ul>
        <?php foreach ($results as $serie): ?>
            <li>
                <a href="?step=issues&series_id=<?= $serie['id'] ?>">
                    <?= htmlspecialchars($serie['name']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

<?php endif; ?>

<?php
// ============================================================================
// ÉTAPE 2 — LISTE DES ÉPISODES D’UNE SÉRIE
// ============================================================================
elseif ($step === 'issues'):

    $seriesId = $_GET['series_id'] ?? null;
    if (!$seriesId) die("ID série manquant");

    $url = "https://www.comics.org/api/series/{$seriesId}/issues/?format=json";
    $issues = api_get_json($url);
?>

<h2>Liste des épisodes</h2>

<p><a href="?step=search">← Retour</a></p>

<?php if ($issues && !empty($issues['issues'])): ?>

<table>
    <tr>
        <th>Numéro</th>
        <th>Date</th>
        <th>Détails</th>
    </tr>

    <?php foreach ($issues['issues'] as $issue): ?>
        <tr>
            <td><?= htmlspecialchars($issue['number']) ?></td>
            <td><?= htmlspecialchars($issue['publication_date']) ?></td>
            <td>
                <a href="?step=details&issue_id=<?= $issue['id'] ?>">Voir</a>
            </td>
        </tr>
    <?php endforeach; ?>

</table>

<?php else: ?>
    <p>Aucun épisode trouvé.</p>
<?php endif; ?>

<?php
// ============================================================================
// ÉTAPE 3 — FICHE COMPLÈTE D’UN ÉPISODE
// ============================================================================
elseif ($step === 'details'):

    $issueId = $_GET['issue_id'] ?? null;
    if (!$issueId) die("ID épisode manquant");

    $url = "https://www.comics.org/api/issue/{$issueId}/?format=json";
    $issue = api_get_json($url);
?>

<h2>Détails de l'épisode</h2>

<p><a href="?step=search">← Retour à la recherche</a></p>

<?php if ($issue): ?>

<p><strong>Série :</strong> <?= htmlspecialchars($issue['series']['name']) ?></p>
<p><strong>Numéro :</strong> <?= htmlspecialchars($issue['number']) ?></p>
<p><strong>Date :</strong> <?= htmlspecialchars($issue['publication_date']) ?></p>

<?php if (!empty($issue['cover']['thumb_url'])): ?>
    <img src="<?= $issue['cover']['thumb_url'] ?>" alt="Couverture">
<?php endif; ?>

<?php else: ?>
    <p>Impossible de récupérer les détails.</p>
<?php endif; ?>

<?php endif; ?>

</body>
</html>
