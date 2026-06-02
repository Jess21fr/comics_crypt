<?php

class Langue
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

    public function getByComicsOrgId($id_comicsorg)
    {
        $stmt = $this->db->prepare("SELECT * FROM langue WHERE id_comicsorg = ?");
        $stmt->execute([$id_comicsorg]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
