<?php
$config = require __DIR__ . '/../../Config/config.php';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/menu.php';
?>

<div class="container-fluid text-light" style="margin-top: 90px; margin-bottom: 50px;">
    <div class="row mb-4">
        <div class="col">
            <h2 class="text-warning"><i class="fa-solid fa-book-open me-2"></i>Création Manuelle d'un Tome</h2>
            <p class="text-secondary">Ajoutez un nouvel album à votre collection ou à votre liste de recherche, puis associez-y ses épisodes US.</p>
        </div>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
        <div class="alert alert-danger bg-danger bg-opacity-20 text-danger border-danger mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> Une erreur est survenue lors de l'enregistrement du tome.
        </div>
    <?php endif; ?>

    <form id="formCreateTome" action="<?= $base ?>/index.php?route=gestion_tomes_save" method="POST">
    <div class="row g-4">
            
            <div class="col-lg-7">
                <div class="card bg-dark text-light border-secondary shadow">
                    <div class="card-header border-secondary bg-black bg-opacity-20">
                        <h5 class="mb-0 text-info"><i class="fa-solid fa-circle-info me-2"></i>Informations Générales</h5>
                    </div>
                    <div class="card-body">
                        
                        <div class="row mb-3">
                            <div class="col-md-9">
                                <label class="form-label font-monospace small text-secondary">Nom du Tome *</label>
                                <input type="text" name="name" class="form-control bg-secondary bg-opacity-10 text-light border-secondary" required placeholder="ex: Batman - Tome 1 - Année Un">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label font-monospace small text-secondary">N° Tome</label>
                                <input type="number" name="tome_number" class="form-control bg-secondary bg-opacity-10 text-light border-secondary" placeholder="1">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-monospace small text-secondary">Série (VF / Principale)</label>
                            <select name="series_id" class="form-select bg-secondary bg-opacity-10 text-light border-secondary">
                                <option value="">-- Sélectionner une série --</option>
                                <?php if (!empty($series)): ?>
                                    <?php foreach ($series as $s): ?>
                                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label font-monospace small text-secondary">Univers</label>
                                <div class="input-group">
                                    <select name="universe_id" id="selectUniverse" class="form-select bg-secondary bg-opacity-10 text-light border-secondary">
                                        <option value="">-- Aucun --</option>
                                        <?php if (!empty($universes)): ?>
                                            <?php foreach ($universes as $u): ?>
                                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <button class="btn btn-outline-info" type="button" data-bs-toggle="modal" data-bs-target="#quickAddUniverseModal"><i class="fa-solid fa-plus"></i></button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-monospace small text-secondary">Gamme / Collection</label>
                                <div class="input-group">
                                    <select name="collection_id" id="selectCollection" class="form-select bg-secondary bg-opacity-10 text-light border-secondary">
                                        <option value="">-- Aucune --</option>
                                        <?php if (!empty($collections)): ?>
                                            <?php foreach ($collections as $c): ?>
                                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <button class="btn btn-outline-info" type="button" data-bs-toggle="modal" data-bs-target="#quickAddCollectionModal"><i class="fa-solid fa-plus"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label font-monospace small text-secondary">Éditeur VF</label>
                                <input type="text" name="publisher_vf" class="form-control bg-secondary bg-opacity-10 text-light border-secondary" placeholder="Urban Comics, Panini...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-monospace small text-secondary">Éditeur VO</label>
                                <input type="text" name="publisher_vo" class="form-control bg-secondary bg-opacity-10 text-light border-secondary" placeholder="DC, Marvel, Image...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-monospace small text-secondary">Format</label>
                                <input type="text" name="format" class="form-control bg-secondary bg-opacity-10 text-light border-secondary" placeholder="100% Marvel, Nomad...">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label font-monospace small text-secondary">ISBN / Code-barres</label>
                                <input type="text" name="isbn" class="form-control bg-secondary bg-opacity-10 text-light border-secondary" placeholder="978236577...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-monospace small text-secondary">Nombre de pages</label>
                                <input type="number" name="page_count" class="form-control bg-secondary bg-opacity-10 text-light border-secondary" placeholder="160">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-monospace small text-secondary">Date de publication</label>
                                <input type="date" name="publication_date" class="form-control bg-secondary bg-opacity-10 text-light border-secondary">
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label class="form-label font-monospace small text-secondary">Prix Éditeur (€)</label>
                                <input type="number" step="0.01" name="price_original" class="form-control bg-secondary bg-opacity-10 text-light border-secondary" placeholder="15.00">
                            </div>
                        </div>

                        <div class="row mt-4 pt-3 border-top border-secondary">
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_owned" id="checkOwned" value="1" checked>
                                    <label class="form-check-label text-success" for="checkOwned"><i class="fa-solid fa-box-open me-1"></i>Dans ma collection</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_wanted" id="checkWanted" value="1">
                                    <label class="form-check-label text-warning" for="checkWanted"><i class="fa-solid fa-heart me-1"></i>Recherché</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_read" id="checkRead" value="1">
                                    <label class="form-check-label text-info" for="checkRead"><i class="fa-solid fa-eye me-1"></i>Lu</label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card bg-dark text-light border-secondary shadow h-100">
                    <div class="card-header border-secondary bg-black bg-opacity-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-warning"><i class="fa-solid fa-list-ol me-2"></i>Contenu (Épisodes US)</h5>
                        <span class="badge bg-secondary" id="issueCounter">0 épisode lié</span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <p class="text-secondary small">Recherchez et associez les épisodes VO inclus dans ce tome pour générer la chronologie.</p>
                        
                        <div class="position-relative mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-secondary border-secondary text-light"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text" id="issueSearchInput" class="form-control bg-secondary bg-opacity-10 text-light border-secondary" placeholder="Chercher un épisode (ex: Batman #404)..." autocomplete="off">
                            </div>
                            <div id="searchDropdownZone" class="dropdown-menu dropdown-menu-dark w-100 shadow border-secondary mt-1" style="max-height: 250px; overflow-y: auto;"></div>
                        </div>

                        <div class="flex-grow-1 border border-secondary border-dashed rounded p-2 bg-black bg-opacity-10" style="min-height: 200px; max-height: 400px; overflow-y: auto;">
                            <ul class="list-group list-group-flush" id="attachedIssuesList">
                                <li class="text-muted text-center py-4 italic small empty-message">Aucun épisode lié pour le moment.</li>
                            </ul>
                        </div>
                        
                        <div id="hiddenFieldsZone"></div>

                        <div class="mt-4 pt-3 border-top border-secondary text-end">
                            <a href="<?= $base ?>/index.php?route=home" class="btn btn-outline-secondary me-2">Annuler</a>
                            <button type="submit" class="btn btn-warning px-4"><i class="fa-solid fa-floppy-disk me-2"></i>Enregistrer le Tome</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<div class="modal fade" id="quickAddUniverseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content bg-dark text-light border-info">
            <div class="modal-header border-secondary">
                <h6 class="modal-title text-info"><i class="fa-solid fa-plus me-2"></i>Nouvel Univers</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="newUniverseName" class="form-control bg-secondary bg-opacity-10 text-light border-secondary" placeholder="Nom (ex: Univers Batman)">
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-info btn-sm" id="btnSaveQuickUniverse">Ajouter</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="quickAddCollectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content bg-dark text-light border-info">
            <div class="modal-header border-secondary">
                <h6 class="modal-title text-info"><i class="fa-solid fa-plus me-2"></i>Nouvelle Gamme / Collection</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="newCollectionName" class="form-control bg-secondary bg-opacity-10 text-light border-secondary" placeholder="Nom (ex: DC Deluxe)">
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-info btn-sm" id="btnSaveQuickCollection">Ajouter</button>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    const baseUrl = "<?= $base ?>";
    let attachedIssues = [];

    $('#issueSearchInput').on('input', function() {
        let query = $(this).val().trim();
        if (query.length < 2) {
            $('#searchDropdownZone').removeClass('show').html('');
            return;
        }

        $.getJSON(`${baseUrl}/index.php?route=search_issues_ajax&q=${encodeURIComponent(query)}`, function(data) {
            let html = "";
            if (data.length === 0) {
                html = `<div class="dropdown-item text-muted small italic">Aucun épisode trouvé</div>`;
            } else {
                data.forEach(issue => {
                    let disabledClass = attachedIssues.some(i => i.id == issue.id) ? 'disabled text-muted' : '';
                    html += `
                        <a class="dropdown-item search-result-item ${disabledClass}" href="#" data-id="${issue.id}" data-label="${issue.series_name} #${issue.issue_number} (${issue.year})">
                            <i class="fa-regular fa-file-lines text-warning me-2"></i>
                            <strong>${issue.series_name} #${issue.issue_number}</strong> <span class="text-secondary small">(${issue.year})</span>
                        </a>
                    `;
                });
            }
            $('#searchDropdownZone').addClass('show').html(html);
        });
    });

    $(document).on('click', '.search-result-item', function(e) {
        e.preventDefault();
        if ($(this).hasClass('disabled')) return;

        let id = $(this).data('id');
        let label = $(this).data('label');

        attachedIssues.push({id: id, label: label});
        renderAttachedIssues();
        
        $('#issueSearchInput').val('').focus();
        $('#searchDropdownZone').removeClass('show').html('');
    });

    $(document).click(function(e) {
        if (!$(e.target).closest('#issueSearchInput, #searchDropdownZone').length) {
            $('#searchDropdownZone').removeClass('show');
        }
    });

    $(document).on('click', '.remove-issue-btn', function() {
        let id = $(this).data('id');
        attachedIssues = attachedIssues.filter(i => i.id != id);
        renderAttachedIssues();
    });

    function renderAttachedIssues() {
        let listZone = $('#attachedIssuesList');
        let hiddenZone = $('#hiddenFieldsZone');
        
        if (attachedIssues.length === 0) {
            listZone.html('<li class="text-muted text-center py-4 italic small empty-message">Aucun épisode lié pour le moment.</li>');
            hiddenZone.html('');
            $('#issueCounter').text('0 épisode lié').removeClass('bg-success').addClass('bg-secondary');
            return;
        }

        let listHtml = "";
        let hiddenHtml = "";

        attachedIssues.forEach((issue, index) => {
            listHtml += `
                <li class="list-group-item bg-dark border-secondary text-light d-flex justify-content-between align-items-center py-2 px-1">
                    <span class="small font-monospace"><span class="text-warning me-2">#${index + 1}</span> ${issue.label}</span>
                    <button type="button" class="btn btn-link text-danger btn-sm p-0 remove-issue-btn" data-id="${issue.id}">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </li>
            `;
            hiddenHtml += `<input type="hidden" name="issues[]" value="${issue.id}">`;
        });

        listZone.html(listHtml);
        hiddenZone.html(hiddenHtml);
        $('#issueCounter').text(`${attachedIssues.length} épisode(s) lié(s)`).removeClass('bg-secondary').addClass('bg-success');
    }

    $('#btnSaveQuickUniverse').click(function() {
        let name = $('#newUniverseName').val().trim();
        if(!name) return;
        $.post(`${baseUrl}/index.php?route=quick_add_universe`, {name: name}, function(response) {
            if(response.success) {
                $('#selectUniverse').append(new Option(response.name, response.id, true, true));
                $('#quickAddUniverseModal').modal('hide');
                $('#newUniverseName').val('');
            }
        }, 'json');
    });

    $('#btnSaveQuickCollection').click(function() {
        let name = $('#newCollectionName').val().trim();
        if(!name) return;
        $.post(`${baseUrl}/index.php?route=quick_add_collection`, {name: name}, function(response) {
            if(response.success) {
                $('#selectCollection').append(new Option(response.name, response.id, true, true));
                $('#quickAddCollectionModal').modal('hide');
                $('#newCollectionName').val('');
            }
        }, 'json');
    });
});
</script>

<?php
require __DIR__ . '/../layouts/footer.php';
?>