<?php
$config = require __DIR__ . '/../../Config/config.php';
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/menu.php';
?>

<div class="container mt-5 pt-4">

    <h1 class="mb-4 text-light">Gérer les Éditeurs</h1>

    <table id="publishersTable" class="table table-dark table-striped table-bordered align-middle">
        <thead>
            <tr>
                <th>Logo</th>
                <th>Nom</th>
                <th>ID ComicVine</th>
                <th>Actif</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($publishers as $p): ?>
                <tr data-id="<?= $p['id'] ?>"
                    data-publisher_id="<?= $p['publisher_id'] ?>"
                    data-name="<?= htmlspecialchars($p['name']) ?>"
                    data-logo="<?= htmlspecialchars($p['logo']) ?>"
                    data-actif="<?= $p['actif'] ?>">

                    <td>
                        <?php if ($p['logo']): ?>
                            <img src="<?= $config['base_url'] ?>/logos/<?= $p['logo'] ?>"
                                 style="width:50px;height:50px;object-fit:contain;">
                        <?php else: ?>
                            <img src="https://placehold.co/50x50/1a1a1a/666?text=No+Img">
                        <?php endif; ?>
                    </td>

                    <td class="fw-bold text-warning"><?= htmlspecialchars($p['name']) ?></td>
                    <td><code><?= $p['publisher_id'] ?></code></td>

                    <td>
                        <?php if ($p['actif']): ?>
                            <span class="badge bg-success">Actif</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactif</span>
                        <?php endif; ?>
                    </td>

                    <td class="text-center">
                        <button class="btn btn-sm btn-primary btnEdit">
                            <i class="fa fa-pen"></i>
                        </button>
                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

<!-- MODALE EDITION -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-light">

            <div class="modal-header">
                <h5 class="modal-title">Modifier l’éditeur</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="edit_id">

                <div class="mb-3">
                    <label class="form-label">Nom</label>
                    <input type="text" id="edit_name" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Logo actuel</label><br>
                    <img id="edit_logo_preview" src="" style="width:80px;height:80px;object-fit:contain;">
                </div>

                <div class="mb-3">
                    <label class="form-label">Nouveau logo (optionnel)</label>
                    <input type="file" id="edit_logo" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Actif</label>
                    <select id="edit_actif" class="form-select">
                        <option value="1">Actif</option>
                        <option value="0">Inactif</option>
                    </select>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button class="btn btn-success" id="btnSaveEdit">Enregistrer</button>
            </div>

        </div>
    </div>
</div>

<!-- Toast -->
<div id="toastMsg" 
     style="position:fixed;bottom:20px;right:20px;z-index:9999;
            background:#28a745;color:white;padding:12px 20px;
            border-radius:6px;display:none;font-weight:bold;">
</div>

<script>
function showToast(msg, color="#28a745") {
    let t = $('#toastMsg');
    t.css("background", color);
    t.text(msg).fadeIn(200);
    setTimeout(() => t.fadeOut(400), 3000);
}

$(function() {

    $('#publishersTable').DataTable({
        pageLength: 20,
        order: [[1, 'asc']],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/fr-FR.json"
        }
    });

    /* ============================================================
       OUVERTURE MODALE
    ============================================================ */
    $('.btnEdit').on('click', function() {

        let tr = $(this).closest('tr');

        $('#edit_id').val(tr.data('id'));
        $('#edit_name').val(tr.data('name'));
        $('#edit_actif').val(tr.data('actif'));

        let logo = tr.data('logo');
        if (logo) {
            $('#edit_logo_preview').attr('src', "<?= $config['base_url'] ?>/logos/" + logo);
        } else {
            $('#edit_logo_preview').attr('src', "https://placehold.co/80x80/1a1a1a/666?text=No+Img");
        }

        new bootstrap.Modal(document.getElementById('editModal')).show();
    });

    /* ============================================================
       SAUVEGARDE MODIFICATIONS
    ============================================================ */
    $('#btnSaveEdit').on('click', function() {

        let formData = new FormData();
        formData.append("id", $('#edit_id').val());
        formData.append("name", $('#edit_name').val());
        formData.append("actif", $('#edit_actif').val());

        let file = $('#edit_logo')[0].files[0];
        if (file) formData.append("logo", file);

        $.ajax({
            url: "<?= $config['base_url'] ?>/index.php?route=gestion_editeurs_update",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(resp) {
                if (resp.success) {
                    showToast("Modifications enregistrées !");
                    location.reload();
                } else {
                    showToast(resp.message, "#c0392b");
                }
            }
        });

    });

});
</script>

<?php
require __DIR__ . '/../layouts/footer.php';
?>
