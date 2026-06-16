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
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle menu-title" href="#" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-gears"></i> Gestion
                    </a>

                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Éditeurs</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="<?= $base ?>/index.php?route=gestion_editeurs_importer">Importer</a></li>
                                <li><a class="dropdown-item" href="<?= $base ?>/index.php?route=gestion_editeurs_gerer">Gérer</a></li>
                            </ul>
                        </li>

                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Séries</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="<?= $base ?>/index.php?route=gestion_series_importer">Importer</a></li>
                                <li><a class="dropdown-item" href="<?= $base ?>/index.php?route=gestion_series_gerer">Gérer</a></li>
                            </ul>
                        </li>

                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Gammes</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="<?= $base ?>/index.php?route=gestion_gammes_importer">Importer</a></li>
                                <li><a class="dropdown-item" href="<?= $base ?>/index.php?route=gestion_gammes_gerer">Gérer</a></li>
                            </ul>
                        </li>

                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Univers</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="<?= $base ?>/index.php?route=gestion_univers_creer">Créer</a></li>
                                <li><a class="dropdown-item" href="<?= $base ?>/index.php?route=gestion_univers_affecter_series">Affecter des séries</a></li>
                            </ul>
                        </li>

                        <li class="dropdown-submenu">
                            <a class="dropdown-item dropdown-toggle" href="#">Épisodes</a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="<?= $base ?>/index.php?route=gestion_issues_importer">Importer</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>

            <div class="d-flex align-items-center ms-auto">
                <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#limiterModal" id="btnLimiterStatus">
                    <i class="fa-solid fa-gauge-high me-1"></i> API : <span id="navApiCounter">0</span> / 200 (1h)
                </button>
            </div>

        </div>
    </div>
</nav>

<div class="modal fade" id="limiterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-light border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-warning"><i class="fa-solid fa-server me-2"></i>Statut du Rate Limiter (ComicVine)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-secondary small">Analyse basée sur une heure glissante pour éviter les blocages de clé API.</p>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1 font-monospace small">
                        <span>Consommation globale :</span>
                        <span id="modalTotalText">0 / 200</span>
                    </div>
                    <div class="progress bg-secondary" style="height: 12px;">
                        <div id="modalProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>

                <h6 class="text-light border-bottom border-secondary pb-2 mb-2">Utilisation par Endpoint :</h6>
                <div id="endpointDetailsZone" class="font-monospace small">
                    </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary btn-sm" id="btnRefreshLimiter"><i class="fa-solid fa-rotate me-1"></i>Actualiser</button>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    // Gestion des sous-menus multi-niveaux existants
    document.querySelectorAll('.dropdown-submenu > a').forEach(function(element){
        element.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            let submenu = this.nextElementSibling;
            this.closest('.dropdown-menu').querySelectorAll('.dropdown-menu').forEach(function(menu){
                if(menu !== submenu){ menu.classList.remove('show'); }
            });
            submenu.classList.toggle('show');
        });
    });

    // 🔄 LOGIQUE AJAX DU RATE LIMITER
    const baseUrl = "<?= $base ?>";

    function updateLimiterDisplay() {
        $.getJSON(baseUrl + "/index.php?route=get_limiter_status", function(response) {
            if (!response.success) return;

            let total = response.total_used;
            let limit = response.limit;
            let percent = Math.min(100, (total / limit) * 100);

            // 1. Update de la Navbar
            $('#navApiCounter').text(total);
            
            // Ajustement dynamique de la couleur du bouton de la Navbar
            let btn = $('#btnLimiterStatus');
            btn.removeClass('btn-outline-success btn-outline-warning btn-outline-danger');
            if (percent < 50) btn.addClass('btn-outline-success');
            else if (percent < 80) btn.addClass('btn-outline-warning');
            else btn.addClass('btn-outline-danger');

            // 2. Update de la Modale
            $('#modalTotalText').text(`${total} / ${limit}`);
            $('#modalProgressBar').css('width', percent + '%');

            // Ajustement couleur progress-bar
            $('#modalProgressBar').removeClass('bg-success bg-warning bg-danger');
            if (percent < 50) $('#modalProgressBar').addClass('bg-success');
            else if (percent < 80) $('#modalProgressBar').addClass('bg-warning');
            else $('#modalProgressBar').addClass('bg-danger');

            // 3. Construction de la liste par Endpoint
            let html = "";
            if (!response.details || response.details.length === 0) {
                html = `<div class="text-muted italic py-2">Aucune requête enregistrée cette dernière heure.</div>`;
            } else {
                html = `<ul class="list-group list-group-flush">`;
                response.details.forEach(item => {
                    let endpointLimit = 200; // La limite par endpoint dans ApiRateLimiter est fixe à 200/h
                    html += `
                        <li class="list-group-item bg-dark text-light border-secondary d-flex justify-content-between align-items-center px-0">
                            <span><i class="fa-regular fa-folder-open text-info me-2"></i>/${item.endpoint}</span>
                            <span class="badge bg-secondary">${item.used} req.</span>
                        </li>
                    `;
                });
                html += `</ul>`;
            }
            $('#endpointDetailsZone').html(html);
        });
    }

    // Chargement initial au chargement de la page
    updateLimiterDisplay();

    // Re-calcul à l'ouverture de la modale
    $('#limiterModal').on('show.bs.modal', function() {
        updateLimiterDisplay();
    });

    // Bouton forcer l'actualisation
    $('#btnRefreshLimiter').on('click', function() {
        let btn = $(this);
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i>...');
        updateLimiterDisplay();
        setTimeout(() => btn.prop('disabled', false).html('<i class="fa-solid fa-rotate me-1"></i>Actualiser'), 600);
    });

    // Optionnel : Rafraîchissement automatique discret en tâche de fond toutes les 30 secondes
    setInterval(updateLimiterDisplay, 30000);
});
</script>