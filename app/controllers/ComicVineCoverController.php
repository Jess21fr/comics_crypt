<?php

class ComicVineCoverController
{
    private string $apiKey;
    private string $baseUrl = "https://comicvine.gamespot.com/api";

    public function __construct()
    {
        $config = require __DIR__ . '/../Config/config.php';
        $this->apiKey = $config['comicvine_api_key'];
    }

    /**
     * Recherche la meilleure cover ComicVine pour une issue
     * GET /comicvine/cover?volume_id=XXX&number=YYY
     */
    public function search()
    {
        if (!isset($_GET['volume_id'], $_GET['number'])) {
            $this->jsonError("Paramètres manquants : volume_id, number");
            return;
        }

        $volumeId = (int) $_GET['volume_id'];
        $number   = $_GET['number'];

        // Requête ComicVine conforme à la documentation
        $url = $this->baseUrl
            . "/issues/?api_key={$this->apiKey}&format=json"
            . "&filter=volume:{$volumeId},number:{$number}"
            . "&field_list=id,issue_number,name,image,cover_date,volume"
            . "&limit=5";

        $json = $this->curl($url);

        if (!$json || !isset($json['results'])) {
            $this->jsonError("Aucun résultat ComicVine");
            return;
        }

        $results = $json['results'];

        if (empty($results)) {
            $this->jsonError("Aucune issue trouvée pour volume={$volumeId}, number={$number}");
            return;
        }

        // On prend la première issue (la plus pertinente)
        $issue = $results[0];

        $image = $issue['image']['original_url']
            ?? $issue['image']['super_url']
            ?? $issue['image']['medium_url']
            ?? $issue['image']['icon_url']
            ?? null;

        $this->jsonSuccess([
            "id"          => $issue['id'],
            "name"        => $issue['name'],
            "number"      => $issue['issue_number'],
            "cover_date"  => $issue['cover_date'],
            "volume"      => $issue['volume']['id'] ?? null,
            "image"       => $image
        ]);
    }

    /**
     * Téléchargement de la cover
     * POST /comicvine/cover/download
     */
    public function download()
    {
        if (!isset($_POST['url'], $_POST['issue_id'])) {
            $this->jsonError("Paramètres manquants : url, issue_id");
            return;
        }

        $url      = $_POST['url'];
        $issueId  = (int) $_POST['issue_id'];

        $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
        if (!$ext) $ext = "jpg";

        $filename = "issue_{$issueId}." . $ext;
        $path     = __DIR__ . "/../../public/covers/" . $filename;

        $img = file_get_contents($url);
        if (!$img) {
            $this->jsonError("Impossible de télécharger l'image ComicVine");
            return;
        }

        file_put_contents($path, $img);

        $this->jsonSuccess([
            "filename" => $filename,
            "url"      => "/comics_crypt/public/covers/" . $filename
        ]);
    }

    /* ============================================================
       OUTILS
    ============================================================ */

    private function curl(string $url)
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT      => "ComicsCrypt/1.0",
            CURLOPT_TIMEOUT        => 10
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    private function jsonError(string $msg)
    {
        echo json_encode([
            "success" => false,
            "message" => $msg
        ]);
    }

    private function jsonSuccess(array $data)
    {
        echo json_encode([
            "success" => true,
            "data"    => $data
        ]);
    }
}
