<?php

class Series
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

    /**
     * Vérifie si une série existe déjà via son ID comics.org
     */
    public function existsBySeriesId($series_id)
    {
        $stmt = $this->db->prepare("SELECT id FROM series WHERE series_id = ?");
        $stmt->execute([$series_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }

    /**
     * Récupère une série via son ID comics.org
     */
    public function getBySeriesId($series_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM series WHERE series_id = ?");
        $stmt->execute([$series_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère toutes les séries
     */
    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM series ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Insère une série à partir du JSON Comics.org
     */
    public function insertFromJson($s)
    {
        $stmt = $this->db->prepare("
            INSERT INTO series (
                series_id, name, sort_name, format, color, dimensions,
                paper_stock, binding, publishing_format, publication_type,
                notes, tracking_notes, year_began, year_ended,
                year_began_uncertain, year_ended_uncertain,
                publication_dates, first_issue, last_issue, issue_count,
                country, language, publisher, external_link
            ) VALUES (
                :series_id, :name, :sort_name, :format, :color, :dimensions,
                :paper_stock, :binding, :publishing_format, :publication_type,
                :notes, :tracking_notes, :year_began, :year_ended,
                :year_began_uncertain, :year_ended_uncertain,
                :publication_dates, :first_issue, :last_issue, :issue_count,
                :country, :language, :publisher, :external_link
            )
        ");

        return $stmt->execute([
            ':series_id'             => $s['id'] ?? null,
            ':name'                  => $s['name'] ?? null,
            ':sort_name'             => $s['sort_name'] ?? null,
            ':format'                => $s['format'] ?? null,
            ':color'                 => $s['color'] ?? null,
            ':dimensions'            => $s['dimensions'] ?? null,
            ':paper_stock'           => $s['paper_stock'] ?? null,
            ':binding'               => $s['binding'] ?? null,
            ':publishing_format'     => $s['publishing_format'] ?? null,
            ':publication_type'      => $s['publication_type'] ?? null,
            ':notes'                 => $s['notes'] ?? null,
            ':tracking_notes'        => $s['tracking_notes'] ?? null,
            ':year_began'            => $s['year_began'] ?? null,
            ':year_ended'            => $s['year_ended'] ?? null,
            ':year_began_uncertain'  => $s['year_began_uncertain'] ?? 0,
            ':year_ended_uncertain'  => $s['year_ended_uncertain'] ?? 0,
            ':publication_dates'     => $s['publication_dates'] ?? null,
            ':first_issue'           => $s['first_issue'] ?? null,
            ':last_issue'            => $s['last_issue'] ?? null,
            ':issue_count'           => $s['issue_count'] ?? null,
            ':country'               => $s['country'] ?? null,
            ':language'              => $s['language'] ?? null,
            ':publisher'             => $s['publisher'] ?? null,
            ':external_link'         => $s['external_link'] ?? null
        ]);
    }
}
