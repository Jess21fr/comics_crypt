<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/menu.php';
?>

<div class="container mt-4 text-white">

    <h1 class="mb-4">Éditer l’Issue #<?= htmlspecialchars($issue['number']) ?></h1>

    <form id="editIssueForm">

        <input type="hidden" name="id" value="<?= htmlspecialchars($issue['id']) ?>">

        <!-- ============================
             COVER + MODALE COMICVINE
        ============================= -->
        <div class="mb-3">
            <label class="form-label">Cover</label><br>

            <img id="coverPreview"
                 src="<?= !empty($issue['cover_local']) ? '/comics_crypt/public/covers/' . $issue['cover_local'] : '' ?>"
                 style="max-height:200px; border:1px solid #ccc;">

            <input type="hidden"
                   id="cover_local"
                   name="cover_local"
                   value="<?= $issue['cover_local'] ?? '' ?>">

            <button type="button"
                    id="btnOpenCoverModal"
                    class="btn btn-primary mt-2">
                Sélectionner une cover ComicVine
            </button>
        </div>

        <!-- ============================
             FORMULAIRE ISSUE
        ============================= -->
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Numéro</label>
                <input type="text" name="number" class="form-control"
                       value="<?= htmlspecialchars($issue['number']) ?>">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Date VO</label>
                <input type="date" name="on_sale_date" class="form-control"
                       value="<?= htmlspecialchars($issue['on_sale_date']) ?>">
            </div>

            <div class="col-md-5 mb-3">
                <label class="form-label">Titre</label>
                <input type="text" name="title" class="form-control"
                       value="<?= htmlspecialchars($issue['title']) ?>">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Synopsis</label>
            <textarea name="synopsis" class="form-control" rows="6"><?= htmlspecialchars($issue['synopsis']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-success w-100 mt-3">
            Enregistrer les modifications
        </button>

    </form>

</div>


<!-- ============================
     MODALE COMICVINE
============================= -->
<div class="modal fade" id="coverModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content bg-dark text-white">

            <div class="modal-header">
                <h5 class="modal-title">Recherche de cover ComicVine</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Numéro de l'issue</label>
                        <input type="text"
                               id="issue_number"
                               class="form-control"
                               value="<?= $issue['number'] ?? '' ?>">
                    </div>

                    <div class="col-md-4">
                        <label>Volume ID ComicVine</label>
                        <input type="text"
                               id="comicvine_volume_id"
                               class="form-control"
                               value="<?= $series['comicvine_volume_id'] ?? '' ?>">
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button id="btnSearchCover"
                                class="btn btn-success w-100">
                            Rechercher
                        </button>
                    </div>
                </div>

                <hr>

                <div id="comicvineResults" class="text-center">
                    <p class="text-muted">Aucun résultat pour le moment.</p>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>

        </div>
    </div>
</div>

<!-- JS -->
<script src="/comics_crypt/public/assets/js/cover_modal.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {

    /* ============================================================
       SAUVEGARDE ISSUE
    ============================================================ */
    document.getElementById("editIssueForm").addEventListener("submit", function(e) {
        e.preventDefault();

        fetch("index.php?route=gestion_issues_update", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: new URLSearchParams(new FormData(this)).toString()
        })
        .then(r => r.json())
        .then(res => {
            alert(res.message);
            if (res.success) {
                window.location.href = "index.php?route=gestion_issues_gerer";
            }
        });
    });

});
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>