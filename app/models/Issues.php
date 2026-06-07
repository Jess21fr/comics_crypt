<?php

class Issues
{
    private $db;

    public function __construct()
    {
        require __DIR__ . '/../config/database.php';
        $this->db = $db;
    }

    /* ============================
       Vérifie si une issue existe
       ============================ */
    public function existsByIssueId($issue_id)
    {
        $sql = "SELECT id FROM issues WHERE issue_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$issue_id]);
        return $stmt->fetch() ? true : false;
    }

    /* ============================
       Récupère une issue par issue_id
       ============================ */
    public function getByIssueId($issue_id)
    {
        $sql = "SELECT * FROM issues WHERE issue_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$issue_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ============================
       Récupère une issue par id interne
       ============================ */
    public function getById($id)
    {
        $sql = "SELECT * FROM issues WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ============================
       Insère une issue depuis JSON
       ============================ */
    public function insertFromJson($i)
    {
        $sql = "INSERT INTO issues (
            issue_id, series_id, number, title, no_title,
            volume, no_volume, volume_not_printed, display_volume_with_number,
            isbn, no_isbn, valid_isbn,
            variant_of, variant_name, variant_cover_status,
            barcode, no_barcode,
            rating, no_rating,
            publication_date, key_date, on_sale_date, on_sale_date_uncertain,
            sort_code,
            indicia_frequency, no_indicia_frequency,
            price,
            page_count, page_count_uncertain,
            editing, no_editing,
            notes,
            indicia_publisher, indicia_pub_not_printed,
            brand, no_brand,
            indicia_printer_not_printed, indicia_printer_sourced_by,
            is_indexed, external_link,
            brand_emblem, indicia_printer,
            image_resources
        ) VALUES (
            :issue_id, :series_id, :number, :title, :no_title,
            :volume, :no_volume, :volume_not_printed, :display_volume_with_number,
            :isbn, :no_isbn, :valid_isbn,
            :variant_of, :variant_name, :variant_cover_status,
            :barcode, :no_barcode,
            :rating, :no_rating,
            :publication_date, :key_date, :on_sale_date, :on_sale_date_uncertain,
            :sort_code,
            :indicia_frequency, :no_indicia_frequency,
            :price,
            :page_count, :page_count_uncertain,
            :editing, :no_editing,
            :notes,
            :indicia_publisher, :indicia_pub_not_printed,
            :brand, :no_brand,
            :indicia_printer_not_printed, :indicia_printer_sourced_by,
            :is_indexed, :external_link,
            :brand_emblem, :indicia_printer,
            :image_resources
        )";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':issue_id' => $i['id'],
            ':series_id' => $i['series'],

            ':number' => $i['number'] ?? null,
            ':title' => $i['title'] ?? null,
            ':no_title' => $i['no_title'] ?? 0,

            ':volume' => $i['volume'] ?? null,
            ':no_volume' => $i['no_volume'] ?? 0,
            ':volume_not_printed' => $i['volume_not_printed'] ?? 0,
            ':display_volume_with_number' => $i['display_volume_with_number'] ?? 0,

            ':isbn' => $i['isbn'] ?? null,
            ':no_isbn' => $i['no_isbn'] ?? 0,
            ':valid_isbn' => $i['valid_isbn'] ?? null,

            ':variant_of' => $i['variant_of'] ?? null,
            ':variant_name' => $i['variant_name'] ?? null,
            ':variant_cover_status' => $i['variant_cover_status'] ?? null,

            ':barcode' => $i['barcode'] ?? null,
            ':no_barcode' => $i['no_barcode'] ?? 0,

            ':rating' => $i['rating'] ?? null,
            ':no_rating' => $i['no_rating'] ?? 0,

            ':publication_date' => $i['publication_date'] ?? null,
            ':key_date' => $i['key_date'] ?? null,
            ':on_sale_date' => $i['on_sale_date'] ?? null,
            ':on_sale_date_uncertain' => $i['on_sale_date_uncertain'] ?? 0,

            ':sort_code' => $i['sort_code'] ?? null,

            ':indicia_frequency' => $i['indicia_frequency'] ?? null,
            ':no_indicia_frequency' => $i['no_indicia_frequency'] ?? 0,

            ':price' => $i['price'] ?? null,

            ':page_count' => $i['page_count'] ?? null,
            ':page_count_uncertain' => $i['page_count_uncertain'] ?? 0,

            ':editing' => $i['editing'] ?? null,
            ':no_editing' => $i['no_editing'] ?? 0,

            ':notes' => $i['notes'] ?? null,

            ':indicia_publisher' => $i['indicia_publisher'] ?? null,
            ':indicia_pub_not_printed' => $i['indicia_pub_not_printed'] ?? 0,

            ':brand' => $i['brand'] ?? null,
            ':no_brand' => $i['no_brand'] ?? 0,

            ':indicia_printer_not_printed' => $i['indicia_printer_not_printed'] ?? 0,
            ':indicia_printer_sourced_by' => $i['indicia_printer_sourced_by'] ?? null,

            ':is_indexed' => $i['is_indexed'] ?? null,
            ':external_link' => $i['external_link'] ?? null,

            ':brand_emblem' => $i['brand_emblem'] ?? null,
            ':indicia_printer' => $i['indicia_printer'] ?? null,

            ':image_resources' => $i['image_resources'] ?? null
        ]);
    }

    /* ============================
       Liste complète avec nom de série
       ============================ */
    public function getAllWithSeries()
    {
        $sql = "SELECT i.*, s.name AS series_name
                FROM issues i
                LEFT JOIN series s ON s.series_id = i.series_id
                ORDER BY i.sort_code ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================
       Mise à jour
       ============================ */
    public function update($data)
    {
        $sql = "UPDATE issues SET
            number = :number,
            title = :title,
            notes = :notes
        WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':number' => $data['number'],
            ':title' => $data['title'],
            ':notes' => $data['notes'],
            ':id' => $data['id']
        ]);
    }

    /* ============================
       Suppression
       ============================ */
    public function delete($id)
    {
        $sql = "DELETE FROM issues WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /* ============================
    Met à jour cover_id / cover_url / cover_local
    depuis le JSON des covers
    ============================ */
    public function updateCoverFromJson($c)
    {
        $cover_id = $c['id'];
        $issue_id = $c['issue'];

        // 1) Construire l’URL CDN réelle
        $folder = floor($cover_id / 1000);
        $cover_url = "https://files1.comics.org//img/gcd/covers_by_id/{$folder}/w400/{$cover_id}.jpg";

        // 2) Déterminer le chemin local
        $local_path = $this->getLocalCoverPath($issue_id, $cover_id);

        // 3) Télécharger l’image
        $this->downloadCover($cover_url, $local_path);

        // 4) Mettre à jour la base
        $sql = "UPDATE issues SET
                    cover_id = :cover_id,
                    cover_url = :cover_url,
                    cover_local = :cover_local
                WHERE issue_id = :issue_id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':cover_id'   => $cover_id,
            ':cover_url'  => $cover_url,
            ':cover_local'=> $local_path,
            ':issue_id'   => $issue_id
        ]);
    }

    /* ============================
    Construit le chemin local
    ============================ */
    public function getLocalCoverPath($issue_id, $cover_id)
    {
        $dir = __DIR__ . '/../../public/covers/' . $issue_id;

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return "/public/covers/{$issue_id}/{$cover_id}.jpg";
    }

    /* ============================
    Télécharge l’image en local
    ============================ */
    public function downloadCover($url, $local_path)
    {
        $full_path = __DIR__ . '/../../' . ltrim($local_path, '/');

        $img = @file_get_contents($url);

        if ($img !== false) {
            file_put_contents($full_path, $img);
        }
    }
}