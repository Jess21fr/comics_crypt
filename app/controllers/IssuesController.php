<?php

require_once __DIR__ . '/../models/Issues.php';
require_once __DIR__ . '/../models/Series.php';
require_once __DIR__ . '/../models/Publishers.php';

class IssuesController
{
    private string $comicvineApiKey;
    private string $comicvineBaseUrl = 'https://comicvine.gamespot.com/api';

    public function __construct()
    {
        $config = require __DIR__ . '/../Config/config.php';
        $this->comicvineApiKey = $config['comicvine_api_key'] ?? '';
    }

    /**
     * Page d’import des issues
     */
    public function importer()
    {
        $seriesModel    = new Series();
        $publisherModel = new Publishers();

        $series     = $seriesModel->getAllWithPublisherAndCountry();
        $publishers = $publisherModel->getActivePublishers();

        require __DIR__ . '/../views/issues/importer.php';
    }

    /**
     * Prévisualisation : JSON Comics.org -> issues enrichies (ComicVine + nom série)
     */
    public function preview()
    {
        header('Content-Type: application/json');

        if (empty($_POST['json'])) {
            echo json_encode(['success' => false, 'message' => 'Aucun JSON reçu.']);
            return;
        }

        $json = trim($_POST['json']);
        $data = json_decode($json, true);

        if (!$data || !is_array($data)) {
            echo json_encode(['success' => false, 'message' => 'JSON Comics.org invalide.']);
            return;
        }

        $seriesModel = new Series();
        $issuesFinal = [];

        foreach ($data as $issue) {

            // JSON Comics.org : structure telle que tu l’as collée
            $issueId   = $issue['id'] ?? null;
            $number    = $issue['number'] ?? null;
            $seriesId  = $issue['series'] ?? null;          // ID série Comics.org
            $onSale    = $issue['on_sale_date'] ?? null;
            $title     = $issue['title'] ?? null;
            $synopsis  = null; // Comics.org ne le fournit pas ici

            // Récupérer la série locale pour nom + volume ComicVine
            $seriesRow = $seriesModel->getBySeriesId($seriesId);
            $seriesName = $seriesRow['name'] ?? '—';
            $volumeId   = $seriesRow['comicvine_volume_id'] ?? null;

            // ============================
            // Appel ComicVine pour la cover
            // ============================
            $comicvineImage = null;

            if ($this->comicvineApiKey && $volumeId && $number !== null) {

                $url = $this->comicvineBaseUrl
                    . "/issues/?api_key={$this->comicvineApiKey}&format=json"
                    . "&filter=volume:{$volumeId},number:{$number}"
                    . "&field_list=id,issue_number,name,image,cover_date,volume"
                    . "&limit=1";

                $cvJson = $this->curlJson($url);

                if ($cvJson && !empty($cvJson['results'])) {
                    $cvIssue = $cvJson['results'][0];

                    $comicvineImage =
                        $cvIssue['image']['original_url']
                        ?? $cvIssue['image']['super_url']
                        ?? $cvIssue['image']['medium_url']
                        ?? $cvIssue['image']['icon_url']
                        ?? null;
                }
            }

            // ============================
            // Construction de l’issue envoyée au front
            // ============================
            $issuesFinal[] = [
                'id'          => $issueId,
                'number'      => $number,
                'series_id'   => $seriesId,
                'series_name' => $seriesName,
                'on_sale_date'=> $onSale,
                'title'       => $title,
                'synopsis'    => $synopsis,
                // pour le front + importerSave()
                'image'       => [
                    'original_url' => $comicvineImage
                ],
                'comicvine_image'            => $comicvineImage,
                'series_comicvine_volume_id' => $volumeId
            ];
        }

        echo json_encode([
            'success' => true,
            'issues'  => $issuesFinal
        ]);
    }

    /**
     * Sauvegarde d’une issue (import)
     */
    public function importerSave()
    {
        header('Content-Type: application/json');

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

        $model      = new Issues();
        $issueId    = $issue['id'];
        $coverLocal = null;

        // URL ComicVine fournie par preview()
        $comicvineUrl = $issue['image']['original_url']
            ?? $issue['image']['super_url']
            ?? null;

        if ($comicvineUrl) {
            $imgData = @file_get_contents($comicvineUrl);

            if ($imgData) {
                $baseDir = __DIR__ . '/../../public/covers';
                if (!is_dir($baseDir)) {
                    mkdir($baseDir, 0777, true);
                }

                $fullPath = $baseDir . "/{$issueId}.jpg";
                file_put_contents($fullPath, $imgData);

                $thumbPath = $baseDir . "/{$issueId}_thumb.jpg";
                $this->resizeImage($fullPath, $thumbPath, 400, 619);

                $coverLocal = "{$issueId}.jpg";
            }
        }

        $existing = $model->getByIssueId($issueId);

        if ($existing) {
            $model->updateIssue($existing['id'], [
                'number'       => $issue['number'],
                'on_sale_date' => $issue['on_sale_date'],
                'title'        => $issue['title'],
                'synopsis'     => $issue['synopsis'],
                'cover_local'  => $coverLocal ?: $existing['cover_local'],
            ]);

            echo json_encode(['success' => true, 'message' => 'Issue mise à jour.']);
            return;
        }

        $newId = $model->addIssue([
            'issue_id'     => $issueId,
            'series_id'    => $issue['series_id'],
            'number'       => $issue['number'],
            'on_sale_date' => $issue['on_sale_date'],
            'title'        => $issue['title'],
            'synopsis'     => $issue['synopsis'],
            'cover_local'  => $coverLocal,
        ]);

        echo json_encode(['success' => true, 'message' => 'Issue ajoutée.', 'id' => $newId]);
    }

    /* ============================================================
       OUTILS
    ============================================================ */

    private function curlJson(string $url): ?array
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT      => "ComicsCrypt/1.0",
            CURLOPT_TIMEOUT        => 10
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            return null;
        }

        return json_decode($response, true);
    }

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

        $dst   = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);

        imagecopyresampled($dst, $image, 0, 0, 0, 0, $width, $height, $srcW, $srcH);

        $ok = imagejpeg($dst, $dest, 90);

        imagedestroy($image);
        imagedestroy($dst);

        return $ok;
    }
}
