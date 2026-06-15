<?php
$config = require __DIR__ . '/../../Config/config.php';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/menu.php';
?>

<div class="container mt-5 pt-4">

    <h1 class="mb-4 text-light">Gérer les Séries</h1>

    <table id="seriesTable" class="display table table-dark table-striped table-bordered align-middle">
        <thead>
            <tr>
                <th>Logo</th>
                <th>Nom</th>
                <th>Éditeur</th>
                <th>Année début</th>
                <th>Nb. issues</th>
                <th>Series ID</th>
                <th>Actif</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($series as $s): ?>
                <?php
                    $logo = $s['logo']
                        ? $config['base_url'] . "/series/" . $s['logo']
                        : "https://placehold.co/60x90/1a1a1a/666?text=No+Img";

                    $publisherLogo = !empty($s['publisher_logo'])
                        ? $config['base_url'] . "/publishers/" . $s['publisher_logo']
                        : null;

                    $labelEditeur = !empty($s['publisher_name'])
                        ? htmlspecialchars($s['publisher_name'])
                        : '-';

                    $anneeDebut = $s['start_year'] ? htmlspecialchars($s['start_year']) : '-';
                    $nbIssues   = $s['count_of_issues'] !== null ? (int)$s['count_of_issues'] : 0;
                    $cvId       = $s['series_id'] ?: '-';
                    $actif      = $s['actif'] ? "Oui" : "Non";
                ?>
                <tr>
                    <td><img src="<?= $logo ?>" style="width:60px;height:90px;object-fit:cover;"></td>
                    <td><?= htmlspecialchars($s['name']) ?></td>

                    <td>
                        <?php if ($publisherLogo): ?>
                            <img src="<?= $publisherLogo ?>" style="width:25px;height:25px;object-fit:contain;margin-right:5px;">
                        <?php endif; ?>
                        <?= $labelEditeur ?>
                    </td>

                    <td><?= $anneeDebut ?></td>
                    <td><?= $nbIssues ?></td>
                    <td><code><?= htmlspecialchars($cvId) ?></code></td>
                    <td><?= $actif ?></td>

                    <td class="text-center">

                        <button class="btn btn-sm btn-outline-info edit-series"
                                data-id="<?= $s['id'] ?>"
                                title="Modifier">
                            <i class="fa-solid fa-pen"></i>
                        </button>

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

<!-- 🟩 MODALE ÉDITION -->
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

<!-- 🟩 MODALE SUPPRESSION -->
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

    $('#seriesTable').DataTable({
        pageLength: 25,
        order: [[1, 'asc']],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/fr-FR.json"
        }
    });

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

    $('.delete-series').on('click', function() {
        let id = $(this).data('id');
        $('#deleteSeriesId').val(id);
        new bootstrap.Modal(document.getElementById('deleteSeriesModal')).show();
    });

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
