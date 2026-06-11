<?php
session_start();

$config = require __DIR__ . '/../app/Config/config.php';

$route = $_GET['route'] ?? 'home';

switch ($route) {

    /* ============================================================
       GESTION > ÉDITEURS
    ============================================================ */

    case 'gestion_editeurs_importer':
    case 'gestion_editeurs_importer_add':
    case 'gestion_editeurs_importer_info':
    case 'gestion_editeurs_gerer':
    case 'gestion_editeurs_toggle':
    case 'gestion_editeurs_edit':
    case 'gestion_editeurs_update':
        require_once __DIR__ . '/../app/controllers/PublisherController.php';
        $ctrl = new PublisherController();
        $method = str_replace('gestion_editeurs_', '', $route);
        $ctrl->$method();
        break;


    /* ============================================================
       GESTION > SERIES
    ============================================================ */

    case 'gestion_series_importer':
    case 'gestion_series_preview':
    case 'gestion_series_ajax_add':
    case 'gestion_series_ajax_info':
    case 'gestion_series_gerer':
    case 'gestion_series_edit':
    case 'gestion_series_update':
    case 'gestion_series_delete':
        require_once __DIR__ . '/../app/controllers/SeriesController.php';
        $ctrl = new SeriesController();
        $method = str_replace('gestion_series_', '', $route);
        $ctrl->$method();
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
    case 'gestion_issues_preview':
    case 'gestion_issues_importer_save':
    case 'gestion_issues_gerer':
    case 'gestion_issues_edit':
    case 'gestion_issues_update':
    case 'gestion_issues_delete':
        require_once __DIR__ . '/../app/controllers/IssuesController.php';
        $ctrl = new IssuesController();
        $method = str_replace('gestion_issues_', '', $route);
        $ctrl->$method();
        break;


    /* ============================================================
       COMICVINE — COVERS HD
    ============================================================ */

    case 'comicvine_search_issue':
    case 'comicvine_download_cover':
        require_once __DIR__ . '/../app/controllers/ComicVineCoverController.php';
        $ctrl = new ComicVineCoverController();
        $method = str_replace('comicvine_', '', $route);
        $ctrl->$method();
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
