<?php

class PublisherController
{
    public function importer()
    {
        $config = require __DIR__ . '/../Config/config.php';

        $publishers = [];

        if (!empty($_FILES['json_file']['tmp_name'])) {
            $json = file_get_contents($_FILES['json_file']['tmp_name']);
            $data = json_decode($json, true);

            if (is_array($data)) {
                $publishers = $data;
            }
        }

        require __DIR__ . '/../views/publishers/importer.php';
    }

    public function ajaxAdd()
    {
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => true,
            'message' => "Éditeur ajouté (simulation)."
        ]);
        exit;
    }

    public function ajaxInfo()
    {
        $id = $_GET['id'] ?? null;

        echo "<p>Détails de l’éditeur ID : " . htmlspecialchars($id) . "</p>";
        echo "<p>(Contenu à compléter plus tard)</p>";
        exit;
    }
}
