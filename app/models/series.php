<?php

class Series
{
    private PDO $db;

    public function __construct()
    {
        // On remonte d'un niveau (de app/models vers app) pour charger la configuration
        $config = require __DIR__ . '/../Config/config.php';
        
        // Connexion en ciblant précisément le sous-tableau ['db'] de ton fichier config.php
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

    // Sélectionne les séries et le logo de leur éditeur pour la table de gestion
    public function getAllWithPublisher(): array
    {
        $stmt = $this->db->query("
            SELECT s.*, p.name AS publisher_name, p.logo AS publisher_logo 
            FROM series s
            LEFT JOIN publishers p ON s.publisher_id = p.publisher_id
            ORDER BY s.name ASC
        ");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM series WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getBySeriesId(int $seriesId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM series WHERE series_id = ?");
        $stmt->execute([$seriesId]);
        return $stmt->fetch() ?: null;
    }

    // Gère le téléchargement de l'image de l'API + l'écriture propre en BDD (Chantier 2)
    public function importFromApi(array $data): bool
    {
        $seriesId = intval($data['series_id']);
        $logoName = null;

        // Si l'API fournit une image, on la rapatrie localement sur notre serveur
        if (!empty($data['original_url'])) {
            $ext = pathinfo(parse_url($data['original_url'], PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $logoName = $seriesId . '.' . strtolower($ext);
            $targetPath = __DIR__ . '/../../public/series/' . $logoName;

            // Création du dossier public/series s'il n'existe pas encore
            if (!is_dir(dirname($targetPath))) {
                mkdir(dirname($targetPath), 0755, true);
            }

            // Téléchargement sécurisé du flux de l'image
            $imgContent = @file_get_contents($data['original_url']);
            if ($imgContent !== false) {
                file_put_contents($targetPath, $imgContent);
            } else {
                $logoName = null; // Évite d'enregistrer un nom de fichier si le téléchargement échoue
            }
        }

        // Insertion ou mise à jour automatique si la série existe déjà (sécurité ON DUPLICATE KEY)
        $stmt = $this->db->prepare("
            INSERT INTO series (series_id, name, start_year, count_of_issues, publisher_id, logo, actif, last_sync)
            VALUES (:series_id, :name, :start_year, :count_of_issues, :publisher_id, :logo, 1, NOW())
            ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                start_year = VALUES(start_year),
                count_of_issues = VALUES(count_of_issues),
                logo = IFNULL(VALUES(logo), logo),
                last_sync = NOW()
        ");

        return $stmt->execute([
            'series_id'       => $seriesId,
            'name'            => trim($data['name']),
            'start_year'      => !empty($data['start_year']) ? intval($data['start_year']) : null,
            'count_of_issues' => intval($data['count_of_issues']),
            'publisher_id'    => intval($data['publisher_id']),
            'logo'            => $logoName
        ]);
    }

    public function update(array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE series 
            SET name = :name, 
                start_year = :start_year, 
                count_of_issues = :count_of_issues, 
                publisher_id = :publisher_id, 
                logo = :logo, 
                actif = :actif 
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'              => $data['id'],
            'name'            => $data['name'],
            'start_year'      => $data['start_year'],
            'count_of_issues' => $data['count_of_issues'],
            'publisher_id'    => $data['publisher_id'],
            'logo'            => $data['logo'],
            'actif'           => $data['actif']
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM series WHERE id = ?");
        return $stmt->execute([$id]);
    }
}