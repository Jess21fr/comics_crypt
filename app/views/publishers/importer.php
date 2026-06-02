<?php
$config = require __DIR__ . '/../../Config/config.php';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/menu.php';

// Modèles
require_once __DIR__ . '/../../models/Langue.php';
$langueModel = new Langue();
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

        <table id="publishersTable" class="display table table-dark table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Pays</th>
                    <th>Année</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($publishers as $p): ?>
                    <?php
                        $name    = $p['name']       ?? '';
                        $country = $p['country']    ?? null;
                        $year    = $p['year_began'] ?? '';
                        $id      = $p['id']         ?? '';

                        // Récup langue
                        $langue = $langueModel->getByComicsOrgId($country);

                        if ($langue) {
                            if (!empty($langue['drapeau'])) {
                                $labelPays = '<img src="' . $config['base_url'] . '/assets/img/flags/' . $langue['drapeau'] . '" 
                                                alt="' . htmlspecialchars($langue['nom_court']) . '" 
                                                style="width:15px;height:10px;">';
                            } else {
                                $labelPays = htmlspecialchars($langue['nom_court']);
                            }
                        } else {
                            $labelPays = '-';
                        }
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($name) ?></td>
                        <td><?= $labelPays ?></td>
                        <td><?= $year !== '' ? htmlspecialchars($year) : '-' ?></td>
                        <td class="text-center">

                            <!-- Bouton infos -->
                            <button class="btn btn-sm btn-outline-info more-info"
                                    data-id="<?= htmlspecialchars($id) ?>"
                                    title="+ d'infos">
                                <i class="fa-solid fa-circle-info"></i>
                            </button>

                            <!-- Bouton ajouter -->
                            <button class="btn btn-sm btn-outline-success add-publisher"
                                    data-id="<?= htmlspecialchars($id) ?>"
                                    title="Ajouter">
                                <i class="fa-solid fa-plus"></i>
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
$(function() {

    // Activation DataTables
    $('#publishersTable').DataTable({
        pageLength: 25,
        order: [[0, 'asc']],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/fr-FR.json"
        }
    });

    // Bouton + d'infos
    $('.more-info').on('click', function() {
        let id = $(this).data('id');

        $('#infoModalBody').html("Chargement...");

        $.get("<?= $config['base_url'] ?>/index.php?route=gestion_editeurs_importer_info&id=" + encodeURIComponent(id),
            function(data) {
                $('#infoModalBody').html(data);
                new bootstrap.Modal(document.getElementById('infoModal')).show();
            }
        );
    });

    // Bouton Ajouter
    $('.add-publisher').on('click', function() {
        let id = $(this).data('id');

        $.post("<?= $config['base_url'] ?>/index.php?route=gestion_editeurs_importer_add",
            { id: id },
            function(response) {

                if (response.success) {
                    alert("Éditeur ajouté !");
                } else {
                    alert(response.message);
                }

            },
            "json"
        );
    });

});
</script>

<?php
require __DIR__ . '/../layouts/footer.php';
?>
