let cvModal = null;

document.addEventListener("click", async (e) => {

    const btn = e.target.closest(".change-cover");
    if (!btn) return;

    const issueId = btn.dataset.id;
    const number  = btn.dataset.number;
    const volume  = btn.dataset.volume;

    if (!volume) {
        alert("Aucun volume ComicVine pour cette série.");
        return;
    }

    if (!cvModal) {
        cvModal = new bootstrap.Modal(document.getElementById("coverModal"));
    }

    document.getElementById("cvResults").innerHTML = "";
    document.getElementById("cvLoading").style.display = "block";

    cvModal.show();

    const url = `index.php?route=comicvine_cover_search&volume_id=${volume}&number=${number}`;
    const res = await fetch(url);
    const json = await res.json();

    document.getElementById("cvLoading").style.display = "none";

    if (!json.success) {
        document.getElementById("cvResults").innerHTML =
            `<p class="text-danger">${json.message}</p>`;
        return;
    }

    const img = json.data.image;

    document.getElementById("cvResults").innerHTML = `
        <img src="${img}" class="img-fluid border" style="max-height:350px;">
        <button class="btn btn-success mt-3" id="applyCover"
                data-url="${img}" data-id="${issueId}">
            Utiliser cette cover
        </button>
    `;
});

/* APPLIQUER LA COVER */
document.addEventListener("click", async (e) => {

    const btn = e.target.closest("#applyCover");
    if (!btn) return;

    const url = btn.dataset.url;
    const id  = btn.dataset.id;

    const form = new FormData();
    form.append("url", url);
    form.append("issue_id", id);

    const res = await fetch("index.php?route=comicvine_cover_download", {
        method: "POST",
        body: form
    });

    const json = await res.json();

    if (!json.success) {
        alert(json.message);
        return;
    }

    cvModal.hide();

    // Mise à jour immédiate dans le tableau
    const imgTag = document.querySelector(`button.change-cover[data-id="${id}"]`)
        .closest("td")
        .querySelector("img");

    imgTag.src = json.data.url + "?t=" + Date.now();
});
