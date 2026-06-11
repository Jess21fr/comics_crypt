<?php

class Publishers
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

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM publishers ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function exists($id)
    {
        $stmt = $this->db->prepare("SELECT id FROM publishers WHERE id = ?");
        $stmt->execute([$id]);
        return (bool) $stmt->fetchColumn();
    }

    public function existsByPublisherId($publisher_id)
    {
        $stmt = $this->db->prepare("SELECT id FROM publishers WHERE publisher_id = ?");
        $stmt->execute([$publisher_id]);
        return (bool) $stmt->fetchColumn();
    }

    public function toggleActif($id)
    {
        $stmt = $this->db->prepare("SELECT actif FROM publishers WHERE id = ?");
        $stmt->execute([$id]);
        $current = $stmt->fetchColumn();

        $new = $current ? 0 : 1;

        $stmt = $this->db->prepare("UPDATE publishers SET actif = ? WHERE id = ?");
        $stmt->execute([$new, $id]);

        return $new;
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM publishers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE publishers SET
                name = ?,
                country = ?,
                year_began = ?,
                year_ended = ?,
                url = ?,
                notes = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $data['name'],
            $data['country'],
            $data['year_began'],
            $data['year_ended'],
            $data['url'],
            $data['notes'],
            $id
        ]);
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

    /**
     * VERSION CORRIGÉE : renvoie AUSSI publisher_id
     */
    public function getActivePublishers()
    {
        $sql = "
            SELECT 
                p.id,
                p.name,
                p.country,
                p.publisher_id,      -- IMPORTANT
                l.nom_court AS country_code
            FROM publishers p
            LEFT JOIN langue l 
                ON l.id_comicsorg = p.country
            WHERE p.actif = 1
            ORDER BY p.name ASC
        ";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
