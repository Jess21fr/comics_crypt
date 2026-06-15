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
    setTimeout(() => t.fadeOut(400), 3000);
}

$(function() {

    $('#btnSearch').on('click', function() {
        let name = $('#searchName').val().trim();
        if (!name) {
            showToast("Veuillez entrer un nom", "#c0392b");
            return;
        }

        $('#resultsZone').html("<div class='text-warning'>Recherche en cours...</div>");

        $.get("<?= $config['base_url'] ?>/index.php?route=gestion_editeurs_search&name=" + encodeURIComponent(name),
            function(response) {

                if (!response.success) {
                    $('#resultsZone').html("<div class='alert alert-danger'>" + response.message + "</div>");
                    return;
                }

                let url = response.url;

                $.ajax({
                    url: url,
                    dataType: "jsonp",
                    jsonp: "json_callback",
                    success: function(data) {

                        if (!data.results || data.results.length === 0) {
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

                        data.results.forEach(pub => {

                            let icon = pub.image ? pub.image.icon_url : "";

                            html += `
                                <tr data-pub='${JSON.stringify(pub).replace(/'/g, "&apos;")}'>
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

                        $('#publishersTable').DataTable({
                            pageLength: 20,
                            order: [[1, 'asc']],
                            language: {
                                url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/fr-FR.json"
                            }
                        });

                        $('#importZone').show();
                    }
                });

            },
            "json"
        );
    });

    $('#btnSelectAll').on('click', () => $('.cv-check').prop('checked', true));
    $('#btnUnselectAll').on('click', () => $('.cv-check').prop('checked', false));

    $('#btnImportSelection').on('click', function() {

        let rows = [];
        $('#publishersTable tbody tr').each(function() {
            if ($(this).find('.cv-check').is(':checked')) {
                rows.push($(this).data('pub'));
            }
        });

        if (rows.length === 0) {
            showToast("Aucun éditeur sélectionné", "#c0392b");
            return;
        }

        let done = 0;

        rows.forEach(pub => {

            let payload = {
                publisher_id: pub.id,
                name: pub.name,
                image_super_url: pub.image?.super_url
            };

            $.post(
                "<?= $config['base_url'] ?>/index.php?route=gestion_editeurs_import",
                payload,
                function(response) {

                    done++;
                    if (done === rows.length) {
                        showToast("Import terminé !");
                    }
                },
                "json"
            );
        });

    });

});
</script>

<?php
require __DIR__ . '/../layouts/footer.php';
?>
