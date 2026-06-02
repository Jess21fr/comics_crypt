<?php
$config = require __DIR__ . '/../../Config/config.php';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/menu.php';

// Modèles
require_once __DIR__ . '/../../models/Langue.php';
$langueModel = new Langue();
?>

<div class="container mt-5 pt-4">

    <h1 class="mb-4 text-light">Gérer les Éditeurs</h1>

    <table id="publishersTable" class="display table table-dark table-striped table-bordered align-middle">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Pays</th>
                <th>Année</th>
                <th>Actif</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($publishers as $p): ?>
                <?php
                    $langue = $langueModel->getByComicsOrgId($p['country']);

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

                    $badgeActif = $p['actif']
                        ? '<span class="badge bg-success">Actif</span>'
                        : '<span class="badge bg-secondary">Inactif</span>';
                ?>
                <tr>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= $labelPays ?></td>
                    <td><?= $p['year_began'] ? htmlspecialchars($p['year_began']) : '-' ?></td>
                    <td><?= $badgeActif ?></td>

                    <td class="text-center">

                        <!-- Bouton activer/désactiver -->
                        <button class="btn btn-sm btn-outline-warning toggle-activite"
                                data-id="<?= $p['id'] ?>"
                                title="Activer / Désactiver">
                            <i class="fa-solid fa-power-off"></i>
                        </button>

                        <!-- Bouton modifier -->
                        <button class="btn btn-sm btn-outline-info edit-publisher"
                                data-id="<?= $p['id'] ?>"
                                title="Modifier">
                            <i class="fa-solid fa-pen"></i>
                        </button>

                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
<!-- MODALE ÉDITION -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5 class="modal-title">Modifier l'Éditeur</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="editModalBody">
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

    // Toggle actif
    $('.toggle-activite').on('click', function() {
        let id = $(this).data('id');

        $.post("<?= $config['base_url'] ?>/index.php?route=gestion_editeurs_toggle",
            { id: id },
            function(response) {
                alert(response.message);
                location.reload();
            },
            "json"
        );
    });

    // Bouton modifier
    $('.edit-publisher').on('click', function() {
        let id = $(this).data('id');

        $('#editModalBody').html("Chargement...");

        $.get("<?= $config['base_url'] ?>/index.php?route=gestion_editeurs_edit&id=" + encodeURIComponent(id),
            function(data) {
                $('#editModalBody').html(data);
                new bootstrap.Modal(document.getElementById('editModal')).show();
            }
        );
    });

});
</script>

<?php
require __DIR__ . '/../layouts/footer.php';
?>