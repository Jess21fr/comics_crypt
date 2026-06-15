<?php

class Publishers
{
    public PDO $db; // ← doit être public pour update() dans le controller

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
        return $this->db->query("SELECT * FROM publishers ORDER BY name ASC")
                        ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM publishers WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function existsByPublisherId(int $publisherId): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM publishers WHERE publisher_id = ?");
        $stmt->execute([$publisherId]);
        return (bool)$stmt->fetchColumn();
    }

    /* ============================================================
       NOUVELLE MÉTHODE MANQUANTE
    ============================================================ */

    public function getByPublisherId(int $publisherId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM publishers WHERE publisher_id = ?");
        $stmt->execute([$publisherId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /* ============================================================
       INSERTION
    ============================================================ */

    public function insertComicVine(array $cv): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO publishers
            (publisher_id, name, logo, actif, last_sync)
            VALUES (?, ?, ?, 1, NOW())
        ");

        $stmt->execute([
            $cv['publisher_id'],
            $cv['name'],
            $cv['logo']
        ]);

        return (int)$this->db->lastInsertId();
    }

    /* ============================================================
       ACTIVER / DÉSACTIVER
    ============================================================ */

    public function toggleActif(int $id): int
    {
        $stmt = $this->db->prepare("SELECT actif FROM publishers WHERE id = ?");
        $stmt->execute([$id]);
        $current = (int)$stmt->fetchColumn();

        $new = $current ? 0 : 1;

        $stmt = $this->db->prepare("UPDATE publishers SET actif = ? WHERE id = ?");
        $stmt->execute([$new, $id]);

        return $new;
    }
}
