<?php
$config = require __DIR__ . '/../../Config/config.php';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/menu.php';

// Modèle langue (pour drapeaux)
require_once __DIR__ . '/../../models/Langue.php';
$langueModel = new Langue();
?>

<div class="container mt-5 pt-4">

    <h1 class="mb-4 text-light">Gérer les Séries</h1>

    <table id="seriesTable" class="display table table-dark table-striped table-bordered align-middle">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Éditeur</th>
                <th>Pays</th>
                <th>Année début</th>
                <th>Année fin</th>
                <th>Nb. issues</th>
                <th>ComicVine Volume ID</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($series as $s): ?>
                <?php
                    // Pays via table langue
                    $langue = $langueModel->getByComicsOrgId($s['country']);

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

                    $labelEditeur = !empty($s['publisher_name'])
                        ? htmlspecialchars($s['publisher_name'])
                        : '-';

                    $anneeDebut = $s['year_began'] ? htmlspecialchars($s['year_began']) : '-';
                    $anneeFin   = $s['year_ended'] ? htmlspecialchars($s['year_ended']) : '-';
                    $nbIssues   = $s['issue_count'] !== null ? (int)$s['issue_count'] : 0;
                    $cvVolume   = $s['comicvine_volume_id'] ?: '-';
                ?>
                <tr>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td><?= $labelEditeur ?></td>
                    <td><?= $labelPays ?></td>
                    <td><?= $anneeDebut ?></td>
                    <td><?= $anneeFin ?></td>
                    <td><?= $nbIssues ?></td>
                    <td><?= htmlspecialchars($cvVolume) ?></td>

                    <td class="text-center">

                        <!-- Bouton modifier -->
                        <button class="btn btn-sm btn-outline-info edit-series"
                                data-id="<?= $s['id'] ?>"
                                title="Modifier">
                            <i class="fa-solid fa-pen"></i>
                        </button>

                        <!-- Bouton supprimer -->
                        <button class="btn btn-sm btn-outline-danger delete-series"
                                data-id="<?= $s['id'] ?>"
                                title="Supprimer">
                            <i class="fa-solid fa-trash"></i>
                        </button>

                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

<!-- MODALE ÉDITION -->
<div class="modal fade" id="editSeriesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5 class="modal-title">Modifier la Série</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="editSeriesModalBody">
                Chargement...
            </div>
        </div>
    </div>
</div>

<!-- MODALE SUPPRESSION -->
<div class="modal fade" id="deleteSeriesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5 class="modal-title">Supprimer la Série</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>Voulez-vous vraiment supprimer cette série ?</p>
                <input type="hidden" id="deleteSeriesId">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteSeries">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {

    // Activation DataTables
    $('#seriesTable').DataTable({
        pageLength: 25,
        order: [[0, 'asc']],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/fr-FR.json"
        }
    });

    // Bouton modifier
    $('.edit-series').on('click', function() {
        let id = $(this).data('id');

        $('#editSeriesModalBody').html("Chargement...");

        $.get("index.php?route=gestion_series_edit&id=" + encodeURIComponent(id),
            function(data) {
                $('#editSeriesModalBody').html(data);
                new bootstrap.Modal(document.getElementById('editSeriesModal')).show();
            }
        );
    });

    // Bouton supprimer → ouverture modale
    $('.delete-series').on('click', function() {
        let id = $(this).data('id');
        $('#deleteSeriesId').val(id);
        new bootstrap.Modal(document.getElementById('deleteSeriesModal')).show();
    });

    // Confirmation suppression
    $('#confirmDeleteSeries').on('click', function() {
        let id = $('#deleteSeriesId').val();

        $.post("index.php?route=gestion_series_delete",
            { id: id },
            function(response) {
                location.reload();
            },
            "json"
        );
    });

});
</script>

<?php
require __DIR__ . '/../layouts/footer.php';
?>