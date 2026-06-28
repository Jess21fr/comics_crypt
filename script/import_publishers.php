<?php
/**
 * Comics Crypt - Script d'auto-matching des éditeurs Comics.org (Version debug & boostée)
 */

// 1. FORCER L'AFFICHAGE DES ERREURS (Pour tuer la page blanche)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. BOOSTER LES CAPACITÉS DE PHP (Pour encaisser le gros JSON)
ini_set('memory_limit', '512M'); // Augmente la RAM allouée au script
set_time_limit(300);             // Laisse 5 minutes au script pour tourner

// 3. Connexion à la base de données (Pense à vérifier tes identifiants ici !)
$host = 'localhost';
$dbname = 'comics_crypt';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("❌ Erreur de connexion à la BDD : " . $e->getMessage());
}

// 4. Chemin vers ton fichier JSON Comics.org
$jsonPath = __DIR__ . '/../public/json/publishers_comicsorg.json'; 

if (!file_exists($jsonPath)) {
    die("❌ Fichier JSON introuvable à l'emplacement : " . realpath($jsonPath));
}

echo "📢 Lecture du fichier JSON en cours (cela peut prendre quelques secondes)...<br>";
$jsonData = file_get_contents($jsonPath);

echo "📢 Décodage du JSON...<br>";
$publishersFromJson = json_decode($jsonData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("❌ Erreur lors du décodage du JSON : " . json_last_error_msg());
}

$totalJson = count($publishersFromJson);
echo "📢 Analyse de <strong>$totalJson</strong> éditeurs commencée...<br><br>";

// 5. Préparation des requêtes SQL
$stmtCheck = $pdo->prepare("SELECT id, name, comicsorg_id FROM publishers WHERE name = :name");
$stmtUpdate = $pdo->prepare("UPDATE publishers SET comicsorg_id = :comicsorg_id WHERE id = :id");

$matchedCount = 0;
$alreadyLinkedCount = 0;
$noMatchCount = 0;

// Utilisation d'une transaction pour accélérer grandement les écritures en BDD
$pdo->beginTransaction();

foreach ($publishersFromJson as $jsonPub) {
    $gcdId = $jsonPub['id'];
    $gcdName = isset($jsonPub['name']) ? trim($jsonPub['name']) : '';

    if (empty($gcdName)) {
        continue;
    }

    // Recherche de l'éditeur local par son nom exact
    $stmtCheck->execute(['name' => $gcdName]);
    $localPub = $stmtCheck->fetch();

    if ($localPub) {
        if (!empty($localPub['comicsorg_id'])) {
            $alreadyLinkedCount++;
        } else {
            $stmtUpdate->execute([
                'comicsorg_id' => $gcdId,
                'id' => $localPub['id']
            ]);
            echo "✅ Match : [{$localPub['name']}] reçoit l'ID Comics.org <strong>$gcdId</strong><br>";
            $matchedCount++;
        }
    } else {
        $noMatchCount++;
    }
}

// Validation de toutes les modifications d'un coup
$pdo->commit();

// 6. Rapport final
echo "<br>=== 📊 RAPPORT D'IMPORTATION ===";
echo "<br>🔹 Nouvelles associations réussies : " . $matchedCount;
echo "<br>🔹 Éditeurs déjà associés auparavant : " . $alreadyLinkedCount;
echo "<br>🔹 Éditeurs du JSON sans correspondance locale : " . $noMatchCount;
echo "<br>================================<br>";
echo "🏁 Fin du script avec succès !";