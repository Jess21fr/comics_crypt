<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm fixed-top">
    <div class="container-fluid">

        <!-- LOGO -->
        <a class="navbar-brand d-flex align-items-center" href="/comics_crypt/public/index.php">
            <img src="/comics_crypt/public/assets/img/comics_crypt_text_logo.png"
                 alt="ComicsCrypt"
                 height="50"
                 class="me-2">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainMenu">

            <ul class="navbar-nav me-auto">

                <!-- ========================= -->
                <!-- GESTION                   -->
                <!-- ========================= -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle menu-title" href="#" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-gears"></i> Gestion
                    </a>

                    <ul class="dropdown-menu dropdown-menu-dark">

                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Éditeurs</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="#">Liste / Gestion</a></li>
                                <li><a class="dropdown-item" href="#">Créer</a></li>
                                <li><a class="dropdown-item" href="#">Importer</a></li>
                            </ul>
                        </li>

                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Gammes</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="#">Liste / Gestion</a></li>
                                <li><a class="dropdown-item" href="#">Créer</a></li>
                                <li><a class="dropdown-item" href="#">Importer</a></li>
                            </ul>
                        </li>

                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Univers</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="#">Liste / Gestion</a></li>
                                <li><a class="dropdown-item" href="#">Créer</a></li>
                                <li><a class="dropdown-item" href="#">Importer</a></li>
                            </ul>
                        </li>

                    </ul>
                </li>

                <!-- ========================= -->
                <!-- CHRONOLOGIE               -->
                <!-- ========================= -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle menu-title" href="#" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-timeline"></i> Chronologie
                    </a>

                    <ul class="dropdown-menu dropdown-menu-dark">

                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Par Univers</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="#">Marvel</a></li>
                                <li><a class="dropdown-item" href="#">DC</a></li>
                                <li><a class="dropdown-item" href="#">Indé</a></li>
                            </ul>
                        </li>

                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Par Série</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="#">Spider-Man</a></li>
                                <li><a class="dropdown-item" href="#">Batman</a></li>
                                <li><a class="dropdown-item" href="#">Spawn</a></li>
                            </ul>
                        </li>

                    </ul>
                </li>

                <!-- ========================= -->
                <!-- COLLECTION                -->
                <!-- ========================= -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle menu-title" href="#" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-books"></i> Collection
                    </a>

                    <ul class="dropdown-menu dropdown-menu-dark">

                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Mes Séries</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="#">Liste</a></li>
                                <li><a class="dropdown-item" href="#">Ajouter</a></li>
                                <li><a class="dropdown-item" href="#">Importer</a></li>
                            </ul>
                        </li>

                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Mes Épisodes</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="#">Liste</a></li>
                                <li><a class="dropdown-item" href="#">Ajouter</a></li>
                                <li><a class="dropdown-item" href="#">Importer</a></li>
                            </ul>
                        </li>

                    </ul>
                </li>

            </ul>

        </div>
    </div>
</nav>

<script>
// Sous-menus Bootstrap 5
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
