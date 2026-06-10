<?php

class ComicVineCoverController
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $config = require __DIR__ . '/../config.php';
        $this->apiKey  = $config['comicvine_api_key'];
        $this->baseUrl = $config['comicvine_base_url'];
    }

    /* ============================================================
       1) Recherche d'une issue ComicVine par volume + numéro
    ============================================================ */
    public function searchIssue()
    {
        $volumeId = $_POST['volume_id'] ?? null;
        $number   = $_POST['number'] ?? null;

        if (!$volumeId || !$number) {
            echo json_encode([
                'success' => false,
                'message' => 'Paramètres manquants (volume_id, number)'
            ]);
            return;
        }

        $url = $this->baseUrl
            . "/issues/?api_key={$this->apiKey}&format=json"
            . "&filter=volume:{$volumeId},issue_number:{$number}"
            . "&limit=1";

        $json = $this->curlGet($url);

        if (!$json || empty($json['results'])) {
            echo json_encode([
                'success' => false,
                'message' => "Issue introuvable sur ComicVine"
            ]);
            return;
        }

        $issue = $json['results'][0];

        echo json_encode([
            'success' => true,
            'issue'   => [
                'id'    => $issue['id'],
                'name'  => $issue['name'],
                'image' => $issue['image'] ?? null
            ]
        ]);
    }

    /* ============================================================
       2) Téléchargement de la cover HD + thumbnail
    ============================================================ */
    public function downloadCover()
    {
        $issueId = $_POST['issue_id'] ?? null;
        $url     = $_POST['url'] ?? null;

        if (!$issueId || !$url) {
            echo json_encode([
                'success' => false,
                'message' => 'Paramètres manquants (issue_id, url)'
            ]);
            return;
        }

        $imgData = @file_get_contents($url);
        if (!$imgData) {
            echo json_encode([
                'success' => false,
                'message' => "Impossible de télécharger l'image ComicVine"
            ]);
            return;
        }

        $baseDir = __DIR__ . '/../../public/covers';
        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0777, true);
        }

        $fullPath  = $baseDir . "/{$issueId}.jpg";
        file_put_contents($fullPath, $imgData);

        // Génération thumbnail 400x619
        $thumbPath = $baseDir . "/{$issueId}_thumb.jpg";
        $ok = $this->resizeImage($fullPath, $thumbPath, 400, 619);

        if (!$ok) {
            echo json_encode([
                'success' => false,
                'message' => "Impossible de générer le thumbnail"
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'thumb'   => "/covers/{$issueId}_thumb.jpg"
        ]);
    }

    /* ============================================================
       CURL GET générique
    ============================================================ */
    private function curlGet(string $url): ?array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_USERAGENT      => 'ComicsCrypt/1.0'
        ]);

        $response = curl_exec($ch);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($err || !$response) {
            return null;
        }

        return json_decode($response, true);
    }

    /* ============================================================
       Redimensionnement image (JPEG only)
    ============================================================ */
    private function resizeImage(string $src, string $dest, int $width, int $height): bool
    {
        $info = getimagesize($src);
        if (!$info) return false;

        $mime = $info['mime'];
        $srcW = $info[0];
        $srcH = $info[1];

        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = @imagecreatefromjpeg($src);
                break;

            case 'image/png':
                $image = @imagecreatefrompng($src);
                break;

            case 'image/webp':
                if (!function_exists('imagecreatefromwebp')) return false;
                $image = @imagecreatefromwebp($src);
                break;

            default:
                return false;
        }

        if (!$image) return false;

        $dst = imagecreatetruecolor($width, $height);

        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);

        imagecopyresampled($dst, $image, 0, 0, 0, 0, $width, $height, $srcW, $srcH);

        $ok = imagejpeg($dst, $dest, 90);

        imagedestroy($image);
        imagedestroy($dst);

        return $ok;
    }
}
