<?php
$config = require __DIR__ . '/../../Config/config.php';
$base = rtrim($config['base_url'], '/');
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm fixed-top">
    <div class="container-fluid">

        <a class="navbar-brand d-flex align-items-center" href="<?= $base ?>/index.php?route=home">
            <img src="<?= $base ?>/assets/img/comics_crypt_text_logo.png"
                 alt="ComicsCrypt"
                 height="50"
                 class="me-2">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainMenu">

            <ul class="navbar-nav me-auto">

                <!-- GESTION -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle menu-title" href="#" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-gears"></i> Gestion
                    </a>

                    <ul class="dropdown-menu dropdown-menu-dark">

                        <!-- ÉDITEURS -->
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Éditeurs</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="<?= $base ?>/index.php?route=gestion_editeurs_importer">Importer</a></li>
                                <li><a class="dropdown-item" href="<?= $base ?>/index.php?route=gestion_editeurs_gerer">Gérer</a></li>
                            </ul>
                        </li>

                        <!-- SÉRIES -->
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Séries</a>
                            <ul class="dropdown-menu dropdown-menu-dark">

                                <li>
                                    <a class="dropdown-item"
                                    href="<?= $base ?>/index.php?route=gestion_series_importer">
                                        Importer
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item"
                                    href="<?= $base ?>/index.php?route=gestion_series_gerer">
                                        Gérer
                                    </a>
                                </li>

                            </ul>
                        </li>

                        <!-- GAMMES -->
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Gammes</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="<?= $base ?>/index.php?route=gestion_gammes_importer">Importer</a></li>
                                <li><a class="dropdown-item" href="<?= $base ?>/index.php?route=gestion_gammes_gerer">Gérer</a></li>
                            </ul>
                        </li>

                        <!-- UNIVERS -->
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Univers</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="<?= $base ?>/index.php?route=gestion_univers_creer">Créer</a></li>
                                <li><a class="dropdown-item" href="<?= $base ?>/index.php?route=gestion_univers_affecter_series">Affecter des séries</a></li>
                            </ul>
                        </li>

                        <!-- ÉPISODES -->
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Épisodes</a>
                            <ul class="dropdown-menu dropdown-menu-dark">

                                <li>
                                    <a class="dropdown-item"
                                       href="<?= $base ?>/index.php?route=gestion_issues_importer">
                                        Importer
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item"
                                       href="<?= $base ?>/index.php?route=gestion_issues_gerer">
                                        Gérer
                                    </a>
                                </li>

                                <!-- ⭐ NOUVELLE ENTRÉE : IMPORTER LES COVERS -->
                                <li>
                                    <a class="dropdown-item"
                                       href="<?= $base ?>/index.php?route=gestion_issues_import_covers">
                                        Importer les covers
                                    </a>
                                </li>

                            </ul>
                        </li>

                    </ul>
                </li>

            </ul>

        </div>
    </div>
</nav>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.dropdown-submenu > a').forEach(function(element){
        element.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            let submenu = this.nextElementSibling;

            this.closest('.dropdown-menu').querySelectorAll('.dropdown-menu').forEach(function(menu){
                if(menu !== submenu){
                    menu.classList.remove('show');
                }
            });

            submenu.classList.toggle('show');
        });
    });
});
</script>
