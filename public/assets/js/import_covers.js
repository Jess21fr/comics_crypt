// /public/assets/js/import_covers.js
console.log(">>> import_covers.js chargé <<<");

/**
 * Import Cloudflare-proof + CORS-proof + Tracking Prevention-proof
 * Fonctionne en 2 étapes :
 *
 * 1) Ouvre l'image dans un nouvel onglet → Cloudflare + CORS OK
 * 2) Recharge l'image depuis le cache → canvas OK → Base64 OK
 */
async function importCover(c) {

    const issueId = c.issue;
    const coverId = c.id;

    // 1) Construire l'URL Comics.org
    const folder = Math.floor(coverId / 1000);
    const url = `https://files1.comics.org/img/gcd/covers_by_id/${folder}/w400/${coverId}.jpg`;

    console.log("Import cover", coverId, "issue", issueId, "URL :", url);

    // 2) Ouvrir l'image dans un nouvel onglet pour passer Cloudflare + CORS
    window.open(url, "_blank");

    // 3) Attendre que le navigateur charge l'image et pose les cookies
    await new Promise(resolve => setTimeout(resolve, 600));

    try {
        // 4) Charger l'image depuis le cache (CORS bypass)
        const img = new Image();
        img.crossOrigin = "anonymous";
        img.src = url + "?cache=" + Date.now(); // forcer revalidation cache

        await new Promise((resolve, reject) => {
            img.onload = () => resolve();
            img.onerror = () => reject("Impossible de charger l'image " + url);
        });

        // 5) Canvas → Base64
        const canvas = document.createElement('canvas');
        const ctx    = canvas.getContext('2d');

        canvas.width  = img.width;
        canvas.height = img.height;

        ctx.drawImage(img, 0, 0);

        const base64 = canvas.toDataURL("image/jpeg");

        // 6) Envoi au serveur
        const formData = new FormData();
        formData.append("issue_id", issueId);
        formData.append("cover_id", coverId);
        formData.append("base64", base64);

        const res  = await fetch("save_cover.php", {
            method: "POST",
            body: formData
        });

        const json = await res.json();

        console.log("Résultat import cover", json);

        if (!json.success) {
            alert(json.message || "Erreur lors de l'import");
            return;
        }

        alert(`Cover ${coverId} importée avec succès`);

        // 7) Mise à jour miniature locale
        const imgTag = document.querySelector(`#cover-${issueId}-${coverId}`);
        if (imgTag) {
            imgTag.src = json.thumb + "?t=" + Date.now();
        }

    } catch (e) {
        console.error(e);
        alert("Erreur lors de l'import de la cover " + coverId);
    }
}
