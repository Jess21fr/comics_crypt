<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/menu.php';
?>

<div class="container mt-4">

    <h1 class="mb-4 text-white">Gérer les Issues</h1>

    <div class="card bg-dark text-white">
        <div class="card-body">

            <table id="issues_table" class="table table-dark table-striped align-middle" style="width:100%;">
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>#</th>
                        <th>Titre</th>
                        <th>Série</th>
                        <th>Date VO</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($issues as $i): ?>
                        <tr>

                            <!-- Cover -->
                            <td>
                                <?php if (!empty($i['cover_local'])): ?>
                                    <img src="/comics_crypt/public/covers/<?= htmlspecialchars($i['cover_local']) ?>?t=<?= time() ?>"
                                         style="width:55px; height:80px; object-fit:cover; border:1px solid #444;">
                                <?php else: ?>
                                    <div style="width:55px;height:80px;background:#333;color:#aaa;
                                                display:flex;align-items:center;justify-content:center;
                                                font-size:11px;border:1px solid #444;">
                                        no cover
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($i['number']) ?></td>
                            <td><?= htmlspecialchars($i['title']) ?></td>
                            <td><?= htmlspecialchars($i['series_name']) ?></td>
                            <td><?= htmlspecialchars($i['on_sale_date']) ?></td>

                            <td class="text-center">

                                <!-- Modifier -->
                                <a href="index.php?route=gestion_issues_edit&id=<?= $i['id'] ?>"
                                   class="btn btn-warning btn-sm">
                                    <i class="fa-solid fa-pen"></i>
                                </a>

                                <!-- Supprimer -->
                                <button class="btn btn-danger btn-sm delete-btn"
                                        data-id="<?= $i['id'] ?>">
                                    <i class="fa-solid fa-trash"></i>
                                </button>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    new DataTable('#issues_table', {
        pageLength: 25,
        order: [[1, 'asc']],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
        }
    });

    /* ------------------------------
       Suppression AJAX
    --------------------------------- */
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.delete-btn');
        if (!btn) return;

        if (!confirm("Supprimer cette issue ?")) return;

        let id = btn.dataset.id;

        fetch("index.php?route=gestion_issues_delete", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "id=" + encodeURIComponent(id)
        })
        .then(r => r.json())
        .then(res => {
            alert(res.message);
            if (res.success) location.reload();
        });
    });

});
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>