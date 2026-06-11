console.log(">>> issues_import.js chargé <<<");

let dataIssues = [];
let currentSerieName = "";

document.addEventListener("DOMContentLoaded", function() {

    /* ============================================================
       DATATABLE DES SÉRIES
    ============================================================ */
    new DataTable('#series_table', {
        pageLength: 25,
        autoWidth: false,
        language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json" }
    });

    /* ============================================================
       ÉTAPE 1 — CHOISIR UNE SÉRIE
    ============================================================ */
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.import-btn');
        if (!btn) return;

        currentSerieName = btn.dataset.name;

        const name    = btn.dataset.name;
        const year    = btn.dataset.year;
        const count   = btn.dataset.count;
        const pub     = btn.dataset.pub;
        const country = btn.dataset.country;

        const urlIssues = "https://www.comics.org/search/advanced/process/?" +
            "target=issue&method=icontains&is_variant=False&in_selected_collection=on" +
            "&order1=series&order2=date" +
            "&pub_name=" + encodeURIComponent(pub) +
            "&country=" + encodeURIComponent(country) +
            "&series=" + encodeURIComponent(name) +
            "&series_year_began=" + year +
            "&issue_count=" + count +
            "&_export=db_json";

        window.open(urlIssues, "_blank");

        document.getElementById('step2').style.display = "block";
    });

    /* ============================================================
       ÉTAPE 2 — PRÉVISUALISATION DES ISSUES
    ============================================================ */
    document.getElementById('btn_preview_issues').addEventListener('click', async function () {

        const json = document.getElementById('json_issues').value.trim();
        if (!json) {
            alert("Veuillez coller le JSON des issues.");
            return;
        }

        const res = await fetch("index.php?route=gestion_issues_preview", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "json=" + encodeURIComponent(json)
        });

        const data = await res.json();
        if (!data.success) {
            alert(data.message || "Erreur lors de la prévisualisation des issues.");
            return;
        }

        dataIssues = data.issues;

        buildIssuesTable(data.issues);
        document.getElementById('step3').style.display = "block";
    });

    /* ============================================================
       IMPORT SÉLECTIONNÉS
    ============================================================ */
    document.getElementById('btn_import_selected').addEventListener('click', async function () {
        const selected = document.querySelectorAll('.check_issue:checked');
        if (!selected.length) {
            alert("Aucune issue sélectionnée.");
            return;
        }

        for (let chk of selected) {
            const issue = JSON.parse(chk.dataset.issue);
            await importIssue(issue);
        }

        alert("Import des issues sélectionnées terminé.");
    });

    /* ============================================================
       IMPORT TOUT
    ============================================================ */
    document.getElementById('btn_import_all').addEventListener('click', async function () {
        const all = document.querySelectorAll('.check_issue');
        if (!all.length) {
            alert("Aucune issue à importer.");
            return;
        }

        for (let chk of all) {
            const issue = JSON.parse(chk.dataset.issue);
            await importIssue(issue);
        }

        alert("Import de toutes les issues terminé.");
    });

});
/* ============================================================
   CONSTRUCTION DU TABLEAU D'IMPORT
============================================================ */
function buildIssuesTable(issues) {

    let html = `
        <table id="issues_table" class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Série</th>
                    <th>Numéro</th>
                    <th>Date de mise en vente</th>
                    <th>ID Episode</th>
                    <th>Cover</th>
                    <th><input type="checkbox" id="check_all_issues"></th>
                </tr>
            </thead>
            <tbody>
    `;

    for (let i of issues) {

        const idEp   = i.id;
        const serie  = currentSerieName;
        const number = i.number || "?";
        const date   = i.on_sale_date || "";

        const issueJson = JSON.stringify(i).replace(/"/g, '&quot;');

        html += `
            <tr>
                <td>
                    <div id="cover-placeholder-${idEp}" class="placeholder-cover">
                        pas de cover
                    </div>
                </td>
                <td>${serie}</td>
                <td>${number}</td>
                <td>${date}</td>
                <td>${idEp}</td>
                <td>
                    <button class="btn btn-sm btn-info btn_search_cover"
                            data-issue-id="${idEp}"
                            data-number="${number}">
                        Sélectionner une cover
                    </button>
                </td>
                <td>
                    <input type="checkbox" class="check_issue" data-issue="${issueJson}">
                </td>
            </tr>
        `;
    }

    html += `
            </tbody>
        </table>
    `;

    const block = document.getElementById('issues_preview_block');
    block.innerHTML = html;

    new DataTable('#issues_table', {
        pageLength: 25,
        autoWidth: false,
        deferRender: true,
        language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json" }
    });

    document.getElementById('check_all_issues').addEventListener('change', function () {
        document.querySelectorAll('.check_issue').forEach(chk => chk.checked = this.checked);
    });

    /* ============================================================
       BOUTON "SÉLECTIONNER UNE COVER" — VERSION COMICVINE
    ============================================================ */
    document.addEventListener('click', async function(e) {
        const btn = e.target.closest('.btn_search_cover');
        if (!btn) return;

        const issueId = btn.dataset.issueId;
        const number  = btn.dataset.number;

        // Appel ComicVine
        const res = await fetch("index.php?route=comicvine_search_issue", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "issue_number=" + encodeURIComponent(number)
        });

        const data = await res.json();
        if (!data.success) {
            alert(data.message || "Erreur ComicVine.");
            return;
        }

        // Si aucune image
        if (!data.results || !data.results.length) {
            alert("Aucune cover trouvée sur ComicVine.");
            return;
        }

        // On prend la meilleure image
        const imageUrl = data.results[0].image.original_url;

        // Téléchargement + redimensionnement
        const res2 = await fetch("index.php?route=comicvine_download_cover", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "issue_id=" + encodeURIComponent(issueId) +
                  "&url=" + encodeURIComponent(imageUrl)
        });

        const data2 = await res2.json();
        if (!data2.success) {
            alert(data2.message || "Erreur lors du téléchargement de la cover.");
            return;
        }

        // Mise à jour du placeholder
        const placeholder = document.querySelector(`#cover-placeholder-${issueId}`);
        if (placeholder) {
            placeholder.innerHTML = `
                <img src="/comics_crypt/public/covers/${issueId}.jpg?t=${Date.now()}"
                     width="80" height="120">
            `;
        }
    });

}

/* ============================================================
   IMPORT D'UNE ISSUE EN BDD
============================================================ */
async function importIssue(issue) {

    const res = await fetch("index.php?route=gestion_issues_importer_save", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "issue=" + encodeURIComponent(JSON.stringify(issue))
    });

    const data = await res.json();
    if (!data.success) {
        alert(data.message || "Erreur lors de l'import de l'issue " + issue.id);
        return;
    }
}
