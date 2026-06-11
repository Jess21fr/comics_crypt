<?php

// ============================================================
// Script : fill_comicvine_volume_id.php
// Objectif : remplir la colonne comicvine_volume_id dans la table series
// ============================================================

$config = require __DIR__ . '/../config.php';

// Connexion DB
$pdo = new PDO(
    "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset=utf8mb4",
    $config['db']['user'],
    $config['db']['pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$apiKey  = $config['comicvine_api_key'];
$baseUrl = $config['comicvine_base_url'];

// Récupération des séries sans volume_id
$sql = "SELECT id, name, year_began, publisher_name FROM series WHERE comicvine_volume_id IS NULL";
$stmt = $pdo->query($sql);
$series = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "=== Début du remplissage comicvine_volume_id ===\n\n";

foreach ($series as $s) {

    $name      = $s['name'];
    $year      = $s['year_began'];
    $publisher = $s['publisher_name'];

    echo "→ Série : {$name} ({$year}) [{$publisher}]\n";

    // 1) Appel ComicVine
    $url = $baseUrl
        . "/search/?api_key={$apiKey}&format=json"
        . "&query=" . urlencode($name)
        . "&resources=volume";

    $json = curlGet($url);

    if (!$json || empty($json['results'])) {
        echo "   ❌ Aucun résultat ComicVine\n\n";
        continue;
    }

    $results = $json['results'];
    $best = null;

    // 2) Filtrage intelligent
    foreach ($results as $vol) {

        // Vérification année
        if (!empty($vol['start_year']) && intval($vol['start_year']) !== intval($year)) {
            continue;
        }

        // Vérification éditeur
        if ($publisher && isset($vol['publisher']['name'])) {
            $cvPub = strtolower(trim($vol['publisher']['name']));
            $myPub = strtolower(trim($publisher));

            if ($cvPub !== $myPub) {
                continue;
            }
        }

        // Vérification nom (similarité)
        $cvName = strtolower(trim($vol['name']));
        $myName = strtolower(trim($name));

        similar_text($cvName, $myName, $percent);

        if ($percent < 70) {
            continue;
        }

        // OK
        $best = $vol;
        break;
    }

    if (!$best) {
        echo "   ❌ Aucun volume correspondant (nom/année/éditeur)\n\n";
        continue;
    }

    // 3) Mise à jour DB
    $volumeId = $best['id'];

    $update = $pdo->prepare("UPDATE series SET comicvine_volume_id = ? WHERE id = ?");
    $update->execute([$volumeId, $s['id']]);

    echo "   ✔ Volume trouvé : {$best['name']} ({$best['start_year']}) — ID = {$volumeId}\n\n";
}

echo "=== Terminé ===\n";


// ============================================================
// Fonction CURL GET
// ============================================================
function curlGet(string $url): ?array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_USERAGENT      => 'ComicsCrypt/1.0'
    ]);

    $response = curl_exec($ch);
    $err      = curl_error($ch);
    curl_close($ch);

    if ($err || !$response) {
        return null;
    }

    return json_decode($response, true);
}
