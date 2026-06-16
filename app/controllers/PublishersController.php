<?php

require_once __DIR__ . '/../models/Publishers.php';
require_once __DIR__ . '/../services/ComicVineApi.php';

class PublishersController
{
    /**
     * Affiche la vue de l'importateur d'éditeurs
     */
    public function index()
    {
        require __DIR__ . '/../views/publishers/importer.php';
    }

    /**
     * RECHERCHE COMICVINE (Sécurisée par le RateLimiter & Flux JSON Direct)
     */
    public function search()
    {
        // 1. On force le header JSON pur pour le Front-end
        header('Content-Type: application/json; charset=utf-8');

        $name = isset($_GET['name']) ? trim($_GET['name']) : '';
        if (!$name) {
            echo json_encode(['success' => false, 'message' => "Nom de l'éditeur manquant"]);
            exit;
        }

        try {
            // Récupération de la configuration globale
            $config = require __DIR__ . '/../config/config.php';

            // Connexion sécurisée à la base de données
            $db = new PDO(
                "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset={$config['db']['charset']}",
                $config['db']['user'],
                $config['db']['pass'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );

            // Récupération de la clé API ComicVine dans la config (gère deux syntaxes courantes)
            $apiKey = $config['comicvine']['api_key'] ?? $config['comicvine_api_key'] ?? '';
            
            // Construction de l'URL officielle au format "json" natif
            $url = "https://comicvine.gamespot.com/api/publishers/?api_key={$apiKey}&filter=name:" . urlencode($name) . "&format=json";

            // Inclusion du Helper de limitation de requêtes
            require_once __DIR__ . '/../helpers/ApiRateLimiter.php';

            // Exécution de l'appel via le limiter (vérifie l'intervalle de 2s et pose le verrou)
            $data = ApiRateLimiter::performRequest($db, 'search', $url);

            // Vérification des erreurs renvoyées par l'API ComicVine (ex: clé API invalide)
            if (isset($data['status_code']) && $data['status_code'] !== 1) {
                $errorMsg = $data['error'] ?? 'Erreur inconnue provenant de l’API ComicVine.';
                echo json_encode(['success' => false, 'message' => $errorMsg]);
                exit;
            }

            $results = $data['results'] ?? [];

            // Plus besoin de fichier temporaire physique ! On renvoie les résultats directement en JSON
            echo json_encode([
                'success' => true,
                'results' => $results
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            exit;

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => "Erreur lors de la recherche : " . $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * IMPORTER UN ÉDITEUR DANS LA BDD LOCALE
     */
    public function import()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_POST['publisher_id']) || empty($_POST['name'])) {
            echo json_encode(['success' => false, 'message' => "Identifiant (publisher_id) ou nom manquant"]);
            exit;
        }

        try {
            $model = new Publishers();
            $publisherId = (int)$_POST['publisher_id'];
            $name = trim($_POST['name']);

            // Vérification des doublons
            if ($model->existsByPublisherId($publisherId)) {
                echo json_encode(['success' => false, 'message' => "Cet éditeur existe déjà dans votre base locale."]);
                exit;
            }

            $logoUrl = $_POST['image_super_url'] ?? null;
            $logoFilename = null;

            // Traitement et rapatriement local du logo de l'éditeur
            if ($logoUrl) {
                $ext = pathinfo(parse_url($logoUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
                if (!$ext) { $ext = "jpg"; }

                $logoFilename = "publisher_" . $publisherId . "." . $ext;
                
                $logoDir = __DIR__ . "/../../public/logos/";
                if (!is_dir($logoDir)) {
                    mkdir($logoDir, 0755, true);
                }
                
                $logoPath = $logoDir . $logoFilename;

                // Téléchargement sécurisé du flux d'image
                $img = @file_get_contents($logoUrl);
                if ($img) {
                    file_put_contents($logoPath, $img);
                } else {
                    $logoFilename = null; // Repli si l'image distante est inaccessible
                }
            }

            $payload = [
                'publisher_id' => $publisherId,
                'name'         => $name,
                'logo'         => $logoFilename
            ];

            $newId = $model->insertComicVine($payload);

            echo json_encode(['success' => true, 'id' => $newId]);
            exit;

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => "Erreur d'importation : " . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Liste et affiche la page de gestion des éditeurs locaux
     */
    public function gerer()
    {
        $model = new Publishers();
        $publishers = $model->getAll();

        require __DIR__ . '/../views/publishers/gerer.php';
    }

    /**
     * METTRE À JOUR UN ÉDITEUR (Données + Nouveau Logo)
     */
    public function update()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_POST['id']) || empty($_POST['name'])) {
            echo json_encode(['success' => false, 'message' => "Champs obligatoires manquants"]);
            exit;
        }

        try {
            $id    = (int)$_POST['id'];
            $name  = trim($_POST['name']);
            $actif = isset($_POST['actif']) ? (int)$_POST['actif'] : 1;

            $model = new Publishers();
            $publisher = $model->getById($id);

            if (!$publisher) {
                echo json_encode(['success' => false, 'message' => "Éditeur introuvable en base de données"]);
                exit;
            }

            $logoFilename = $publisher['logo'];

            // Traitement robuste de l'upload du nouveau logo
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                if (!$ext) { $ext = "jpg"; }

                $logoFilename = "publisher_" . $publisher['publisher_id'] . "." . $ext;
                
                $logoDir = __DIR__ . "/../../public/logos/";
                if (!is_dir($logoDir)) {
                    mkdir($logoDir, 0755, true);
                }
                
                $logoPath = $logoDir . $logoFilename;

                if (!move_uploaded_file($_FILES['logo']['tmp_name'], $logoPath)) {
                    throw new Exception("Le déplacement du fichier uploadé a échoué.");
                }
            }

            // Utilisation de la méthode encapsulée du Modèle
            $model->update($id, $name, $logoFilename, $actif);

            echo json_encode(['success' => true]);
            exit;

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => "Erreur de mise à jour : " . $e->getMessage()]);
            exit;
        }
    }

    /**
     * ACTIVE OU DÉSACTIVE UN ÉDITEUR (Toggle asynchrone)
     */
    public function toggle()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = $_POST['id'] ?? null;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => "ID de l'éditeur manquant"]);
            exit;
        }

        try {
            $model = new Publishers();
            $newState = $model->toggleActif((int)$id);

            echo json_encode([
                'success' => true,
                'message' => $newState ? "Éditeur activé avec succès" : "Éditeur désactivé avec succès"
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => "Erreur de bascule d'état : " . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Synchronisation manuelle (Désactivée temporairement)
     */
    public function sync()
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => "La synchronisation globale est actuellement désactivée"]);
        exit;
    }
}