<?php
$config = require __DIR__ . '/../app/Config/config.php';

$route = $_GET['route'] ?? 'home';

switch ($route) {

    // IMPORT ÉDITEURS
    case 'gestion_editeurs_importer':
        require_once __DIR__ . '/../app/controllers/PublisherController.php';
        (new PublisherController())->importer();
        break;

    // AJAX : ajout éditeur
    case 'gestion_editeurs_importer_add':
        require_once __DIR__ . '/../app/controllers/PublisherController.php';
        (new PublisherController())->ajaxAdd();
        break;

    // AJAX : infos éditeur
    case 'gestion_editeurs_importer_info':
        require_once __DIR__ . '/../app/controllers/PublisherController.php';
        (new PublisherController())->ajaxInfo();
        break;

    // ACCUEIL
    default:
        require __DIR__ . '/../app/views/layouts/header.php';
        require __DIR__ . '/../app/views/layouts/menu.php';
        ?>
        <div class="container mt-5 pt-5 text-center text-light">
            <h1>COMICSCRYPT BACK‑OFFICE</h1>
            <p>Bienvenue dans votre espace d’administration</p>
        </div>
        <?php
        require __DIR__ . '/../app/views/layouts/footer.php';
        break;
}
