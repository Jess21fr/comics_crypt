<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/menu.php';
?>

<div class="container mt-4">

    <h1 class="mb-4 text-white">Importer les Issues</h1>

    <!-- ============================
         ÉTAPE 1 : CHOIX DE LA SÉRIE
    ============================ -->
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
    ============================ -->
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
    ============================ -->
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

            <div id="covers_preview_block" class="mt-4"></div>
        </div>
    </div>

</div>

<script>
console.log(">>> IMPORTER.PHP ACTIF <<<");

let dataIssues = [];        // stockage des issues
let currentSerieName = "";  // nom de la série sélectionnée

document.addEventListener("DOMContentLoaded", function() {

    // ============================
    // DataTable des séries
    // ============================
    new DataTable('#series_table', {
        pageLength: 25,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
        }
    });

    // ============================
    // Bouton IMPORTER (Étape 1)
    // ============================
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.import-btn')) return;

        let btn = e.target.closest('.import-btn');

        currentSerieName = btn.dataset.name;

        let name    = btn.dataset.name;
        let year    = btn.dataset.year;
        let count   = btn.dataset.count;
        let pub     = btn.dataset.pub;
        let country = btn.dataset.country;

        // URL JSON ISSUES (comics.org, ouvert dans un nouvel onglet)
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

        // URL JSON COVERS (comics.org, ouvert dans un nouvel onglet)
        let urlCovers = "https://www.comics.org/search/advanced/process/?" +
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

        window.open(urlCovers, "_blank");

        // Bouton jaune : mémoriser l’URL covers
        let btnCovers = document.getElementById('btn_open_covers_url');
        btnCovers.dataset.url = urlCovers;
        btnCovers.style.display = "block";

        // Afficher l’étape 2
        document.getElementById('step2').style.display = "block";
    });

    // ============================
    // Bouton JAUNE : ouvrir JSON covers
    // ============================
    document.getElementById('btn_open_covers_url').addEventListener('click', function () {
        if (this.dataset.url) {
            window.open(this.dataset.url, "_blank");
        }
    });

    // ============================
    // PRÉVISUALISATION DES ISSUES (ÉTAPE 2)
    // ============================
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
            dataIssues = data.issues;

            document.getElementById('step3').style.display = "block";
        });
    });

    // ============================
    // PRÉVISUALISATION DES COVERS (ÉTAPE 3)
    // ============================
    document.getElementById('btn_preview_covers').addEventListener('click', async function () {

        let json = document.getElementById('json_covers').value.trim();

        if (!json) {
            alert("Veuillez coller le JSON des covers.");
            return;
        }

        let res = await fetch("index.php?route=gestion_covers_preview", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "json=" + encodeURIComponent(json)
        });

        let data = await res.json();

        if (!data.success) {
            alert(data.message);
            return;
        }

        console.log("Covers reçues :", data.covers);

        // Index des issues par ID pour fusion
        let issuesIndex = {};
        dataIssues.forEach(i => {
            issuesIndex[i.id] = i;
        });

        // Construction du tableau HTML
        let html = `
            <table id="covers_table" class="table table-dark table-striped mt-3">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Série</th>
                        <th>Numéro</th>
                        <th>Date de mise en vente</th>
                        <th>ID Episode</th>
                        <th><input type="checkbox" id="check_all"></th>
                    </tr>
                </thead>
                <tbody>
        `;
        for (let c of data.covers) {

            let issue = issuesIndex[c.issue] || null;

            let serie  = currentSerieName;
            let number = issue ? issue.number       : "?";
            let date   = issue ? issue.on_sale_date : "";
            let idEp   = issue ? issue.id           : c.issue;

            // ============================
            // MINIATURE GOOGLE IMAGES DIRECTE
            // ============================
            let googleQuery = encodeURIComponent(`"comics.org" "${c.issue}" "${c.id}"`);
            let thumbUrl    = `https://encrypted-tbn0.gstatic.com/images?q=${googleQuery}`;

            html += `
                <tr>
                    <td>
                        <img src="${thumbUrl}"
                             height="120"
                             onerror="this.src='https://via.placeholder.com/80x120?text=Cover';">
                    </td>
                    <td>${serie}</td>
                    <td>${number}</td>
                    <td>${date}</td>
                    <td>${idEp}</td>
                    <td><input type="checkbox" class="check_cover" data-cover='${JSON.stringify(c)}'></td>
                </tr>
            `;
        }

        html += `
                </tbody>
            </table>

            <button id="btn_import_selected" class="btn btn-success mt-3">
                Importer sélectionnés
            </button>

            <button id="btn_import_all" class="btn btn-danger mt-3 ms-2">
                Importer tout
            </button>
        `;

        document.getElementById('covers_preview_block').innerHTML = html;

        // ============================
        // Activation DataTable
        // ============================
        new DataTable('#covers_table', {
            pageLength: 25,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            }
        });

        // ============================
        // CHECKBOX "TOUT SÉLECTIONNER"
        // ============================
        document.getElementById('check_all').addEventListener('change', function () {
            let checked = this.checked;
            document.querySelectorAll('.check_cover').forEach(chk => {
                chk.checked = checked;
            });
        });

        // ============================
        // IMPORTER SÉLECTIONNÉS
        // ============================
        document.getElementById('btn_import_selected').addEventListener('click', function () {

            let selected = document.querySelectorAll('.check_cover:checked');

            if (!selected.length) {
                alert("Aucune cover sélectionnée.");
                return;
            }

            selected.forEach(chk => {
                let c = JSON.parse(chk.dataset.cover);
                importCover(c);
            });
        });

        // ============================
        // IMPORTER TOUT
        // ============================
        document.getElementById('btn_import_all').addEventListener('click', function () {

            let all = document.querySelectorAll('.check_cover');

            if (!all.length) {
                alert("Aucune cover à importer.");
                return;
            }

            all.forEach(chk => {
                let c = JSON.parse(chk.dataset.cover);
                importCover(c);
            });
        });

    }); // fin listener btn_preview_covers

    // ============================
    // FONCTION IMPORT D’UNE COVER
    // ============================
    function importCover(c) {

        fetch("index.php?route=gestion_issues_add_cover", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "cover=" + encodeURIComponent(JSON.stringify(c))
        })
        .then(r => r.json())
        .then(res => {
            alert(res.message);
        });
    }

}); // fin DOMContentLoaded
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
