<?php
// Variables fournies par le controller :
// $publisher  → données de l’éditeur
// $langues    → liste des langues
?>

<form id="editPublisherForm">

    <input type="hidden" name="id" value="<?= htmlspecialchars($publisher['id']) ?>">

    <div class="mb-3">
        <label class="form-label">Nom</label>
        <input type="text" name="name" class="form-control"
               value="<?= htmlspecialchars($publisher['name']) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Pays</label>
        <select name="country" class="form-select">
            <option value="">-- Aucun --</option>
            <?php foreach ($langues as $l): ?>
                <option value="<?= $l['id_comicsorg'] ?>"
                    <?= ($publisher['country'] == $l['id_comicsorg']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($l['nom']) ?> (<?= htmlspecialchars($l['nom_court']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="row">
        <div class="col mb-3">
            <label class="form-label">Année début</label>
            <input type="number" name="year_began" class="form-control"
                   value="<?= htmlspecialchars($publisher['year_began']) ?>">
        </div>

        <div class="col mb-3">
            <label class="form-label">Année fin</label>
            <input type="number" name="year_ended" class="form-control"
                   value="<?= htmlspecialchars($publisher['year_ended']) ?>">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">URL</label>
        <input type="text" name="url" class="form-control"
               value="<?= htmlspecialchars($publisher['url']) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="5"><?= htmlspecialchars($publisher['notes']) ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary w-100">
        Enregistrer les modifications
    </button>

</form>

<script>
$(function() {

    $('#editPublisherForm').on('submit', function(e) {
        e.preventDefault();

        $.post(
            "<?= $config['base_url'] ?>/index.php?route=gestion_editeurs_update",
            $(this).serialize(),
            function(response) {

                if (response.success) {
                    alert("Éditeur mis à jour !");
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
