<?php
// Variables fournies par le controller :
// $serie → données de la série
?>

<form id="editSeriesForm">

    <input type="hidden" name="id" value="<?= htmlspecialchars($serie['id']) ?>">

    <div class="mb-3">
        <label class="form-label">Nom</label>
        <input type="text" name="name" class="form-control"
               value="<?= htmlspecialchars($serie['name']) ?>" required>
    </div>

    <div class="row">
        <div class="col mb-3">
            <label class="form-label">Année début</label>
            <input type="number" name="year_began" class="form-control"
                   value="<?= htmlspecialchars($serie['year_began']) ?>">
        </div>

        <div class="col mb-3">
            <label class="form-label">Année fin</label>
            <input type="number" name="year_ended" class="form-control"
                   value="<?= htmlspecialchars($serie['year_ended']) ?>">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="5"><?= htmlspecialchars($serie['notes']) ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary w-100">
        Enregistrer les modifications
    </button>

</form>

<script>
$(function() {

    $('#editSeriesForm').on('submit', function(e) {
        e.preventDefault();

        $.post(
            "<?= $config['base_url'] ?>/index.php?route=gestion_series_update",
            $(this).serialize(),
            function(response) {

                if (response.success) {
                    alert("Série mise à jour !");
                    location.reload();
                } else {
                    alert(response.message);
                }

            },
            "json"
        );
    });

});
</script>
