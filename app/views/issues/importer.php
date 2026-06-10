<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/menu.php';
?>

<div class="container mt-4">

    <h1 class="mb-4 text-white">Importer les Issues</h1>

    <!-- ============================
         ÉTAPE 1 : CHOIX DE LA SÉRIE
    ============================ -->
    <div class="card bg-dark text-white mb-4">
        <div class="card-body">
            <h4 class="text-info">Étape 1 — Choisir une série</h4>
            <p class="text-muted">Filtrez la série dans le tableau ci‑dessous puis cliquez sur <b>Importer</b>.</p>

            <table id="series_table" class="table table-dark table-striped" style="width:100%;">
                <thead>
                    <tr>
                        <th>Série</th>
                        <th>Année</th>
                        <th>Éditeur</th>
                        <th>Pays</th>
                        <th>Issues</th>
                        <th>Importer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($series as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['name']) ?></td>
                            <td><?= $s['year_began'] ?></td>
                            <td><?= htmlspecialchars($s['publisher_name']) ?></td>
                            <td><?= htmlspecialchars($s['country_code']) ?></td>
                            <td><?= $s['issue_count'] ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm import-btn"
                                    data-name="<?= htmlspecialchars($s['name']) ?>"
                                    data-year="<?= $s['year_began'] ?>"
                                    data-count="<?= $s['issue_count'] ?>"
                                    data-pub="<?= htmlspecialchars($s['publisher_name']) ?>"
                                    data-country="<?= strtolower($s['country_code']) ?>">
                                    Importer
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================
         ÉTAPE 2 : COLLER JSON ISSUES
    ============================ -->
    <div id="step2" class="card bg-dark text-white mb-4" style="display:none;">
        <div class="card-body">
            <h4 class="text-info">Étape 2 — Coller le JSON des issues</h4>
            <textarea id="json_issues" class="form-control bg-black text-white" rows="10"
                      placeholder="Collez ici le JSON des issues Comics.org (_export=db_json)"></textarea>
            <button id="btn_preview_issues" class="btn btn-success mt-3 w-100">
                Prévisualiser les issues
            </button>
        </div>
    </div>

    <!-- ============================
         ÉTAPE 3 : TABLEAU D'IMPORT
    ============================ -->
    <div id="step3" class="card bg-dark text-white mb-4" style="display:none;">
        <div class="card-body">
            <h4 class="text-info">Étape 3 — Prévisualiser et importer les issues</h4>

            <div id="issues_preview_block" class="mt-3"></div>

            <button id="btn_import_selected" class="btn btn-success mt-3">
                Importer sélectionnés
            </button>

            <button id="btn_import_all" class="btn btn-danger mt-3 ms-2">
                Importer tout
            </button>
        </div>
    </div>

</div>

<!-- ============================
     MODALE SÉLECTION COVER WEB
============================ -->
<div class="modal fade" id="modalSelectCover" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h5 class="modal-title">Sélectionner une cover</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <div id="modal_cover_issue_id" class="d-none" data-issue-id=""></div>
        <div id="modal_cover_grid" class="row g-3"></div>
      </div>
      <div class="modal-footer">
        <button id="btn_apply_cover" type="button" class="btn btn-success">
          Affecter l'image
        </button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<style>
    .placeholder-cover {
        width: 80px;
        height: 120px;
        background-color: #555;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Jolly Lodger', cursive;
        color: #ff3333;
        font-size: 14px;
        text-align: center;
    }
</style>

<script src="assets/js/issues_import.js"></script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
