<?php

class Publisher
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

    public function existsByPublisherId($publisher_id)
    {
        $stmt = $this->db->prepare("SELECT id FROM publishers WHERE publisher_id = ?");
        $stmt->execute([$publisher_id]);
        return $stmt->fetchColumn();
    }

    public function insertFromJson($p)
    {
        $stmt = $this->db->prepare("
            INSERT INTO publishers 
            (name, country, actif, logo, publisher_id, year_began, year_ended, notes, url)
            VALUES (?, ?, 0, NULL, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $p['name'] ?? '',
            $p['country'] ?? '',
            $p['id'] ?? null,
            $p['year_began'] ?? null,
            $p['year_ended'] ?? null,
            $p['notes'] ?? null,
            $p['url'] ?? null
        ]);
    }
}
