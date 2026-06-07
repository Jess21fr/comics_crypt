<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/menu.php';
?>

<div class="container mt-4">

    <h1 class="mb-4 text-white">Gérer les Issues</h1>

    <div class="card bg-dark text-white">
        <div class="card-body">

            <table id="issues_table" class="table table-dark table-striped" style="width:100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Titre</th>
                        <th>Série</th>
                        <th>Date VO</th>
                        <th>Pages</th>
                        <th>Prix</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($issues as $i): ?>
                        <tr>
                            <td><?= htmlspecialchars($i['number']) ?></td>
                            <td><?= htmlspecialchars($i['title']) ?></td>
                            <td><?= htmlspecialchars($i['series_name']) ?></td>
                            <td><?= htmlspecialchars($i['publication_date']) ?></td>
                            <td><?= htmlspecialchars($i['page_count']) ?></td>
                            <td><?= htmlspecialchars($i['price']) ?></td>

                            <td>
                                <a href="index.php?route=gestion_issues_edit&id=<?= $i['id'] ?>"
                                   class="btn btn-warning btn-sm">
                                    <i class="fa-solid fa-pen"></i>
                                </a>

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
        order: [[0, 'asc']],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
        }
    });

    /* ------------------------------
       Suppression AJAX
    --------------------------------- */
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {

            if (!confirm("Supprimer cette issue ?")) return;

            let id = e.target.closest('.delete-btn').dataset.id;

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
        }
    });

});
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
