<?php
session_start();

$config = require __DIR__ . '/../app/Config/config.php';

$route = $_GET['route'] ?? 'home';

switch ($route) {

    /* -----------------------------------------
       GESTION > ÉDITEURS > IMPORTER
    ----------------------------------------- */
    case 'gestion_editeurs_importer':
        require_once __DIR__ . '/../app/controllers/PublisherController.php';
        (new PublisherController())->importer();
        break;

    case 'gestion_editeurs_importer_add':
        require_once __DIR__ . '/../app/controllers/PublisherController.php';
        (new PublisherController())->ajaxAdd();
        break;

    case 'gestion_editeurs_importer_info':
        require_once __DIR__ . '/../app/controllers/PublisherController.php';
        (new PublisherController())->ajaxInfo();
        break;


    /* -----------------------------------------
       GESTION > ÉDITEURS > GÉRER
    ----------------------------------------- */
    case 'gestion_editeurs_gerer':
        require_once __DIR__ . '/../app/controllers/PublisherController.php';
        (new PublisherController())->gerer();
        break;

    case 'gestion_editeurs_toggle':
        require_once __DIR__ . '/../app/controllers/PublisherController.php';
        (new PublisherController())->toggle();
        break;

    case 'gestion_editeurs_edit':
        require_once __DIR__ . '/../app/controllers/PublisherController.php';
        (new PublisherController())->edit();
        break;

    case 'gestion_editeurs_update':
        require_once __DIR__ . '/../app/controllers/PublisherController.php';
        (new PublisherController())->update();
        break;


    /* ============================
       GESTION > SERIES
    ============================ */

    case 'gestion_series_importer':
        require_once __DIR__ . '/../app/controllers/SeriesController.php';
        (new SeriesController())->importer();
        break;

    case 'gestion_series_preview':
        require_once __DIR__ . '/../app/controllers/SeriesController.php';
        (new SeriesController())->preview();
        break;

    case 'gestion_series_ajax_add':
        require_once __DIR__ . '/../app/controllers/SeriesController.php';
        (new SeriesController())->ajaxAdd();
        break;

    case 'gestion_series_ajax_info':
        require_once __DIR__ . '/../app/controllers/SeriesController.php';
        (new SeriesController())->ajaxInfo();
        break;

    case 'gestion_series_gerer':
        require_once __DIR__ . '/../app/controllers/SeriesController.php';
        (new SeriesController())->gerer();
        break;

    case 'gestion_series_edit':
        require_once __DIR__ . '/../app/controllers/SeriesController.php';
        (new SeriesController())->edit();
        break;

    case 'gestion_series_update':
        require_once __DIR__ . '/../app/controllers/SeriesController.php';
        (new SeriesController())->update();
        break;

    case 'gestion_series_delete':
        require_once __DIR__ . '/../app/controllers/SeriesController.php';
        (new SeriesController())->delete();
        break;


    /* -----------------------------------------
       GESTION > GAMMES (placeholders)
    ----------------------------------------- */
    case 'gestion_gammes_importer':
        echo "<h1 class='text-light p-5'>Import Gammes — en construction</h1>";
        break;

    case 'gestion_gammes_gerer':
        echo "<h1 class='text-light p-5'>Gérer Gammes — en construction</h1>";
        break;


    /* -----------------------------------------
       GESTION > UNIVERS (placeholders)
    ----------------------------------------- */
    case 'gestion_univers_creer':
        echo "<h1 class='text-light p-5'>Créer Univers — en construction</h1>";
        break;

    case 'gestion_univers_affecter_series':
        echo "<h1 class='text-light p-5'>Affecter Séries — en construction</h1>";
        break;


    /* -----------------------------------------
       GESTION > ÉPISODES (ISSUES)
    ----------------------------------------- */

    /* Importer les issues */
    case 'gestion_issues_importer':
        require_once __DIR__ . '/../app/controllers/IssuesController.php';
        (new IssuesController())->importer();
        break;

    case 'gestion_issues_preview':
        require_once __DIR__ . '/../app/controllers/IssuesController.php';
        (new IssuesController())->preview();
        break;

    case 'gestion_issues_add':
        require_once __DIR__ . '/../app/controllers/IssuesController.php';
        (new IssuesController())->ajaxAdd();
        break;

    case 'gestion_issues_info':
        require_once __DIR__ . '/../app/controllers/IssuesController.php';
        (new IssuesController())->ajaxInfo();
        break;

    /* Gérer les issues */
    case 'gestion_issues_gerer':
        require_once __DIR__ . '/../app/controllers/IssuesController.php';
        (new IssuesController())->gerer();
        break;

    case 'gestion_issues_edit':
        require_once __DIR__ . '/../app/controllers/IssuesController.php';
        (new IssuesController())->edit();
        break;

    case 'gestion_issues_update':
        require_once __DIR__ . '/../app/controllers/IssuesController.php';
        (new IssuesController())->update();
        break;

    case 'gestion_issues_delete':
        require_once __DIR__ . '/../app/controllers/IssuesController.php';
        (new IssuesController())->delete();
        break;


    /* -----------------------------------------
       GESTION > ÉPISODES > IMPORTER LES COVERS
    ----------------------------------------- */

    case 'gestion_issues_import_covers':
        require_once __DIR__ . '/../app/controllers/IssuesController.php';
        (new IssuesController())->importerCovers();
        break;

    case 'gestion_issues_add_cover':
        require_once __DIR__ . '/../app/controllers/IssuesController.php';
        (new IssuesController())->ajaxAddCover();
        break;


    /* -----------------------------------------
       PAGE D’ACCUEIL
    ----------------------------------------- */
    default:
        require __DIR__ . '/../app/views/layouts/header.php';
        require __DIR__ . '/../app/views/layouts/menu.php';
        ?>
        <div class="homepage-bg">
            <div class="landing-wrapper">
                <div class="landing-content">
                    <h1>COMICSCRYPT BACK‑OFFICE</h1>
                    <p>Bienvenue dans votre espace d’administration</p>
                </div>
            </div>
        </div>
        <?php
        require __DIR__ . '/../app/views/layouts/footer.php';
        break;
}
