<?php
$config = require __DIR__ . '/../../Config/config.php';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/menu.php';
?>
<div class="container mt-5 pt-4">

    <h1 class="mb-4 text-light">Importer des Séries (ComicVine)</h1>

    <div class="card bg-dark text-light mb-4">
        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Mot-clé</label>
                    <input type="text" id="searchQuery" class="form-control" placeholder="Batman, Avengers...">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Éditeur</label>
                    <select id="publisherId" class="form-select">
                        <option value="">-- Choisir un éditeur --</option>
                        <?php foreach ($publishers as $p): ?>
                            <option value="<?= $p['publisher_id'] ?>">
                                <?= htmlspecialchars($p['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <button id="searchBtn" class="btn btn-primary w-100">
                        <i class="fa fa-search"></i> Rechercher
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div id="status" class="text-warning mb-2"></div>
    <div id="progressZone" class="text-info mb-3" style="font-family:monospace;"></div>

    <table id="seriesTable" class="table table-dark table-striped table-bordered align-middle" style="display:none;">
        <thead>
            <tr>
                <th>Sélection</th>
                <th>Logo</th>
                <th>Nom</th>
                <th>Année</th>
                <th>Issues</th>
                <th>ID Série</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <button id="btnImportSelected" class="btn btn-success mt-3" style="display:none;">
        <i class="fa fa-download"></i> Importer la sélection
    </button>

</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
async function searchVolumesDynamic() {
    const query = $('#searchQuery').val().trim();
    const publisherId = parseInt($('#publisherId').val(), 10);

    if (!query) {
        $('#status').text("Mot-clé manquant.");
        $('#progressZone').html("");
        return;
    }
    if (!publisherId) {
        $('#status').text("Éditeur manquant.");
        $('#progressZone').html("");
        return;
    }

    $('#status').text("Extraction sécurisée via le serveur...");
    $('#progressZone').html(`<span class="text-warning">Requête en cours de traitement par le serveur...</span>`);

    let allResults = [];
    let page = 1;
    let limit = 100;
    let totalResults = 1;

    try {
        while (((page - 1) * limit) < totalResults) {

            const response = await $.ajax({
                url: "<?= $config['base_url'] ?>/index.php?route=gestion_series_search",
                type: "POST",
                data: {
                    query: query,
                    publisher_id: publisherId,
                    page: page
                },
                dataType: "json"
            });

            if (!response || !response.success || response.results.status_code !== 1) {
                const msg = response ? response.message : "Erreur serveur.";
                $('#status').html(`<span class="text-danger fw-bold">🛑 Interrompu : ${msg}</span>`);
                return;
            }

            const data = response.results;
            totalResults = data.number_of_total_results || 0;

            const filtered = (data.results || []).filter(v =>
                v.publisher && parseInt(v.publisher.id, 10) === publisherId
            );

            allResults = allResults.concat(filtered);

            const totalPages = totalResults > 0 ? Math.ceil(totalResults / limit) : page;

            $('#progressZone').html(`
                <span class="text-warning">📡 Page ${page} / ${totalPages} (Vérifiée par le Limiter)</span><br>
                <span class="text-info">🎯 Séries correspondant à l'éditeur : <b>${allResults.length}</b></span><br>
                <span class="text-secondary">🔍 Résultats bruts analysés : ${Math.min(page * limit, totalResults)} / ${totalResults}</span>
            `);

            if (!data.results || data.results.length === 0) break;
            page++;
        }

        if (allResults.length === 0) {
            $('#status').text("Aucun résultat pour cet éditeur avec ce mot-clé.");
            return;
        }

        $('#status').text("Extraction terminée avec succès.");
        displayResults(allResults);

    } catch (e) {
        $('#status').text("Erreur de liaison avec le serveur.");
        console.error(e);
    }
}

let seriesTable = null;

function displayResults(list) {
    $('#seriesTable').show();
    $('#btnImportSelected').show();

    if (seriesTable) {
        seriesTable.clear().destroy();
    }

    const tbody = $('#seriesTable tbody');
    tbody.empty();

    list.forEach(v => {
        const imgLight = v.image
            ? (v.image.icon_url || v.image.thumb_url || v.image.medium_url)
            : "https://placehold.co/80x120/1a1a1a/666?text=No+Img";

        const imgOriginal = v.image
            ? (v.image.original_url || v.image.super_url || v.image.medium_url || v.image.icon_url)
            : "";

        const startYear = v.start_year || '';
        const issues = v.count_of_issues || 0;
        const pubId = v.publisher ? v.publisher.id : '';
        const safeName = (v.name || '').replace(/"/g, '&quot;');

        tbody.append(`
            <tr>
                <td class="text-center">
                    <input type="checkbox" class="chkImport"
                        data-series_id="${v.id}"
                        data-name="${safeName}"
                        data-start_year="${startYear}"
                        data-count_of_issues="${issues}"
                        data-publisher_id="${pubId}"
                        data-original_url="${imgOriginal}">
                </td>
                <td><img src="${imgLight}" style="width:60px;height:90px;object-fit:cover;"></td>
                <td class="fw-bold text-warning">${safeName}</td>
                <td>${startYear}</td>
                <td>${issues}</td>
                <td><code>${v.id}</code></td>
            </tr>
        `);
    });

    seriesTable = $('#seriesTable').DataTable({
        pageLength: 20,
        order: [[2, 'asc']],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/fr-FR.json"
        }
    });
}

function importSelectedSeries() {
    const selected = $('.chkImport:checked');

    if (selected.length === 0) {
        alert("Aucune série sélectionnée.");
        return;
    }

    $('#status').text("Import en cours...");

    let done = 0;
    const total = selected.length;

    selected.each(function() {
        const fd = new FormData();
        fd.append("series_id", $(this).data('series_id'));
        fd.append("name", $(this).data('name'));
        fd.append("start_year", $(this).data('start_year'));
        fd.append("count_of_issues", $(this).data('count_of_issues'));
        fd.append("publisher_id", $(this).data('publisher_id'));
        fd.append("original_url", $(this).data('original_url'));

        $.ajax({
            url: "<?= $config['base_url'] ?>/index.php?route=gestion_series_import",
            type: "POST",
            data: fd,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(resp) {
                done++;
                if (done === total) {
                    $('#status').html("<span class='text-success fw-bold'>Votre sélection a bien été importée.</span>");
                }
            },
            error: function() {
                done++;
                if (done === total) {
                    $('#status').text("Erreur lors de l'import.");
                }
            }
        });
    });
}

$(document).ready(function() {
    $('#searchBtn').on('click', function() {
        searchVolumesDynamic();
    });

    $('#btnImportSelected').on('click', function() {
        importSelectedSeries();
    });

    $('#searchQuery').on('keyup', function(e) {
        if (e.key === 'Enter') {
            searchVolumesDynamic();
        }
    });
});
</script>

<?php
require __DIR__ . '/../layouts/footer.php';
?>