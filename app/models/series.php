<?php

class Series
{
    public PDO $db;

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
       LECTURE
    ============================================================ */

    public function getAll(): array
    {
        return $this->db->query("
            SELECT * FROM series ORDER BY name ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM series WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function existsBySeriesId(int $seriesId): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM series WHERE series_id = ?");
        $stmt->execute([$seriesId]);
        return (bool)$stmt->fetchColumn();
    }

    public function getAllByPublisher(int $publisherId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM series
            WHERE publisher_id = ?
            ORDER BY start_year ASC, name ASC
        ");
        $stmt->execute([$publisherId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================================================
       INSERTION
    ============================================================ */

    public function insertComicVine(array $cv): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO series
            (series_id, name, start_year, count_of_issues, publisher_id, logo, actif, last_sync)
            VALUES (?, ?, ?, ?, ?, ?, 1, NOW())
        ");

        $stmt->execute([
            $cv['series_id'],
            $cv['name'],
            $cv['start_year'],
            $cv['count_of_issues'],
            $cv['publisher_id'],
            $cv['logo']
        ]);

        return (int)$this->db->lastInsertId();
    }

    /* ============================================================
       ACTIVER / DÉSACTIVER
    ============================================================ */

    public function toggleActif(int $id): int
    {
        $stmt = $this->db->prepare("SELECT actif FROM series WHERE id = ?");
        $stmt->execute([$id]);
        $current = (int)$stmt->fetchColumn();

        $new = $current ? 0 : 1;

        $stmt = $this->db->prepare("UPDATE series SET actif = ? WHERE id = ?");
        $stmt->execute([$new, $id]);

        return $new;
    }
}
