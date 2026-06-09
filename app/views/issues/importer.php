<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/menu.php';
?>

<div class="container mt-4">

    <h1 class="mb-4 text-white">Importer les Issues</h1>

    <!-- ============================
         ÉTAPE 1 : CHOIX DE LA SÉRIE
    ============================= -->
    <div class="card bg-dark text-white mb-4">
        <div class="card-body">
            <h4 class="text-info">Étape 1 — Choisir une série</h4>
            <p class="text-muted">Filtrez la série dans le tableau ci‑dessous puis cliquez sur <b>Importer</b>.</p>

            <table id="series_table" class="table table-dark table-striped" style="width:100%;">
                <thead>
                    <tr>
                        <th>Série</th>
                        <th>Année</th>
                        <th>Éditeur</th>
                        <th>Pays</th>
                        <th>Issues</th>
                        <th>Importer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($series as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['name']) ?></td>
                            <td><?= $s['year_began'] ?></td>
                            <td><?= htmlspecialchars($s['publisher_name']) ?></td>
                            <td><?= htmlspecialchars($s['country_code']) ?></td>
                            <td><?= $s['issue_count'] ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm import-btn"
                                    data-name="<?= htmlspecialchars($s['name']) ?>"
                                    data-year="<?= $s['year_began'] ?>"
                                    data-count="<?= $s['issue_count'] ?>"
                                    data-pub="<?= htmlspecialchars($s['publisher_name']) ?>"
                                    data-country="<?= strtolower($s['country_code']) ?>">
                                    Importer
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>

    <!-- ============================
         ÉTAPE 2 : COLLER JSON ISSUES
    ============================= -->
    <div id="step2" class="card bg-dark text-white mb-4" style="display:none;">
        <div class="card-body">
            <h4 class="text-info">Étape 2 — Coller le JSON des issues</h4>

            <textarea id="json_issues" class="form-control bg-black text-white" rows="10"
                      placeholder="Collez ici le JSON des issues Comics.org"></textarea>

            <button id="btn_preview_issues" class="btn btn-success mt-3 w-100">
                Prévisualiser les issues
            </button>
        </div>
    </div>

    <!-- ============================
         ÉTAPE 3 : COLLER JSON COVERS
    ============================= -->
    <div id="step3" class="card bg-dark text-white mb-4" style="display:none;">
        <div class="card-body">

            <h4 class="text-info">Étape 3 — Coller le JSON des covers</h4>

            <button id="btn_open_covers_url" class="btn btn-warning mb-3 w-100" style="display:none;">
                Ouvrir JSON Covers
            </button>

            <textarea id="json_covers" class="form-control bg-black text-white" rows="10"
                      placeholder="Collez ici le JSON des covers Comics.org"></textarea>

            <button id="btn_preview_covers" class="btn btn-warning mt-3 w-100">
                Prévisualiser les covers
            </button>

            <!-- ICI S’AFFICHERA LA DATATABLE DES COVERS -->
            <div id="covers_preview_block" class="mt-4"></div>

        </div>
    </div>

</div>

<script>
console.log(">>> IMPORTER.PHP ACTIF <<<");

document.addEventListener("DOMContentLoaded", function() {

    /* ============================
       DATATABLE DES SÉRIES
    ============================ */
    new DataTable('#series_table', {
        pageLength: 25,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
        }
    });

    /* ============================
       FONCTION URL JSON COVERS
    ============================ */
    function buildCoverJsonUrl(name, year, count, pub, country) {

        return "https://www.comics.org/search/advanced/process/?" +
            "target=cover" +
            "&method=icontains" +
            "&is_variant=False" +
            "&in_selected_collection=on" +
            "&order1=series" +
            "&order2=date" +
            "&pub_name=" + encodeURIComponent(pub) +
            "&country=" + encodeURIComponent(country) +
            "&series=" + encodeURIComponent(name) +
            "&series_year_began=" + year +
            "&issue_count=" + count +
            "&_export=db_json";
    }

    /* ============================
       BOUTON IMPORTER (ÉTAPE 1)
    ============================ */
    document.addEventListener('click', function(e) {
        if (e.target.closest('.import-btn')) {

            let btn = e.target.closest('.import-btn');

            let name = btn.dataset.name;
            let year = btn.dataset.year;
            let count = btn.dataset.count;
            let pub = btn.dataset.pub;
            let country = btn.dataset.country;

            /* URL JSON ISSUES */
            let urlIssues = "https://www.comics.org/search/advanced/process/?" +
                "target=issue" +
                "&method=icontains" +
                "&is_variant=False" +
                "&in_selected_collection=on" +
                "&order1=series" +
                "&order2=date" +
                "&pub_name=" + encodeURIComponent(pub) +
                "&country=" + encodeURIComponent(country) +
                "&series=" + encodeURIComponent(name) +
                "&series_year_began=" + year +
                "&issue_count=" + count +
                "&_export=db_json";

            window.open(urlIssues, "_blank");

            /* URL JSON COVERS */
            let urlCovers = buildCoverJsonUrl(name, year, count, pub, country);
            window.open(urlCovers, "_blank");

            let btnCovers = document.getElementById('btn_open_covers_url');
            btnCovers.dataset.url = urlCovers;
            btnCovers.style.display = "block";

            document.getElementById('step2').style.display = "block";
        }
    });

    /* ============================
       BOUTON JAUNE : OUVRIR JSON COVERS
    ============================ */
    document.getElementById('btn_open_covers_url').addEventListener('click', function () {
        window.open(this.dataset.url, "_blank");
    });

    /* ============================
       PRÉVISUALISATION DES ISSUES
    ============================ */
    document.getElementById('btn_preview_issues').addEventListener('click', function () {

        let json = document.getElementById('json_issues').value.trim();

        if (!json) {
            alert("Veuillez coller le JSON des issues.");
            return;
        }

        fetch("index.php?route=gestion_issues_preview", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "json=" + encodeURIComponent(json)
        })
        .then(r => r.json())
        .then(data => {

            if (!data.success) {
                alert(data.message);
                return;
            }

            console.log("Issues reçues :", data.issues);

            document.getElementById('step3').style.display = "block";
        });
    });

    /* ============================
       PRÉVISUALISATION DES COVERS
    ============================ */
    document.getElementById('btn_preview_covers').addEventListener('click', function () {

        let json = document.getElementById('json_covers').value.trim();

        if (!json) {
            alert("Veuillez coller le JSON des covers.");
            return;
        }

        fetch("index.php?route=gestion_covers_preview", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "json=" + encodeURIComponent(json)
        })
        .then(r => r.json())
        .then(data => {

            if (!data.success) {
                alert(data.message);
                return;
            }

            console.log("Covers reçues :", data.covers);

            /* ============================
               AFFICHAGE DATATABLE DES COVERS
            ============================ */

            let html = `
                <table id="covers_table" class="table table-dark table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Cover ID</th>
                            <th>Issue ID</th>
                            <th>Image</th>
                            <th>Importer</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.covers.forEach(c => {

                let folder = Math.floor(c.id / 1000);
                let url = `https://files1.comics.org/img/gcd/covers_by_id/${folder}/w200/${c.id}.jpg`;

                html += `
                    <tr>
                        <td>${c.id}</td>
                        <td>${c.issue}</td>
                        <td><img src="${url}" height="120"></td>
                        <td>
                            <button class="btn btn-success btn-sm import-cover-btn"
                                    data-cover='${JSON.stringify(c)}'>
                                Importer
                            </button>
                        </td>
                    </tr>
                `;
            });

            html += `</tbody></table>`;

            document.getElementById('covers_preview_block').innerHTML = html;

            new DataTable('#covers_table', {
                pageLength: 25,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
                }
            });
        });
    });

    /* ============================
       IMPORT D’UNE COVER
    ============================ */
    document.addEventListener('click', function(e) {
        if (e.target.closest('.import-cover-btn')) {

            let c = JSON.parse(e.target.closest('.import-cover-btn').dataset.cover);

            fetch("index.php?route=gestion_issues_add_cover", {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "cover=" + encodeURIComponent(JSON.stringify(c))
            })
            .then(r => r.json())
            .then(res => {
                alert(res.message);
            });
        }
    });

});
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
