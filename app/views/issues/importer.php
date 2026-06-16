<?php
$config = require __DIR__ . '/../../Config/config.php';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/menu.php';
?>
<div class="container mt-5 pt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-light"><i class="fa-solid fa-cloud-arrow-down text-warning"></i> Synchro Épisodes</h1>
    </div>

    <table id="seriesTable" class="table table-dark table-striped table-bordered align-middle">
        <thead>
            <tr>
                <th style="width: 60px;" class="text-center">Logo</th>
                <th>Nom de la Série</th>
                <th>Éditeur</th>
                <th style="width: 90px;" class="text-center">Année</th>
                <th style="width: 100px;" class="text-center">Nb. Issues</th>
                <th style="width: 130px;" class="text-center text-warning">Importées</th>
                <th style="width: 100px;">Series ID</th>
                <th style="width: 100px;" class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($seriesList as $s): ?>
            <tr id="row-series-<?= $s['series_id'] ?>">
                <td class="text-center">
                    <img src="/comics_crypt/public/series/<?= $s['series_id'] ?>.jpg" style="width:40px; height:auto; border-radius:2px;" onerror="this.src='https://placehold.co/40x60?text=?'" />
                </td>
                <td class="fw-bold text-light"><?= htmlspecialchars($s['name']) ?></td>
                
                <td>
                    <?php if (!empty($s['publisher_logo'])): ?>
                        <img src="/comics_crypt/public/logos/<?= htmlspecialchars($s['publisher_logo']) ?>" style="width:24px; height:auto; margin-right:8px; border-radius:2px;" onerror="this.src='https://placehold.co/24x24?text=?'" />
                    <?php else: ?>
                        <img src="https://placehold.co/24x24?text=?" style="width:24px; height:auto; margin-right:8px; border-radius:2px;" />
                    <?php endif; ?>
                    <span class="text-light"><?= htmlspecialchars($s['publisher_name'] ?? 'Inconnu') ?></span>
                </td>
                
                <td class="text-center text-secondary"><?= htmlspecialchars($s['start_year'] ?? '-') ?></td>
                <td class="text-center fw-bold text-info"><?= htmlspecialchars($s['count_of_issues'] ?? 0) ?></td>
                <td class="text-center fw-bold text-warning count-local"><?= $s['imported_count'] ?></td>
                <td><code><?= $s['series_id'] ?></code></td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-1">
                        <button class="btn btn-sm btn-warning btnFetchIssues" 
                                data-id="<?= $s['series_id'] ?>" 
                                data-name="<?= htmlspecialchars($s['name']) ?>"
                                title="Lancer la requête ComicVine">
                            <i class="fa fa-sync-alt"></i>
                        </button>
                        
                        <button class="btn btn-sm btn-info btnManageLocalIssues" 
                                data-id="<?= $s['series_id'] ?>" 
                                data-name="<?= htmlspecialchars($s['name']) ?>"
                                style="<?= ($s['imported_count'] > 0) ? '' : 'display:none;' ?>"
                                title="Gérer les épisodes importés">
                            <i class="fa fa-edit"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="importModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content bg-dark text-light border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-warning" id="modalTitle">Sélection des épisodes</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalStatus" class="alert alert-info py-2" style="display:none;"></div>
                <div class="table-responsive">
                    <table class="table table-dark table-striped align-middle" id="modalIssuesTable">
                        <thead>
                            <tr>
                                <th style="width: 60px;">Image</th>
                                <th style="width: 400px;">Série VO</th>
                                <th style="width: 90px;">Numéro</th>
                                <th>Titre de l'épisode</th>
                                <th style="width: 40px;" class="text-center"><input type="checkbox" id="modalSelectAll" checked></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-secondary d-flex justify-content-between">
                <div id="modalProgressZone" class="fw-bold text-info"></div>
                <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" id="btnImportSelected" class="btn btn-success">
                        <i class="fa fa-download"></i> Importer la sélection
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="localIssuesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content bg-dark text-light border-info">
            <div class="modal-header border-info">
                <h5 class="modal-title text-info" id="localModalTitle"><i class="fa fa-database"></i> Épisodes en Base de Données</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-dark table-striped align-middle w-100" id="localIssuesTable">
                        <thead>
                            <tr>
                                <th style="width: 60px;">Image</th>
                                <th style="width: 80px;">ID CV</th>
                                <th style="width: 90px;">Numéro</th>
                                <th>Titre de l'épisode local</th>
                                <th style="width: 120px;">Date Couv.</th>
                                <th style="width: 80px;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-info">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editSingleIssueModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content bg-dark text-light border-warning">
            <div class="modal-header border-warning">
                <h5 class="modal-title text-warning" id="edit_modal_title"><i class="fa fa-edit"></i> Modifier l'Épisode</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditIssue" autocomplete="off" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <input type="hidden" id="edit_series_id" name="series_id">
                    
                    <div class="row">
                        <div class="col-md-3 text-center mb-3">
                            <div class="mb-2">
                                <img id="edit_preview_img" src="https://placehold.co/180x270?text=No+Cover" class="img-fluid rounded border border-secondary" style="max-height: 350px; object-fit: cover;">
                            </div>
                            <button type="button" class="btn btn-sm btn-warning w-100 fw-bold" id="btnTriggerUpload">
                                <i class="fa fa-image"></i> Changer l'image
                            </button>
                            <input type="file" id="edit_file_input" name="cover_file" style="display: none;" accept="image/*">
                        </div>
                        
                        <div class="col-md-9">
                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold">Titre de l'épisode</label>
                                <input type="text" class="form-control bg-dark text-light border-secondary" id="edit_name" name="name" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-secondary small fw-bold">Numéro d'épisode</label>
                                    <input type="text" class="form-control bg-dark text-light border-secondary" id="edit_issue_number" name="issue_number" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-secondary small fw-bold">Date de couverture</label>
                                    <input type="date" class="form-control bg-dark text-light border-secondary" id="edit_cover_date" name="cover_date">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold">Description / Synopsis</label>
                                <textarea class="form-control bg-dark text-light border-secondary" id="edit_description" name="description" rows="10"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-warning">
                    <button type="button" class="btn btn-secondary" data-bs-target="#localIssuesModal" data-bs-toggle="modal">Retour à la liste</button>
                    <button type="submit" class="btn btn-primary px-4">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
let currentSeriesId = null;
let currentSeriesName = "";

$(document).ready(function() {
    $('#seriesTable').DataTable({
        pageLength: 25,
        language: { url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/fr-FR.json" }
    });

    $(document).on('click', '.btnFetchIssues', function() {
        currentSeriesId = $(this).data('id');
        currentSeriesName = $(this).data('name');
        
        $('#modalTitle').html(`<i class="fa fa-book-open text-warning"></i> Épisodes disponibles pour : <span class="text-info">${currentSeriesName}</span>`);
        $('#modalIssuesTable tbody').empty();
        $('#modalStatus').show().html(`<div class="spinner-border spinner-border-sm text-light me-2"></div> Connexion à ComicVine...`);
        $('#modalProgressZone').text('');
        $('#btnImportSelected').prop('disabled', true);
        $('#modalSelectAll').prop('checked', true);

        const myModal = new bootstrap.Modal(document.getElementById('importModal'));
        myModal.show();
        loadIssuesFromApi();
    });

    $(document).on('click', '.btnManageLocalIssues', function() {
        currentSeriesId = $(this).data('id');
        currentSeriesName = $(this).data('name');
        $('#localModalTitle').html(`<i class="fa fa-database text-info"></i> Épisodes en BDD pour : <span class="text-warning">${currentSeriesName}</span>`);
        loadLocalIssues();
        const localModal = new bootstrap.Modal(document.getElementById('localIssuesModal'));
        localModal.show();
    });

    $(document).on('click', '.btnEditSingleIssue', function() {
        const issueId = $(this).data('id');
        const cvIssueId = $(this).data('issue_id');
        const issueNum = $(this).data('issue_number');
        
        $('#edit_modal_title').html(`<i class="fa fa-edit"></i> Modifier l'épisode #${issueNum} de la série ${currentSeriesName}`);
        
        $('#edit_id').val(issueId);
        $('#edit_series_id').val($(this).data('series_id'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_issue_number').val(issueNum);
        $('#edit_cover_date').val($(this).data('cover_date'));
        $('#edit_description').val($(this).closest('tr').find('.local-desc-store').html());
        
        // Reset de l'input file à l'ouverture pour éviter les résidus
        $('#edit_file_input').val('');

        // Définition de l'image (si l'ID ComicVine est absent, placeholder propre)
        if (cvIssueId && cvIssueId !== 'undefined' && cvIssueId !== 'null') {
            $('#edit_preview_img').attr('src', '../public/issues/' + cvIssueId + '.jpg');
        } else {
            $('#edit_preview_img').attr('src', 'https://placehold.co/180x270?text=No+Cover');
        }

        $('#localIssuesModal').modal('hide');
        const editModal = new bootstrap.Modal(document.getElementById('editSingleIssueModal'));
        editModal.show();
    });

    // Liaison du bouton jaune vers l'input file masqué
    $(document).on('click', '#btnTriggerUpload', function() {
        $('#edit_file_input').click();
    });

    // Prévisualisation immédiate en local lors du choix d'une image
    $(document).on('change', '#edit_file_input', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#edit_preview_img').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    $('#formEditIssue').on('submit', function(e) {
        e.preventDefault();
        
        // Utilisation de FormData car il y a potentiellement un fichier image à envoyer
        const formData = new FormData(this);

        $.ajax({
            url: "index.php?route=gestion_issues_update",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(res) {
                if(res && res.success) {
                    $('#editSingleIssueModal').modal('hide');
                    $('#localIssuesModal').modal('show');
                    loadLocalIssues();
                } else {
                    alert("Erreur : " + (res.message || "Inconnue"));
                }
            }
        });
    });

    $('#modalSelectAll').on('change', function() { $('.chkModalIssue').prop('checked', this.checked); });
    $('#btnImportSelected').on('click', launchBatchImport);
});

async function loadIssuesFromApi() {
    let allResults = [];
    let page = 1; let limit = 50; let totalResults = 1;
    try {
        while (((page - 1) * limit) < totalResults) {
            const response = await $.ajax({ url: "index.php?route=gestion_issues_search", type: "POST", data: { series_id: currentSeriesId, page: page }, dataType: "json" });
            if (!response || !response.success || response.results.status_code !== 1) { $('#modalStatus').html(`<span class="text-danger fw-bold">🛑 Erreur API</span>`); return; }
            const data = response.results;
            totalResults = data.number_of_total_results || 0;
            allResults = allResults.concat(data.results || []);
            const totalPages = Math.ceil(totalResults / limit);
            $('#modalStatus').html(`<div class="spinner-border spinner-border-sm text-light me-2"></div> Téléchargement : Page ${page} / ${totalPages}`);
            if (!data.results || data.results.length === 0) break;
            page++;
        }
        $('#modalStatus').hide();
        allResults.sort((a, b) => parseFloat(a.issue_number || 0) - parseFloat(b.issue_number || 0));
        renderModalTable(allResults);
    } catch (e) { $('#modalStatus').html(`<span class="text-danger fw-bold">🛑 Échec.</span>`); }
}

function renderModalTable(issues) {
    const tbody = $('#modalIssuesTable tbody').empty();
    if (issues.length === 0) { tbody.append(`<tr><td colspan="5" class="text-center text-muted">Aucun épisode trouvé.</td></tr>`); return; }
    issues.forEach(item => {
        const thumb = item.image ? (item.image.thumb_url || item.image.small_url || item.image.icon_url) : "https://placehold.co/40x60?text=No+Cover";
        tbody.append(`<tr>
            <td><img src="${thumb}" style="width:40px; height:58px; object-fit:cover; border-radius:2px;"></td>
            <td class="text-secondary small">${currentSeriesName}</td>
            <td class="fw-bold text-info"># ${item.issue_number}</td>
            <td class="text-light fw-bold">${item.name || '<em>Sans titre</em>'}</td>
            <td class="text-center">
                <input type="checkbox" class="chkModalIssue" checked data-issue_id="${item.id}" data-series_id="${currentSeriesId}" data-name="${(item.name || '').replace(/"/g, '&quot;')}" data-issue_number="${item.issue_number}" data-cover_date="${item.cover_date || ''}" data-original_url="${item.image ? item.image.original_url : ''}">
                <div class="desc-store" style="display:none;">${item.description || ''}</div>
            </td>
        </tr>`);
    });
    $('#btnImportSelected').prop('disabled', false);
}

function loadLocalIssues() {
    const tbody = $('#localIssuesTable tbody');
    
    if ($.fn.DataTable.isDataTable('#localIssuesTable')) {
        $('#localIssuesTable').DataTable().destroy();
    }
    tbody.html('<tr><td colspan="6" class="text-center"><div class="spinner-border spinner-border-sm text-info"></div> Chargement local...</td></tr>');
    
    $.ajax({
        url: "index.php?route=gestion_issues_list_local",
        type: "POST",
        data: { series_id: currentSeriesId },
        dataType: "json",
        success: function(res) {
            tbody.empty();
            if(!res || res.length === 0) {
                tbody.append('<tr><td colspan="6" class="text-center text-muted">Aucun épisode local.</td></tr>');
                return;
            }
            
            res.forEach(issues => {
                const imgPath = `../public/issues/${issues.issue_id}.jpg`;
                
                let dateFR = '-';
                if(issues.cover_date) {
                    const parts = issues.cover_date.split('-');
                    if(parts.length === 3) dateFR = `${parts[2]}/${parts[1]}/${parts[0]}`;
                }

                tbody.append(`
                    <tr>
                        <td><img src="${imgPath}" style="width:55px; height:80px; object-fit:cover; border-radius:2px;" onerror="this.src='https://placehold.co/55x80?text=no cover'"></td>
                        <td><code class="text-warning">${issues.issue_id}</code></td>
                        <td class="fw-bold text-info"># ${issues.issue_number}</td>
                        <td class="text-light fw-bold">${issues.name || '<em>Sans titre</em>'}</td>
                        <td class="text-secondary">${dateFR}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary btnEditSingleIssue"
                                    data-id="${issues.id}" data-issue_id="${issues.issue_id}" data-series_id="${issues.series_id}" data-name="${(issues.name || '').replace(/"/g, '&quot;')}"
                                    data-issue_number="${issues.issue_number}" data-cover_date="${issues.cover_date || ''}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <div class="local-desc-store" style="display:none;">${issues.description || ''}</div>
                        </td>
                    </tr>
                `);
            });

            $('#localIssuesTable').DataTable({
                "pageLength": 10,
                "ordering": false,
                "language": { url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/fr-FR.json" }
            });
        }
    });
}

function launchBatchImport() {
    const selected = $('.chkModalIssue:checked');
    if (selected.length === 0) { alert("Sélectionne au moins un épisode !"); return; }
    $('#btnImportSelected').prop('disabled', true);
    let done = 0; const total = selected.length;
    $('#modalStatus').show().html(`<div class="spinner-border spinner-border-sm text-success me-2"></div> Traitement...`);
    selected.each(function() {
        const row = $(this).closest('tr');
        const fd = new FormData();
        fd.append("issue_id", $(this).data('issue_id'));
        fd.append("series_id", $(this).data('series_id'));
        fd.append("name", $(this).data('name'));
        fd.append("issue_number", $(this).data('issue_number'));
        fd.append("cover_date", $(this).data('cover_date'));
        fd.append("original_url", $(this).data('original_url'));
        fd.append("description", row.find('.desc-store').html());

        $.ajax({
            url: "index.php?route=gestion_issues_import",
            type: "POST", data: fd, processData: false, contentType: false, dataType: "json",
            success: function(res) {
                done++;
                $('#modalProgressZone').html(`<span class="text-success">🚀 ${done} / ${total}</span>`);
                if (done === total) {
                    $('#modalStatus').html("<span class='text-success fw-bold'>✅ Importation terminée !</span>");
                    const localCounter = $(`#row-series-${currentSeriesId} .count-local`);
                    localCounter.text(parseInt(localCounter.text()) + total);
                    $(`#row-series-${currentSeriesId} .btnManageLocalIssues`).show();
                    $('#btnImportSelected').prop('disabled', false);
                }
            }
        });
    });
}
</script>

<?php
require __DIR__ . '/../layouts/footer.php';
?>