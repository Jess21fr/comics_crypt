<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/menu.php';
?>

<div class="container mt-4">

    <h1 class="mb-4 text-white">Importer les Covers</h1>

    <div class="card bg-dark text-white">
        <div class="card-body">

            <h5 class="text-info">Étape 1 — Coller le JSON des covers</h5>

            <textarea id="json_input" class="form-control bg-black text-white" rows="10"
                      placeholder="Collez ici le JSON des covers Comics.org"></textarea>

            <button id="btn_preview" class="btn btn-primary mt-3 w-100">
                Prévisualiser les covers
            </button>

        </div>
    </div>

    <div id="preview_block" class="mt-4" style="display:none;">

        <h3 class="text-white mb-3">Prévisualisation</h3>

        <table id="covers_table" class="table table-dark table-striped" style="width:100%;">
            <thead>
                <tr>
                    <th>Cover ID</th>
                    <th>Issue ID</th>
                    <th>Image</th>
                    <th>Importer</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

    </div>

</div>

<!-- Modal -->
<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white">

            <div class="modal-header">
                <h5 class="modal-title">Importer la cover</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p id="modal_info"></p>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>

        </div>
    </div>
</div>

<script>
let coversData = [];

document.getElementById('btn_preview').addEventListener('click', function() {

    let json = document.getElementById('json_input').value.trim();
    if (!json) {
        alert("Veuillez coller le JSON des covers.");
        return;
    }

    try {
        coversData = JSON.parse(json);
    } catch (e) {
        alert("JSON invalide.");
        return;
    }

    let tbody = document.querySelector('#covers_table tbody');
    tbody.innerHTML = "";

    coversData.forEach(c => {

        let folder = Math.floor(c.id / 1000);
        let url = `https://files1.comics.org/img/gcd/covers_by_id/${folder}/w200/${c.id}.jpg`;

        tbody.innerHTML += `
            <tr>
                <td>${c.id}</td>
                <td>${c.issue}</td>
                <td><img src="${url}" height="120"></td>
                <td>
                    <button class="btn btn-success btn-sm import-btn"
                            data-cover='${JSON.stringify(c)}'>
                        Importer
                    </button>
                </td>
            </tr>
        `;
    });

    document.getElementById('preview_block').style.display = "block";

    new DataTable('#covers_table', {
        pageLength: 25,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
        }
    });
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.import-btn')) {

        let c = JSON.parse(e.target.closest('.import-btn').dataset.cover);

        fetch("index.php?route=gestion_issues_add_cover", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "cover=" + encodeURIComponent(JSON.stringify(c))
        })
        .then(r => r.json())
        .then(res => {

            document.getElementById('modal_info').innerHTML = res.message;
            new bootstrap.Modal(document.getElementById('modalImport')).show();

        });
    }
});
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
