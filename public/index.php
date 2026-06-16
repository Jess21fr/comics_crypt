<?php
session_start();

$config = require __DIR__ . '/../app/Config/config.php';

$route = $_GET['route'] ?? 'home';

    /* ============================================================
    AUTOLOAD DES CONTROLLERS
    ============================================================ */
    function loadController($name) {
        require_once __DIR__ . "/../app/controllers/{$name}.php";
    }

    /* ============================================================
    ROUTAGE
    ============================================================ */

    switch ($route) {

    /* ============================================================
    ROUTE TECHNIQUE : API RATE LIMITER (AJAX)
    ============================================================ */

    case 'get_limiter_status':
        header('Content-Type: application/json');
        try {
            // Initialisation locale de la connexion PDO si elle n'est pas globale
            if (!isset($db)) {
                $dsn = "mysql:host=" . ($config['db_host'] ?? 'localhost') . ";dbname=" . ($config['db_name'] ?? 'comics_crypt') . ";charset=utf8mb4";
                $db = new PDO($dsn, $config['db_user'] ?? 'root', $config['db_pass'] ?? '', [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            }

            // 1. Nombre total de requêtes sur 1 heure glissante
            $stmtTotal = $db->query("
                SELECT COUNT(*) 
                FROM api_requests 
                WHERE created_at >= (NOW() - INTERVAL 1 HOUR)
            ");
            $totalPerHour = (int)$stmtTotal->fetchColumn();

            // 2. Détail par endpoint pour alimenter la modale
            $stmtDetail = $db->query("
                SELECT endpoint, COUNT(*) as used 
                FROM api_requests 
                WHERE created_at >= (NOW() - INTERVAL 1 HOUR)
                GROUP BY endpoint
            ");
            $details = $stmtDetail->fetchAll();

            echo json_encode([
                'success' => true,
                'total_used' => $totalPerHour,
                'limit' => 200,
                'details' => $details
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;


    /* ============================================================
    GESTION > ÉDITEURS (ComicVine ONLY)
    ============================================================ */

    case 'gestion_editeurs_importer':        // Page importer (import ComicVine)
        loadController('PublishersController');
        (new PublishersController())->index();
        break;

    case 'gestion_editeurs_search':          // Recherche ComicVine
        loadController('PublishersController');
        (new PublishersController())->search();
        break;

    case 'gestion_editeurs_import':          // Importer un éditeur ComicVine
        loadController('PublishersController');
        (new PublishersController())->import();
        break;

    case 'gestion_editeurs_gerer':           // Page DataTable (liste + modifier)
        loadController('PublishersController');
        (new PublishersController())->gerer();
        break;

    case 'gestion_editeurs_update':          // Update depuis la modale
        loadController('PublishersController');
        (new PublishersController())->update();
        break;

    case 'gestion_editeurs_toggle':          // Activer / désactiver
        loadController('PublishersController');
        (new PublishersController())->toggle();
        break;

    case 'gestion_editeurs_sync':            // (optionnel, placeholder)
        loadController('PublishersController');
        (new PublishersController())->sync();
        break;


    /* ============================================================
       GESTION > SERIES (refonte ComicVine à venir)
    ============================================================ */

    case 'gestion_series_importer':
    case 'gestion_series_search':
    case 'gestion_series_import':
    case 'gestion_series_sync':
    case 'gestion_series_gerer':
    case 'gestion_series_edit':
    case 'gestion_series_update':
    case 'gestion_series_delete':
        loadController('SeriesController');
        $method = str_replace('gestion_series_', '', $route);
        (new SeriesController())->$method();
        break;



    /* ============================================================
       GESTION > GAMMES (placeholders)
    ============================================================ */

    case 'gestion_gammes_importer':
        echo "<h1 class='text-light p-5'>Import Gammes — en construction</h1>";
        break;

    case 'gestion_gammes_gerer':
        echo "<h1 class='text-light p-5'>Gérer Gammes — en construction</h1>";
        break;



    /* ============================================================
       GESTION > UNIVERS (placeholders)
    ============================================================ */

    case 'gestion_univers_creer':
        echo "<h1 class='text-light p-5'>Créer Univers — en construction</h1>";
        break;

    case 'gestion_univers_affecter_series':
        echo "<h1 class='text-light p-5'>Affecter Séries — en construction</h1>";
        break;

    /* ============================================================
       GESTION > ÉPISODES (ISSUES)
    ============================================================ */

    case 'gestion_issues_importer': 
        require_once __DIR__ . '/../app/controllers/IssuesController.php';
        (new IssuesController())->importer();
        break;

    case 'gestion_issues_search':
        require_once __DIR__ . '/../app/controllers/IssuesController.php';
        (new IssuesController())->search();
        break;

    case 'gestion_issues_import':
        require_once __DIR__ . '/../app/controllers/IssuesController.php';
        (new IssuesController())->import();
        break;

    // CORRIGÉ ICI : Ajout du ../ pour remonter au bon dossier
    case 'gestion_issues_list_local':
        require_once __DIR__ . '/../app/controllers/IssuesController.php';
        $controller = new IssuesController();
        $controller->listLocal();
        break;

    // CORRIGÉ ICI : Ajout du ../ pour remonter au bon dossier
    case 'gestion_issues_update':
        require_once __DIR__ . '/../app/controllers/IssuesController.php';
        $controller = new IssuesController();
        $controller->updateSingle();
        break;

    /* ============================================================
       COMICVINE — COVERS HD (RESTE COMPATIBLE)
    ============================================================ */

    case 'comicvine_search_issue':
    case 'comicvine_download_cover':
        loadController('ComicVineCoverController');
        $method = str_replace('comicvine_', '', $route);
        (new ComicVineCoverController())->$method();
        break;

    /* ============================================================
       PAGE D’ACCUEIL
    ============================================================ */

    default:
        require __DIR__ . '/../app/views/layouts/header.php';
        require __DIR__ . '/../app/views/layouts/menu.php';
        ?>
        <div class="homepage-bg">
            <div class="landing-wrapper">
                <div class="landing-content">
                    <h1>Bienvenue dans COMICSCRYPT</h1>
                </div>
            </div>
        </div>
        <?php
        require __DIR__ . '/../app/views/layouts/footer.php';
        break;
}