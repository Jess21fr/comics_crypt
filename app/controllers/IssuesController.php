<?php

require_once __DIR__ . '/../models/Issues.php';
require_once __DIR__ . '/../models/Series.php';

class IssuesController
{
    /* ============================================================
       PAGE IMPORTER
    ============================================================ */
    public function importer()
    {
        $seriesModel = new Series();
        $series = $seriesModel->getAllWithPublisherAndCountry();

        require __DIR__ . '/../views/issues/importer.php';
    }

    /* ============================================================
       PRÉVISUALISATION DES ISSUES (ÉTAPE 2)
    ============================================================ */
    public function preview()
    {
        if (!isset($_POST['json'])) {
            echo json_encode([
                "success" => false,
                "message" => "Aucun JSON reçu."
            ]);
            return;
        }

        $json = trim($_POST['json']);
        $data = json_decode($json, true);

        if (!$data) {
            echo json_encode([
                "success" => false,
                "message" => "JSON invalide."
            ]);
            return;
        }

        echo json_encode([
            "success" => true,
            "issues"  => $data
        ]);
    }

    /* ============================================================
       RECHERCHE COVER SUR LE WEB (SerpAPI / Google Images)
       FILTRAGE INTELLIGENT (A)
    ============================================================ */
    public function searchCoverWeb()
    {
        $query = $_POST['query'] ?? '';
        if (!$query) {
            echo json_encode(['success' => false, 'message' => 'Query vide']);
            return;
        }

        // Clé SerpAPI
        $apiKey = 'f9951f28bac54af5993116a57be35d0b3f49bbc45ebdd86f0bf5752ae513a3e7';

        $url = "https://serpapi.com/search.json?engine=google_images&q=" . urlencode($query) . "&api_key=" . urlencode($apiKey) . "&num=20";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);
        $response = curl_exec($ch);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($err || !$response) {
            echo json_encode([
                'success' => false,
                'message' => "Erreur appel SerpAPI : " . ($err ?: 'réponse vide')
            ]);
            return;
        }

        $json = json_decode($response, true);
        if (!$json || !isset($json['images_results'])) {
            echo json_encode([
                'success' => false,
                'message' => "Réponse SerpAPI invalide ou sans images_results"
            ]);
            return;
        }

        // Formats explicitement interdits
        $forbidden = [
            'image/svg+xml',
            'image/avif',
            'image/x-icon',
            'image/vnd.microsoft.icon',
            'image/tiff',
            'image/heic',
            'image/heif'
        ];

        $images = [];

        foreach ($json['images_results'] as $img) {

            $imgUrl = $img['original'] ?? ($img['thumbnail'] ?? '');
            if (!$imgUrl) continue;

            // HEAD request pour récupérer le MIME (si possible)
            $mime = $this->getRemoteMimeType($imgUrl);

            // Si MIME inconnu → on accepte
            if ($mime && in_array($mime, $forbidden)) {
                continue; // format explicitement impossible
            }

            $images[] = [
                'url'   => $imgUrl,
                'thumb' => $img['thumbnail'] ?? $imgUrl,
                'title' => $img['title'] ?? '',
            ];
        }

        echo json_encode(['success' => true, 'images' => $images]);
    }

    /* ============================================================
       OBTENIR LE MIME D'UNE IMAGE DISTANTE (HEAD REQUEST)
    ============================================================ */
    private function getRemoteMimeType(string $url): ?string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_NOBODY        => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 5,
        ]);

        curl_exec($ch);
        $mime = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        return $mime ?: null;
    }

    /* ============================================================
       SAUVEGARDE COVER WEB (TÉLÉCHARGEMENT + REDIMENSION)
    ============================================================ */
    public function saveWebCover()
    {
        $issueId = $_POST['issue_id'] ?? 0;
        $url     = $_POST['url'] ?? '';

        if (!$issueId || !$url) {
            echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
            return;
        }

        $imgData = @file_get_contents($url);
        if (!$imgData) {
            echo json_encode(['success' => false, 'message' => 'Impossible de télécharger l’image']);
            return;
        }

        $baseDir = __DIR__ . '/../../public/covers';
        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0777, true);
        }

        $fullPath  = $baseDir . '/' . $issueId . '.jpg';
        file_put_contents($fullPath, $imgData);

        // Redimensionnement en 400x619
        $thumbPath = $baseDir . '/' . $issueId . '_thumb.jpg';
        $okResize  = $this->resizeImage($fullPath, $thumbPath, 400, 619);

        if (!$okResize) {
            echo json_encode([
                'success' => false,
                'message' => "Redimensionnement impossible pour cette image"
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'thumb'   => "/covers/{$issueId}_thumb.jpg"
        ]);
    }

    /* ============================================================
       REDIMENSION D'IMAGE (formats étendus + conversion JPEG)
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

            case 'image/gif':
                $image = @imagecreatefromgif($src);
                break;

            case 'image/bmp':
            case 'image/x-ms-bmp':
                if (!function_exists('imagecreatefrombmp')) return false;
                $image = @imagecreatefrombmp($src);
                break;

            default:
                return false;
        }

        if (!$image) return false;

        $dst = imagecreatetruecolor($width, $height);

        // Fond blanc pour éviter les artefacts PNG/GIF transparents
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);

        imagecopyresampled($dst, $image, 0, 0, 0, 0, $width, $height, $srcW, $srcH);

        // Toujours convertir en JPEG
        $ok = imagejpeg($dst, $dest, 90);

        imagedestroy($image);
        imagedestroy($dst);

        return $ok;
    }

    /* ============================================================
       IMPORT D'UNE ISSUE EN BDD
    ============================================================ */
    public function importerSave()
    {
        $issueJson = $_POST['issue'] ?? '';
        if (!$issueJson) {
            echo json_encode(['success' => false, 'message' => 'Issue manquante']);
            return;
        }

        $issue = json_decode($issueJson, true);
        if (!$issue) {
            echo json_encode(['success' => false, 'message' => 'JSON issue invalide']);
            return;
        }

        $model = new Issues();

        // Vérifier si cover locale existe
        $issueId = $issue['id'];
        $coverLocal = null;
        $coverPath  = __DIR__ . '/../../public/covers/' . $issueId . '.jpg';

        if (file_exists($coverPath)) {
            $coverLocal = $issueId . '.jpg';
        }

        // Vérifier si l’issue existe déjà
        $existing = $model->getByIssueId($issueId);

        if ($existing) {
            $model->updateIssue($existing['id'], [
                "number"       => $issue['number'],
                "on_sale_date" => $issue['on_sale_date'],
                "title"        => $issue['title'],
                "synopsis"     => $issue['synopsis'],
                "cover_local"  => $coverLocal
            ]);

            echo json_encode([
                "success" => true,
                "message" => "Issue mise à jour."
            ]);
            return;
        }

        // Sinon on ajoute
        $newId = $model->addIssue([
            "issue_id"     => $issueId,
            "series_id"    => $issue['series_id'],
            "number"       => $issue['number'],
            "on_sale_date" => $issue['on_sale_date'],
            "title"        => $issue['title'],
            "synopsis"     => $issue['synopsis'],
            "cover_local"  => $coverLocal
        ]);

        echo json_encode([
            "success" => true,
            "message" => "Issue ajoutée.",
            "id"      => $newId
        ]);
    }
}
