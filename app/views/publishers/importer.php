<?php
$config = require __DIR__ . '/../../Config/config.php';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/menu.php';
?>

<div class="container mt-5 pt-4">

    <h1 class="mb-4 text-light">Importer des Éditeurs</h1>

    <div class="card bg-dark text-light mb-4">
        <div class="card-body">
            <form method="post" enctype="multipart/form-data"
                  action="<?= $config['base_url'] ?>/index.php?route=gestion_editeurs_importer">
                <label class="form-label">Charger un fichier JSON :</label>
                <input type="file" name="json_file" class="form-control mb-3" accept=".json" required>
                <button type="submit" class="btn btn-primary">Importer</button>
            </form>
        </div>
    </div>

    <?php if (!empty($publishers)) : ?>

        <table id="publisherTable" class="table table-dark table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Pays</th>
                    <th>Année</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($publishers as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= htmlspecialchars($p['country']) ?></td>
                        <td><?= htmlspecialchars($p['year']) ?></td>
                        <td>
                            <button class="btn btn-info btn-sm more-info"
                                    data-id="<?= htmlspecialchars($p['id']) ?>">
                                + d'infos
                            </button>

                            <button class="btn btn-success btn-sm add-publisher"
                                    data-id="<?= htmlspecialchars($p['id']) ?>">
                                Ajouter
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>

</div>

<div class="modal fade" id="infoModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5 class="modal-title">Informations éditeur</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="infoModalBody">
                Chargement...
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {

    $('#publisherTable').DataTable({
        pageLength: 25,
        order: [[0, 'asc']]
    });

    $('.more-info').on('click', function() {
        let id = $(this).data('id');

        $('#infoModalBody').html("Chargement...");

        $.get("<?= $config['base_url'] ?>/index.php?route=gestion_editeurs_importer_info&id=" + id,
            function(data) {
                $('#infoModalBody').html(data);
                let modal = new bootstrap.Modal(document.getElementById('infoModal'));
                modal.show();
            }
        );
    });

    $('.add-publisher').on('click', function() {
        let id = $(this).data('id');

        $.post("<?= $config['base_url'] ?>/index.php?route=gestion_editeurs_importer_add",
            { id: id },
            function(response) {
                alert(response.message);
            },
            "json"
        );
    });

});
</script>

<?php
require __DIR__ . '/../layouts/footer.php';
?>
