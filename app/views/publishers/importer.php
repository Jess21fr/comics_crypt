<?php
$config = require __DIR__ . '/../../Config/config.php';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/menu.php';
?>

<div class="container mt-5 pt-4">

    <h1 class="mb-4 text-light">Importer des Éditeurs (ComicVine)</h1>

    <div class="card bg-dark text-light mb-4">
        <div class="card-body">
            <label class="form-label">Nom de l’éditeur :</label>
            <div class="input-group">
                <input type="text" id="searchName" class="form-control" placeholder="Ex : Marvel, DC Comics...">
                <button id="btnSearch" class="btn btn-primary">Rechercher</button>
            </div>
        </div>
    </div>

    <div id="resultsZone"></div>

    <div id="importZone" class="mt-3" style="display:none;">
        <button id="btnSelectAll" class="btn btn-secondary me-2">Tout sélectionner</button>
        <button id="btnUnselectAll" class="btn btn-secondary me-2">Tout désélectionner</button>
        <button id="btnImportSelection" class="btn btn-success">Importer la sélection</button>
    </div>

</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<div id="toastMsg" 
     style="position:fixed;bottom:20px;right:20px;z-index:9999;
            background:#28a745;color:white;padding:12px 20px;
            border-radius:6px;display:none;font-weight:bold;">
</div>

<script>
function showToast(msg, color="#28a745") {
    let t = $('#toastMsg');
    t.css("background", color);
    t.text(msg).fadeIn(200);
    // On laisse le toast visible si c'est un message d'attente (bleu) ou d'erreur (rouge)
    if (color === "#28a745") {
        setTimeout(() => t.fadeOut(400), 3000);
    }
}

$(function() {
    let publishersTable = null;

    $('#btnSearch').on('click', function() {
        let name = $('#searchName').val().trim();
        if (!name) {
            showToast("Veuillez entrer un nom", "#c0392b");
            return;
        }

        $('#resultsZone').html("<div class='text-warning fw-bold'>Recherche en cours via l'API sécurisée...</div>");
        $('#importZone').hide();

        // 1. Appel du contrôleur PHP (C'est lui qui gère le RateLimiter et l'appel ComicVine)
        $.get("<?= $config['base_url'] ?>/index.php?route=gestion_editeurs_search&name=" + encodeURIComponent(name),
            function(response) {

                if (!response.success) {
                    $('#resultsZone').html("<div class='alert alert-danger'>" + response.message + "</div>");
                    return;
                }

                // 2. Ton PHP doit maintenant renvoyer directement le tableau 'results' reçu de ComicVine
                let cvResults = response.results;

                if (!cvResults || cvResults.length === 0) {
                    $('#resultsZone').html("<div class='alert alert-info'>Aucun éditeur trouvé.</div>");
                    return;
                }

                let html = `
                    <table id="publishersTable" class="table table-dark table-striped table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Logo</th>
                                <th>Nom</th>
                                <th>ID ComicVine</th>
                                <th class="text-center">✔</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                cvResults.forEach(pub => {
                    let icon = pub.image ? pub.image.icon_url : "";
                    
                    // Échappement sécurisé des caractères spéciaux pour le JSON dans l'attribut HTML
                    let safeJson = JSON.stringify(pub).replace(/'/g, "&apos;").replace(/"/g, "&quot;");

                    html += `
                        <tr data-pub="${safeJson}">
                            <td>
                                <img src="${icon}" style="width:50px;height:50px;object-fit:contain;"
                                     onerror="this.src='https://placehold.co/50x50/1a1a1a/666?text=No+Img'">
                            </td>
                            <td class="text-warning fw-bold">${pub.name}</td>
                            <td><code>${pub.id}</code></td>
                            <td class="text-center">
                                <input type="checkbox" class="cv-check">
                            </td>
                        </tr>
                    `;
                });

                html += "</tbody></table>";
                $('#resultsZone').html(html);

                // Initialisation propre de DataTables
                publishersTable = $('#publishersTable').DataTable({
                    pageLength: 20,
                    order: [[1, 'asc']],
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/fr-FR.json"
                    }
                });

                $('#importZone').show();
            },
            "json"
        ).fail(function() {
            $('#resultsZone').html("<div class='alert alert-danger'>Erreur serveur lors de la recherche.</div>");
        });
    });

    // Gestion globale des sélections (DataTables compatible)
    $('#btnSelectAll').on('click', function() {
        if (publishersTable) {
            publishersTable.$('.cv-check').prop('checked', true);
        }
    });
    
    $('#btnUnselectAll').on('click', function() {
        if (publishersTable) {
            publishersTable.$('.cv-check').prop('checked', false);
        }
    });

    // 3. Importation séquentielle asynchrone (Anti-crash XAMPP)
    $('#btnImportSelection').on('click', async function() {
        if (!publishersTable) return;

        let rows = [];
        
        // Utilisation de l'API DataTables pour scanner TOUTES les pages du tableau
        publishersTable.$('tr').each(function() {
            if ($(this).find('.cv-check').is(':checked')) {
                rows.push($(this).data('pub'));
            }
        });

        if (rows.length === 0) {
            showToast("Aucun éditeur sélectionné", "#c0392b");
            return;
        }

        // Désactivation du bouton pendant le traitement
        let btn = $(this);
        btn.prop('disabled', true).text("Importation en cours...");

        let total = rows.length;
        
        // Boucle "for...of" couplée avec un "await" : Traitement strict 1 par 1
        for (let i = 0; i < total; i++) {
            let pub = rows[i];
            
            showToast(`Importation de ${pub.name} (${i + 1} / ${total})...`, "#007bff");

            let payload = {
                publisher_id: pub.id,
                name: pub.name,
                image_super_url: pub.image?.super_url || ""
            };

            // On attend la réponse du serveur avant de passer à la ligne suivante
            await new Promise((resolve) => {
                $.post("<?= $config['base_url'] ?>/index.php?route=gestion_editeurs_import", payload)
                 .always(function() {
                     resolve(); // Passe à l'itération suivante quoi qu'il arrive
                 });
            });
        }

        // Réactivation de l'interface
        btn.prop('disabled', false).text("Importer la sélection");
        $('#toastMsg').fadeOut(200);
        showToast("Importation réussie avec succès !", "#28a745");
        
        // Optionnel : Décocher tout après l'import
        publishersTable.$('.cv-check').prop('checked', false);
    });

});
</script>

<?php
require __DIR__ . '/../layouts/footer.php';
?>