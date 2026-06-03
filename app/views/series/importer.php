<?php
// Layouts
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/menu.php';

// Modèles
require_once __DIR__ . '/../../models/Publisher.php';
require_once __DIR__ . '/../../models/Series.php';

// Chargement des éditeurs actifs
$publisherModel = new Publisher();
$publishers = $publisherModel->getActivePublishers();
?>

<div class="container mt-4">

    <h1 class="mb-4 text-white">Import des Séries</h1>

    <!-- Sélection éditeur -->
    <div class="card bg-dark text-white mb-4">
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Éditeur</label>
                    <select id="publisher" class="form-select">
                        <option value="">-- Choisir un éditeur --</option>
                        <?php foreach ($publishers as $p): ?>
                            <option value="<?= strtolower(htmlspecialchars($p['name'])) ?>"
                                    data-country="<?= strtolower(htmlspecialchars($p['country_code'])) ?>">
                                <?= htmlspecialchars($p['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Filtrer par nom (optionnel)</label>
                    <input type="text" id="filter" class="form-control" placeholder="ex : avengers">
                </div>

                <div class="col-md-4">
                    <label class="form-label">URL Comics.org</label>
                    <input type="text" id="comics_url" class="form-control" readonly>
                </div>
            </div>

            <button id="open_comics" class="btn btn-primary">
                <i class="fa-solid fa-up-right-from-square"></i> Ouvrir Comics.org
            </button>

        </div>
    </div>

    <!-- Zone JSON -->
    <div class="card bg-dark text-white mb-4">
        <div class="card-body">
            <label class="form-label">Coller ici le JSON téléchargé</label>
            <textarea id="json_input" class="form-control" rows="10"></textarea>

            <button id="preview_json" class="btn btn-success mt-3">
                <i class="fa-solid fa-eye"></i> Prévisualiser
            </button>
        </div>
    </div>

    <!-- Résultats -->
    <div class="card bg-dark text-white">
        <div class="card-body">

            <h3 class="text-white">Résultats</h3>

            <table id="series_table" class="table table-dark table-striped" style="width:100%; display:none;">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Année début</th>
                        <th>Année fin</th>
                        <th>Épisodes</th>
                        <th>Format</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

        </div>
    </div>

</div>

<!-- Modale info -->
<div class="modal fade" id="modalInfo" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">Informations série</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalInfoBody">
                Chargement...
            </div>
        </div>
    </div>
</div>

<script>
/* ------------------------------
   Construction automatique de l’URL
--------------------------------- */
function updateURL() {
    let pub = document.getElementById('publisher').value;
    let country = document.querySelector('#publisher option:checked')?.dataset.country || '';
    let filter = document.getElementById('filter').value.trim().toLowerCase();

    if (!pub || !country) {
        document.getElementById('comics_url').value = "";
        return;
    }

    let url = "https://www.comics.org/search/advanced/process/?target=series"
            + "&method=icontains&order1=series&order2=date"
            + "&pub_name=" + encodeURIComponent(pub)
            + "&country=" + encodeURIComponent(country);

    if (filter !== "") {
        url += "&series=" + encodeURIComponent(filter);
    }

    url += "&_export=db_json";

    document.getElementById('comics_url').value = url;
}

document.getElementById('publisher').addEventListener('change', updateURL);
document.getElementById('filter').addEventListener('input', updateURL);

/* ------------------------------
   Ouvrir Comics.org
--------------------------------- */
document.getElementById('open_comics').addEventListener('click', function() {
    let url = document.getElementById('comics_url').value;
    if (!url) {
        alert("Veuillez sélectionner un éditeur.");
        return;
    }
    window.open(url, '_blank');
});

/* ------------------------------
   Prévisualisation JSON
--------------------------------- */
document.getElementById('preview_json').addEventListener('click', function() {

    let json = document.getElementById('json_input').value.trim();
    if (!json) {
        alert("Veuillez coller le JSON.");
        return;
    }

    fetch("index.php?route=gestion_series_preview", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "json=" + encodeURIComponent(json)
    })
    .then(r => r.json())
    .then(res => {

        if (!res.success) {
            alert(res.message);
            return;
        }

        // Stockage global du JSON pour la modale Info
        window._seriesList = res.series;

        let table = document.getElementById('series_table');
        let tbody = table.querySelector('tbody');
        tbody.innerHTML = "";

        res.series.forEach(s => {
            let tr = document.createElement('tr');

            tr.innerHTML = `
                <td>${s.name}</td>
                <td>${s.year_began ?? ''}</td>
                <td>${s.year_ended ?? ''}</td>
                <td>${s.issue_count ?? ''}</td>
                <td>${s.publishing_format ?? ''}</td>
                <td>
                    <button class="btn btn-info btn-sm info-btn" data-id="${s.id}">
                        <i class="fa-solid fa-circle-info"></i>
                    </button>
                    <button class="btn btn-success btn-sm import-btn" data-serie='${JSON.stringify(s)}'>
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);
        });

        table.style.display = "table";

        // Activation DataTable
        new DataTable('#series_table', {
            pageLength: 25,
            order: [[0, 'asc']],
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            }
        });
    });
});

/* ------------------------------
   Import d’une série
--------------------------------- */
document.addEventListener('click', function(e) {
    if (e.target.closest('.import-btn')) {

        let serie = e.target.closest('.import-btn').dataset.serie;

        fetch("index.php?route=gestion_series_ajax_add", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "serie=" + encodeURIComponent(serie)
        })
        .then(r => r.json())
        .then(res => {
            alert(res.message);
        });
    }
});

/* ------------------------------
   Info série (JSON, pas BDD)
--------------------------------- */
document.addEventListener('click', function(e) {
    if (e.target.closest('.info-btn')) {

        let id = e.target.closest('.info-btn').dataset.id;

        let s = window._seriesList.find(x => x.id == id);

        if (!s) {
            alert("Impossible de retrouver la série dans le JSON.");
            return;
        }

        document.getElementById('modalInfoBody').innerHTML = `
            <table class="table table-dark table-striped">
                <tr><th>Nom</th><td>${s.name}</td></tr>
                <tr><th>Années</th><td>${s.year_began} - ${s.year_ended}</td></tr>
                <tr><th>Épisodes</th><td>${s.issue_count}</td></tr>
                <tr><th>Format</th><td>${s.format ?? ''}</td></tr>
                <tr><th>Publishing format</th><td>${s.publishing_format ?? ''}</td></tr>
                <tr><th>Notes</th><td>${s.notes ?? ''}</td></tr>
            </table>
        `;

        new bootstrap.Modal(document.getElementById('modalInfo')).show();
    }
});
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
