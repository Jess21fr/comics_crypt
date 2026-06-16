<?php

class Issues
{
    private PDO $db;

    public function __construct()
    {
        $config = require __DIR__ . '/../Config/config.php';
        $this->db = new PDO(
            "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset={$config['db']['charset']}",
            $config['db']['user'],
            $config['db']['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    /**
     * Récupère les séries actives avec le compte des épisodes locaux
     */
    public function getActiveSeriesWithCount(): array
    {
        $stmt = $this->db->query("
            SELECT 
                s.*, 
                p.name as publisher_name, 
                p.logo as publisher_logo,
                COUNT(i.id) as imported_count
            FROM series s
            LEFT JOIN publishers p ON s.publisher_id = p.publisher_id
            LEFT JOIN issues i ON s.series_id = i.series_id
            WHERE s.actif = 1
            GROUP BY s.series_id
            ORDER BY s.name ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Importation unitaire d'un épisode depuis l'API ComicVine
     */
    public function importFromApi(array $data): bool
    {
        $issueId = intval($data['issue_id']);
        $seriesId = intval($data['series_id']);
        $logoName = null;

        if (!empty($data['original_url'])) {
            $ext = pathinfo(parse_url($data['original_url'], PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $logoName = $issueId . '.' . strtolower($ext);
            $targetPath = __DIR__ . '/../../public/issues/' . $logoName;

            if (!is_dir(dirname($targetPath))) {
                mkdir(dirname($targetPath), 0755, true);
            }

            $imgContent = @file_get_contents($data['original_url']);
            if ($imgContent !== false) {
                file_put_contents($targetPath, $imgContent);
            } else {
                $logoName = null;
            }
        }

        $cleanDescription = !empty($data['description']) ? trim(strip_tags($data['description'])) : null;

        $stmt = $this->db->prepare("
            INSERT INTO issues (issue_id, series_id, name, issue_number, cover_date, description, logo, last_sync)
            VALUES (:issue_id, :series_id, :name, :issue_number, :cover_date, :description, :logo, NOW())
            ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                issue_number = VALUES(issue_number),
                cover_date = VALUES(cover_date),
                description = VALUES(description),
                logo = IFNULL(VALUES(logo), logo),
                last_sync = NOW()
        ");

        return $stmt->execute([
            'issue_id'     => $issueId,
            'series_id'    => $seriesId,
            'name'         => !empty($data['name']) ? trim($data['name']) : null,
            'issue_number' => trim($data['issue_number']),
            'cover_date'   => !empty($data['cover_date']) ? $data['cover_date'] : null,
            'description'  => $cleanDescription,
            'logo'         => $logoName
        ]);
    }

    /**
     * Récupère les épisodes locaux d'une série avec tri naturel
     */
    public function getBySeriesId(int $seriesId): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, series_id, issue_id, issue_number, name, cover_date, logo, description 
                FROM issues 
                WHERE series_id = ?
                ORDER BY CAST(issue_number AS UNSIGNED) ASC, issue_number ASC
            ");
            $stmt->execute([$seriesId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Mettre à jour les données textuelles d'un épisode (Modale Édition)
     */
    public function updateSingleIssue(array $data): bool
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE issues 
                SET name = ?, 
                    issue_number = ?, 
                    cover_date = ?, 
                    description = ? 
                WHERE id = ?
            ");
            
            return $stmt->execute([
                $data['name'],
                $data['issue_number'],
                $data['cover_date'],
                $data['description'],
                $data['id']
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
}