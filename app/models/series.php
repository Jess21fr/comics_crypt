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

    public function getBySeriesId(int $seriesId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM series WHERE series_id = ?");
        $stmt->execute([$seriesId]);
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

    public function getAllWithPublisher(): array
    {
        return $this->db->query("
            SELECT s.*, p.name AS publisher_name
            FROM series s
            LEFT JOIN publishers p ON p.publisher_id = s.publisher_id
            ORDER BY s.name ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

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

    public function insertFromJson(array $cv): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO series
            (series_id, name, start_year, count_of_issues, publisher_id, logo, actif, last_sync)
            VALUES (?, ?, ?, ?, ?, ?, 1, NOW())
        ");

        $stmt->execute([
            $cv['id'],
            $cv['name'],
            $cv['start_year'] ?? null,
            $cv['count_of_issues'] ?? 0,
            $cv['publisher']['id'] ?? null,
            $cv['image']['original_url'] ?? null
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE series
            SET name = ?, start_year = ?, count_of_issues = ?, publisher_id = ?, logo = ?, actif = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['name'],
            $data['start_year'],
            $data['count_of_issues'],
            $data['publisher_id'],
            $data['logo'],
            $data['actif'],
            $data['id']
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM series WHERE id = ?");
        return $stmt->execute([$id]);
    }

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
