<?php

class Publisher {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = new PDO(
            "mysql:host=localhost;dbname=comics_crypt;charset=utf8mb4",
            "root",
            "",
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    public function exists(int $publisher_id): bool {
        $sql = "SELECT 1 FROM publishers WHERE publisher_id = :pid";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":pid" => $publisher_id]);
        return (bool)$stmt->fetch();
    }

    public function insert(int $publisher_id, string $name, string $country): void {
        $sql = "
            INSERT INTO publishers (name, country, actif, logo, publisher_id)
            VALUES (:name, :country, 0, NULL, :publisher_id)
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ":name"         => $name,
            ":country"      => $country,
            ":publisher_id" => $publisher_id
        ]);
    }
}
