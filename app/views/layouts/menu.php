<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">

        <!-- LOGO -->
        <a class="navbar-brand d-flex align-items-center" href="/public/index.php">
            <img src="/public/assets/img/comics_crypt_text_logo.png" alt="ComicsCrypt" height="50" class="me-2">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainMenu">

            <ul class="navbar-nav me-auto">

                <!-- GESTION -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-gears"></i> Gestion
                    </a>

                    <ul class="dropdown-menu dropdown-menu-dark">

                        <!-- ÉDITEURS -->
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Éditeurs</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="/editeurs/list">Liste / Gestion</a></li>
                                <li><a class="dropdown-item" href="/editeurs/create">Créer</a></li>
                                <li><a class="dropdown-item" href="/editeurs/import">Importer</a></li>
                            </ul>
                        </li>

                        <!-- GAMMES -->
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Gammes</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="/gammes/list">Liste / Gestion</a></li>
                                <li><a class="dropdown-item" href="/gammes/create">Créer</a></li>
                                <li><a class="dropdown-item" href="/gammes/import">Importer</a></li>
                            </ul>
                        </li>

                        <!-- UNIVERS -->
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Univers</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="/univers/list">Liste / Gestion</a></li>
                                <li><a class="dropdown-item" href="/univers/create">Créer</a></li>
                                <li><a class="dropdown-item" href="/univers/import">Importer</a></li>
                            </ul>
                        </li>

                        <!-- SÉRIES -->
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Séries</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="/series/list">Liste / Gestion</a></li>
                                <li><a class="dropdown-item" href="/series/create">Créer</a></li>
                                <li><a class="dropdown-item" href="/series/import">Importer</a></li>
                            </ul>
                        </li>

                        <!-- ÉPISODES -->
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Épisodes</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="/episodes/list">Liste / Gestion</a></li>
                                <li><a class="dropdown-item" href="/episodes/create">Créer</a></li>
                                <li><a class="dropdown-item" href="/episodes/import">Importer</a></li>
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

            // Ferme les autres sous-menus
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
