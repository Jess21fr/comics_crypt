<?php
$config = require __DIR__ . '/../../Config/config.php';
?>

<form id="editSeriesForm" enctype="multipart/form-data">

    <input type="hidden" name="id" value="<?= htmlspecialchars($serie['id']) ?>">

    <div class="row">

        <div class="col-md-5 text-center">

            <img src="<?= $config['base_url'] ?>/series/<?= htmlspecialchars($serie['logo']) ?>"
                 style="width:260px;height:390px;object-fit:cover;border:1px solid #444;border-radius:4px;">

            <div class="mt-3">
                <label class="btn btn-warning w-100">
                    Changer l’image
                    <input type="file" name="new_logo" accept="image/*" hidden>
                </label>
            </div>

        </div>

        <div class="col-md-7">

            <div class="mb-3">
                <label class="form-label">Nom de la série</label>
                <input type="text" name="name" class="form-control"
                       value="<?= htmlspecialchars($serie['name']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Année de début</label>
                <input type="number" name="start_year" class="form-control"
                       value="<?= htmlspecialchars($serie['start_year']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Nombre d’épisodes</label>
                <input type="number" name="count_of_issues" class="form-control"
                       value="<?= htmlspecialchars($serie['count_of_issues']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Éditeur</label>

                <div class="d-flex align-items-center gap-2 p-2 bg-secondary rounded">

                    <?php if (!empty($publisher['logo'])): ?>
                        <img src="<?= $config['base_url'] ?>/logos/<?= htmlspecialchars($publisher['logo']) ?>"
                             style="width:40px;height:40px;object-fit:contain;">
                    <?php endif; ?>

                    <strong class="fs-5"><?= htmlspecialchars($publisher['name'] ?? 'Inconnu') ?></strong>

                </div>

                <input type="hidden" name="publisher_id"
                       value="<?= htmlspecialchars($serie['publisher_id']) ?>">

            </div>

            <div class="mb-3">
                <label class="form-label d-block">Actif</label>

                <div class="form-check form-switch fs-4">
                    <input class="form-check-input" type="checkbox" name="actif"
                           <?= $serie['actif'] ? 'checked' : '' ?>>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-3">
                Enregistrer les modifications
            </button>

        </div>
    </div>

</form>

<script>
$(function() {

    $('#editSeriesForm').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "index.php?route=gestion_series_update",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                if (response.success) {

                    let modalEl = document.getElementById('editSeriesModal');
                    if (modalEl) {
                        let modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) {
                            modal.hide();
                        }
                    }

                    location.reload();
                } else {
                    alert(response.message);
                }
            }
        });
    });

});
</script>