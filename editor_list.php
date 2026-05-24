<?php

set_time_limit(0);

// ------------------------------------------------------------
// Connexion MySQL
// ------------------------------------------------------------
$pdo = new PDO(
    "mysql:host=localhost;dbname=comics_crypt;charset=utf8mb4",
    "root",
    "",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// ------------------------------------------------------------
// Fonction utilitaire API
// ------------------------------------------------------------
function api_get_json(string $url): ?array {
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

    // Gestion du throttling
    if ($code == 429) {
        echo "<p style='color:red;'>Throttling détecté. Pause 60 secondes...</p>";
        sleep(60);
        return api_get_json($url);
    }

    if ($code !== 200 || !$response) return null;

    return json_decode($response, true);
}

// ------------------------------------------------------------
// Préparation de l'insertion SQL
// ------------------------------------------------------------
$insert = $pdo->prepare("
    INSERT INTO publishers (name, country, publisher_id)
    VALUES (:name, :country, :publisher_id)
    ON DUPLICATE KEY UPDATE name = VALUES(name), country = VALUES(country)
");

// ------------------------------------------------------------
// Lecture du fichier de progression
// ------------------------------------------------------------
$progressFile = "progress.txt";

if (file_exists($progressFile)) {
    $startPage = (int)trim(file_get_contents($progressFile));
} else {
    $startPage = 1;
}

echo "<p>Reprise à la page <strong>$startPage</strong></p>";

$baseUrl = "https://www.comics.org/api/publisher/?format=json";

// ------------------------------------------------------------
// Import par batch de 10 pages
// ------------------------------------------------------------
$batchSize = 10;
$currentPage = $startPage;

while (true) {

    for ($i = 0; $i < $batchSize; $i++) {

        $url = $baseUrl . "&page=" . $currentPage;
        $json = api_get_json($url);

        if (!$json || empty($json["results"])) {
            echo "<p>Page $currentPage vide ou inaccessible.</p>";
            file_put_contents($progressFile, $currentPage + 1);
            exit("<h3>Fin du batch (page vide)</h3>");
        }

        foreach ($json["results"] as $pub) {

            // Extraction du publisher_id
            if (!preg_match('~/publisher/(\d+)/~', $pub["api_url"], $m)) {
                continue;
            }

            $publisherId = (int)$m[1];

            $insert->execute([
                ":name"         => $pub["name"],
                ":country"      => $pub["country"] ?? null,
                ":publisher_id" => $publisherId
            ]);
        }

        echo "<p>Page $currentPage importée.</p>";

        // Sauvegarde de la progression
        file_put_contents($progressFile, $currentPage);

        $currentPage++;

        // Petite pause anti-throttle
        sleep(1);
    }

    // Pause entre les batches
    echo "<p style='color:blue;'>Pause 180 secondes...</p>";
    sleep(180);
}

?>