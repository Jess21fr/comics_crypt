<?php

class Issues
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /* ============================================================
       RÉCUPÉRER UNE ISSUE PAR SON issue_id (ID Comics.org)
    ============================================================ */
    public function getByIssueId($issueId)
    {
        $sql = "SELECT * FROM issues WHERE issue_id = :issue_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['issue_id' => $issueId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ============================================================
       AJOUTER UNE ISSUE
    ============================================================ */
    public function addIssue(array $data)
    {
        $sql = "INSERT INTO issues 
                (issue_id, series_id, number, on_sale_date, title, synopsis, cover_local)
                VALUES 
                (:issue_id, :series_id, :number, :on_sale_date, :title, :synopsis, :cover_local)";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'issue_id'     => $data['issue_id'],
            'series_id'    => $data['series_id'],
            'number'       => $data['number'],
            'on_sale_date' => $data['on_sale_date'],
            'title'        => $data['title'],
            'synopsis'     => $data['synopsis'],
            'cover_local'  => $data['cover_local'] ?? null
        ]);

        return $this->db->lastInsertId();
    }

    /* ============================================================
       METTRE À JOUR UNE ISSUE EXISTANTE
    ============================================================ */
    public function updateIssue($id, array $data)
    {
        $sql = "UPDATE issues SET
                    number       = :number,
                    on_sale_date = :on_sale_date,
                    title        = :title,
                    synopsis     = :synopsis,
                    cover_local  = :cover_local
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'number'       => $data['number'],
            'on_sale_date' => $data['on_sale_date'],
            'title'        => $data['title'],
            'synopsis'     => $data['synopsis'],
            'cover_local'  => $data['cover_local'] ?? null,
            'id'           => $id
        ]);

        return true;
    }

    /* ============================================================
       RÉCUPÉRER UNE ISSUE PAR ID INTERNE
    ============================================================ */
    public function getById($id)
    {
        $sql = "SELECT * FROM issues WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ============================================================
       LISTE DES ISSUES D'UNE SÉRIE
    ============================================================ */
    public function getBySeries($seriesId)
    {
        $sql = "SELECT * FROM issues WHERE series_id = :series_id ORDER BY number ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['series_id' => $seriesId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
