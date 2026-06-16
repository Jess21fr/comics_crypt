<?php

class Tomes
{
    private PDO $db;

    public function __construct()
    {
        $config = require __DIR__ . '/../Config/config.php';
        
        // On va chercher dans le sous-tableau ['db'] de ton config.php
        $dsn = "mysql:host=" . $config['db']['host'] . ";dbname=" . $config['db']['name'] . ";charset=" . ($config['db']['charset'] ?? 'utf8mb4');
        
        $this->db = new PDO($dsn, $config['db']['user'], $config['db']['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public function getAllSeries()
    {
        return $this->db->query("SELECT id, name FROM series ORDER BY name ASC")->fetchAll();
    }

    public function getAllUniverses()
    {
        return $this->db->query("SELECT id, name FROM universes ORDER BY name ASC")->fetchAll();
    }

    public function getAllCollections()
    {
        return $this->db->query("SELECT id, name FROM collections ORDER BY name ASC")->fetchAll();
    }

    public function insertUniverse(string $name)
    {
        $stmt = $this->db->prepare("INSERT INTO universes (name) VALUES (:name)");
        $stmt->execute(['name' => $name]);
        return $this->db->lastInsertId();
    }

    public function insertCollection(string $name)
    {
        $stmt = $this->db->prepare("INSERT INTO collections (name) VALUES (:name)");
        $stmt->execute(['name' => $name]);
        return $this->db->lastInsertId();
    }

    public function searchIssues(string $query)
    {
        $sql = "SELECT i.id, i.issue_number, i.cover_date, s.name AS series_name, YEAR(i.cover_date) AS year
                FROM issues i
                JOIN series s ON i.series_id = s.id
                WHERE s.name LIKE :query_like OR i.issue_number = :query_exact
                ORDER BY s.name ASC, i.issue_number ASC
                LIMIT 15";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'query_like' => '%' . $query . '%',
            'query_exact' => $query
        ]);
        
        return $stmt->fetchAll();
    }

    public function insertTomeWithIssues(array $tomeData, array $issues)
    {
        try {
            $this->db->beginTransaction();

            $sqlTome = "INSERT INTO tomes (name, series_id, universe_id, collection_id, publisher_vf, publisher_vo, format, tome_number, isbn, page_count, publication_date, price_original, is_owned, is_wanted, is_read, cover_image) 
                        VALUES (:name, :series_id, :universe_id, :collection_id, :publisher_vf, :publisher_vo, :format, :tome_number, :isbn, :page_count, :publication_date, :price_original, :is_owned, :is_wanted, :is_read, :cover_image)";
            
            $stmtTome = $this->db->prepare($sqlTome);
            $stmtTome->execute($tomeData);
            
            $tomeId = $this->db->lastInsertId();

            if (!empty($issues) && $tomeId) {
                $sqlPivot = "INSERT INTO tome_issues (tome_id, issue_id) VALUES (:tome_id, :issue_id)";
                $stmtPivot = $this->db->prepare($sqlPivot);

                foreach ($issues as $issueId) {
                    $stmtPivot->execute([
                        'tome_id'  => $tomeId,
                        'issue_id' => intval($issueId)
                    ]);
                }
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}