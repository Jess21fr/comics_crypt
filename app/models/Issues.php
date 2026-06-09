<?php

require_once __DIR__ . '/../helpers/GoogleImages.php';

class Issues
{
    private $db;

    public function __construct()
    {
        $config = require __DIR__ . '/../Config/config.php';

        $this->db = new PDO(
            "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset=utf8mb4",
            $config['db']['user'],
            $config['db']['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    /* ============================================================
       RÉCUPÉRATIONS
    ============================================================ */

    public function getByIssueId($issue_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM issues WHERE issue_id = ?");
        $stmt->execute([$issue_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM issues WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ============================================================
       AJOUT / MISE À JOUR ISSUE
    ============================================================ */

    public function addIssue($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO issues (issue_id, series_id, number, on_sale_date, title, synopsis)
            VALUES (:issue_id, :series_id, :number, :on_sale_date, :title, :synopsis)
        ");

        $stmt->execute([
            ':issue_id'     => $data['issue_id'],
            ':series_id'    => $data['series_id'],
            ':number'       => $data['number'],
            ':on_sale_date' => $data['on_sale_date'],
            ':title'        => $data['title'],
            ':synopsis'     => $data['synopsis']
        ]);

        return $this->db->lastInsertId();
    }

    public function updateIssue($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE issues SET
                number = :number,
                on_sale_date = :on_sale_date,
                title = :title,
                synopsis = :synopsis
            WHERE id = :id
        ");

        return $stmt->execute([
            ':id'           => $id,
            ':number'       => $data['number'],
            ':on_sale_date' => $data['on_sale_date'],
            ':title'        => $data['title'],
            ':synopsis'     => $data['synopsis']
        ]);
    }

    /* ============================================================
       COVERS
    ============================================================ */

    public function getCover($issueId, $coverId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM issues_covers
            WHERE issue_id = ? AND cover_id = ?
        ");
        $stmt->execute([$issueId, $coverId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addCover($issueId, $coverId, $fullPath, $thumbPath)
    {
        $stmt = $this->db->prepare("
            INSERT INTO issues_covers (issue_id, cover_id, cover_local_full, cover_local_thumb)
            VALUES (?, ?, ?, ?)
        ");

        return $stmt->execute([$issueId, $coverId, $fullPath, $thumbPath]);
    }

    public function updateCover($issueId, $coverId, $fullPath, $thumbPath)
    {
        $stmt = $this->db->prepare("
            UPDATE issues_covers
            SET cover_local_full = ?, cover_local_thumb = ?
            WHERE issue_id = ? AND cover_id = ?
        ");

        return $stmt->execute([$fullPath, $thumbPath, $issueId, $coverId]);
    }

    /* ============================================================
       IMPORT COVER VIA GOOGLE IMAGES
    ============================================================ */

    public function importCoverFromGoogle($issueId, $coverId)
    {
        // 1) Construire la requête Google Images
        $query = "comics.org issue {$issueId} cover {$coverId}";

        // 2) Récupérer la vraie image HD
        $url = GoogleImages::search($query);

        if (!$url) {
            return [
                "success" => false,
                "message" => "Impossible de trouver une image Google pour cette cover."
            ];
        }

        // 3) Définir les chemins locaux
        $baseDir = __DIR__ . "/../../public/covers/{$issueId}";
        $fullPath  = "{$baseDir}/{$coverId}_full.jpg";
        $thumbPath = "{$baseDir}/{$coverId}_thumb.jpg";

        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0777, true);
        }

        // 4) Télécharger l’image HD
        if (!GoogleImages::download($url, $fullPath)) {
            return [
                "success" => false,
                "message" => "Téléchargement impossible depuis Google Images."
            ];
        }

        // 5) Générer la miniature
        if (!$this->generateThumbnail($fullPath, $thumbPath, 300)) {
            return [
                "success" => false,
                "message" => "Impossible de générer la miniature."
            ];
        }

        // 6) Enregistrer en BDD
        $existing = $this->getCover($issueId, $coverId);

        if ($existing) {
            $this->updateCover($issueId, $coverId, $fullPath, $thumbPath);
        } else {
            $this->addCover($issueId, $coverId, $fullPath, $thumbPath);
        }

        return [
            "success" => true,
            "message" => "Cover importée avec succès.",
            "full"    => $fullPath,
            "thumb"   => $thumbPath
        ];
    }

    /* ============================================================
       MINIATURE
    ============================================================ */

    public function generateThumbnail($src, $dest, $maxWidth)
    {
        if (!file_exists($src)) return false;

        $info = getimagesize($src);
        if (!$info) return false;

        list($width, $height) = $info;

        $ratio = $maxWidth / $width;
        $newWidth  = $maxWidth;
        $newHeight = intval($height * $ratio);

        $srcImg = imagecreatefromjpeg($src);
        if (!$srcImg) return false;

        $thumb = imagecreatetruecolor($newWidth, $newHeight);

        imagecopyresampled(
            $thumb, $srcImg,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $width, $height
        );

        imagejpeg($thumb, $dest, 90);

        imagedestroy($srcImg);
        imagedestroy($thumb);

        return true;
    }
}
