<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/menu.php';
?>

<div class="container mt-4 text-white">

    <h1 class="mb-4">Importer les Issues</h1>

    <!-- FILTRE ÉDITEUR -->
    <div class="card bg-dark mb-4">
        <div class="card-body">
            <label class="form-label text-white">Éditeur</label>
            <select id="filterPublisher" class="form-select bg-dark text-white">
                <option value="">Tous les éditeurs</option>
                <?php foreach ($publishers as $p): ?>
                    <option value="<?= $p['publisher_id'] ?>">
                        <?= htmlspecialchars($p['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- TABLEAU DES SÉRIES (DATATABLE) -->
    <div class="card bg-dark mb-4">
        <div class="card-body">
            <table id="seriesTable" class="table table-dark table-striped align-middle">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Éditeur</th>
                    <th>Début</th>
                    <th>Type</th>
                    <th>Issues</th>
                    <th class="text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($series as $s): ?>

                    <?php
                        $pubName    = strtolower($s['publisher_name']);
                        $country    = strtolower($s['country_code']);
                        $seriesName = strtolower($s['name']);
                        $year       = $s['year_began'];

                        $url = 'https://www.comics.org/search/advanced/process/?target=issue'
                             . '&method=icontains'
                             . '&is_variant=False'
                             . '&order1=series'
                             . '&order2=date'
                             . '&pub_name=' . urlencode($pubName)
                             . '&country=' . urlencode($country)
                             . '&series=' . urlencode($seriesName)
                             . '&series_year_began=' . urlencode($year)
                             . '&_export=db_json';
                    ?>

                    <tr data-publisher="<?= $s['publisher'] ?>">
                        <td><?= htmlspecialchars($s['name']) ?></td>
                        <td><?= htmlspecialchars($s['publisher_name']) ?></td>
                        <td><?= $s['year_began'] ?></td>
                        <td><?= htmlspecialchars($s['publishing_format']) ?></td>
                        <td><?= $s['issue_count'] ?></td>
                        <td class="text-center">
                            <a href="<?= $url ?>"
                               target="_blank"
                               class="btn btn-primary btn-sm btn-import-serie"
                               data-series-name="<?= htmlspecialchars($s['name'], ENT_QUOTES) ?>">
                                Importer série
                            </a>
                        </td>
                    </tr>

                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ZONE JSON -->
    <div class="card bg-dark mb-4">
        <div class="card-body">
            <h4>Étape 2 — Coller le JSON Comics.org</h4>
            <textarea id="jsonInput" class="form-control mt-3 bg-dark text-white" rows="10"
                      placeholder="Collez ici le JSON Comics.org"></textarea>
            <button id="btnPreview" class="btn btn-success mt-3">
                Prévisualiser les issues
            </button>
        </div>
    </div>

    <!-- ZONE ISSUES -->
    <div id="issuesContainer"></div>

</div>

<!-- DATATABLE + STYLES -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<style>
    .dataTables_wrapper .dataTables_filter label,
    .dataTables_wrapper .dataTables_length label {
        color: #fff;
    }
    .dataTables_wrapper .dataTables_filter input,
    .dataTables_wrapper .dataTables_length select {
        background-color: #333;
        color: #fff;
        border: 1px solid #555;
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
let currentSeriesName = '';

document.addEventListener("DOMContentLoaded", () => {

    /* ============================
       DATATABLE
    ============================ */
    const dt = new DataTable('#seriesTable', {
        paging: true,
        searching: true,
        info: false,
        order: [[0, 'asc']]
    });

    /* ============================
       FILTRE ÉDITEUR
    ============================ */
    const filterPublisher = document.getElementById("filterPublisher");

    filterPublisher.addEventListener("change", () => {
        const pubId = filterPublisher.value;

        dt.rows().every(function () {
            const tr = this.node();
            const rowPub = tr.dataset.publisher;

            tr.style.display = (!pubId || rowPub === pubId) ? "" : "none";
        });
    });

    /* ============================
       CAPTURER LE NOM DE LA SÉRIE
    ============================ */
    document.querySelectorAll('.btn-import-serie').forEach(btn => {
        btn.addEventListener('click', () => {
            currentSeriesName = btn.dataset.seriesName || '';
        });
    });

    /* ============================
       PRÉVISUALISATION ISSUES
    ============================ */
    document.getElementById("btnPreview").addEventListener("click", () => {

        const json = document.getElementById("jsonInput").value.trim();
        if (!json) {
            alert("Veuillez coller le JSON Comics.org.");
            return;
        }

        fetch("index.php?route=gestion_issues_preview", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "json=" + encodeURIComponent(json)
        })
        .then(r => r.json())
        .then(res => {

            if (!res.success) {
                alert(res.message);
                return;
            }

            const issues = res.issues;

            let html = `
            <div class="card bg-dark mt-4">
                <div class="card-body">

                    <div class="d-flex justify-content-between mb-3">
                        <h4>Étape 3 — Sélectionner les issues à importer</h4>

                        <div>
                            <button id="importSelected" class="btn btn-success btn-sm me-2">
                                Importer la sélection
                            </button>
                            <button id="importAll" class="btn btn-primary btn-sm">
                                Tout importer
                            </button>
                        </div>
                    </div>

                    <table class="table table-dark table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>Série</th>
                                <th>#</th>
                                <th>Date VO</th>
                                <th>ID</th>
                                <th class="text-end">Sélection</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            issues.forEach(issue => {

                const img =
                    issue.comicvine_image
                    || (issue.image && (issue.image.original_url || issue.image.super_url || issue.image.medium_url))
                    || "/comics_crypt/public/assets/no_cover.png";

                const usDate = issue.on_sale_date || '';
                let frDate = '';
                if (usDate.includes('-')) {
                    const p = usDate.split('-');
                    frDate = `${p[2]}/${p[1]}/${p[0]}`;
                }

                const serieName =
                    currentSeriesName
                    || issue.series_name
                    || '—';

                html += `
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <img src="${img}" style="height:80px;max-width:55px;object-fit:cover;border:1px solid #444;">
                                <button class="btn btn-warning btn-sm p-1 change-cover"
                                        title="Changer la cover"
                                        data-id="${issue.id}"
                                        data-number="${issue.number}"
                                        data-volume="${issue.series_comicvine_volume_id || ''}">
                                    <i class="fa-solid fa-image" style="font-size:12px;"></i>
                                </button>
                            </div>
                        </td>

                        <td>${serieName}</td>
                        <td>${issue.number}</td>
                        <td data-order="${usDate}">${frDate}</td>
                        <td>${issue.id}</td>

                        <td class="text-end">
                            <input type="checkbox" class="chkIssue" value='${JSON.stringify(issue)}'>
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
            </div>
            `;

            document.getElementById("issuesContainer").innerHTML = html;

            /* ============================
               IMPORTER LA SÉLECTION
            ============================ */
            document.getElementById("importSelected").addEventListener("click", () => {
                const selected = [...document.querySelectorAll(".chkIssue:checked")];

                if (!selected.length) {
                    alert("Aucune issue sélectionnée.");
                    return;
                }

                selected.forEach(chk => importIssue(JSON.parse(chk.value)));
            });

            /* ============================
               IMPORTER TOUT
            ============================ */
            document.getElementById("importAll").addEventListener("click", () => {
                document.querySelectorAll(".chkIssue").forEach(chk => {
                    importIssue(JSON.parse(chk.value));
                });
            });

        });
    });

});

/* ============================
   IMPORT D’UNE ISSUE
============================ */
function importIssue(issue) {

    fetch("index.php?route=gestion_issues_importer_save", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "issue=" + encodeURIComponent(JSON.stringify(issue))
    })
    .then(r => r.json())
    .then(res => {
        console.log(res.message);
    });
}
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
