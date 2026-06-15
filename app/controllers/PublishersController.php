<?php

require_once __DIR__ . '/../models/Publishers.php';

class PublishersController
{
    /* ============================================================
       PAGE IMPORT (importer.php)
    ============================================================ */
    public function index()
    {
        require __DIR__ . '/../views/publishers/importer.php';
    }

    /* ============================================================
       RECHERCHE COMICVINE (JSONP)
    ============================================================ */
    public function search()
    {
        header('Content-Type: application/json; charset=utf-8');

        $name = $_GET['name'] ?? '';
        if (!$name) {
            echo json_encode(['success' => false, 'message' => "Nom manquant"]);
            exit;
        }

        $apiKey = "b3dbb0ee3b99331ebcf840f56441b0b87771f0e3";
        $url = "https://comicvine.gamespot.com/api/publishers/?api_key={$apiKey}&format=jsonp&filter=name:" . urlencode($name);

        echo json_encode(['success' => true, 'url' => $url]);
        exit;
    }

    /* ============================================================
       IMPORTER UN ÉDITEUR (ComicVine → local)
    ============================================================ */
    public function import()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_POST['publisher_id']) || empty($_POST['name'])) {
            echo json_encode(['success' => false, 'message' => "publisher_id ou name manquant"]);
            exit;
        }

        $model = new Publishers();

        if ($model->existsByPublisherId($_POST['publisher_id'])) {
            echo json_encode(['success' => false, 'message' => "Cet éditeur existe déjà"]);
            exit;
        }

        // Téléchargement du logo HD
        $logoUrl = $_POST['image_super_url'] ?? null;
        $logoFilename = null;

        if ($logoUrl) {
            $ext = pathinfo(parse_url($logoUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
            if (!$ext) $ext = "jpg";

            $logoFilename = "publisher_" . $_POST['publisher_id'] . "." . $ext;
            $logoPath = __DIR__ . "/../../public/logos/" . $logoFilename;

            $img = @file_get_contents($logoUrl);
            if ($img) {
                file_put_contents($logoPath, $img);
            } else {
                $logoFilename = null;
            }
        }

        $payload = [
            'publisher_id' => $_POST['publisher_id'],
            'name'         => $_POST['name'],
            'logo'         => $logoFilename
        ];

        $newId = $model->insertComicVine($payload);

        echo json_encode(['success' => true, 'id' => $newId]);
        exit;
    }

    /* ============================================================
       PAGE GÉRER (DataTable + modale)
    ============================================================ */
    public function gerer()
    {
        $model = new Publishers();
        $publishers = $model->getAll();

        require __DIR__ . '/../views/publishers/gerer.php';
    }

    /* ============================================================
       UPDATE (depuis la modale)
    ============================================================ */
    public function update()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_POST['id']) || empty($_POST['name'])) {
            echo json_encode(['success' => false, 'message' => "Champs manquants"]);
            exit;
        }

        $id    = (int)$_POST['id'];
        $name  = trim($_POST['name']);
        $actif = isset($_POST['actif']) ? (int)$_POST['actif'] : 1;

        $model = new Publishers();
        $publisher = $model->getById($id);

        if (!$publisher) {
            echo json_encode(['success' => false, 'message' => "Éditeur introuvable"]);
            exit;
        }

        /* --- Upload logo si fourni --- */
        $logoFilename = $publisher['logo'];

        if (!empty($_FILES['logo']['tmp_name'])) {

            $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            if (!$ext) $ext = "jpg";

            $logoFilename = "publisher_" . $publisher['publisher_id'] . "." . $ext;
            $logoPath = __DIR__ . "/../../public/logos/" . $logoFilename;

            move_uploaded_file($_FILES['logo']['tmp_name'], $logoPath);
        }

        /* --- Update SQL --- */
        $stmt = $model->db->prepare("
            UPDATE publishers SET
                name = ?,
                logo = ?,
                actif = ?,
                last_sync = NOW()
            WHERE id = ?
        ");

        $stmt->execute([$name, $logoFilename, $actif, $id]);

        echo json_encode(['success' => true]);
        exit;
    }

    /* ============================================================
       ACTIVER / DÉSACTIVER
    ============================================================ */
    public function toggle()
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = $_POST['id'] ?? null;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => "ID manquant"]);
            exit;
        }

        $model = new Publishers();
        $newState = $model->toggleActif($id);

        echo json_encode([
            'success' => true,
            'message' => $newState ? "Éditeur activé" : "Éditeur désactivé"
        ]);
        exit;
    }

    /* ============================================================
       SYNC (désactivé)
    ============================================================ */
    public function sync()
    {
        echo json_encode(['success' => false, 'message' => "Sync désactivé"]);
        exit;
    }
}
