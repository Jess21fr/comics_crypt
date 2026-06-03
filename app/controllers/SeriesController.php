<?php

class SeriesController
{
    public function importer()
    {
        // La vue fera les require_once comme pour éditeurs
        require_once __DIR__ . '/../models/Publisher.php';
        $publisherModel = new Publisher();
        $publishers = $publisherModel->getActivePublishers();

        require __DIR__ . '/../views/series/importer.php';
    }

    public function preview()
    {
        header('Content-Type: application/json');

        if (empty($_POST['json'])) {
            echo json_encode(['success' => false, 'message' => "JSON manquant."]);
            return;
        }

        $json = trim($_POST['json']);
        $data = json_decode($json, true);

        if (!$data) {
            echo json_encode(['success' => false, 'message' => "JSON invalide."]);
            return;
        }

        echo json_encode([
            'success' => true,
            'series'  => array_slice($data, 0, 200)
        ]);
    }

    public function ajaxAdd()
    {
        header('Content-Type: application/json');

        if (empty($_POST['serie'])) {
            echo json_encode(['success' => false, 'message' => "Aucune série reçue."]);
            return;
        }

        $serie = json_decode($_POST['serie'], true);

        if (!$serie) {
            echo json_encode(['success' => false, 'message' => "Format série invalide."]);
            return;
        }

        require_once __DIR__ . '/../models/Series.php';
        $seriesModel = new Series();

        if ($seriesModel->existsBySeriesId($serie['id'])) {
            echo json_encode(['success' => false, 'message' => "Série déjà importée."]);
            return;
        }

        $ok = $seriesModel->insertFromJson($serie);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? "Série importée avec succès." : "Erreur lors de l'import."
        ]);
    }

    public function ajaxInfo()
    {
        header('Content-Type: application/json');

        if (empty($_POST['series_id'])) {
            echo json_encode(['success' => false, 'message' => "ID manquant."]);
            return;
        }

        $series_id = intval($_POST['series_id']);

        require_once __DIR__ . '/../models/Series.php';
        $seriesModel = new Series();
        $serie = $seriesModel->getBySeriesId($series_id);

        if (!$serie) {
            echo json_encode(['success' => false, 'message' => "Série introuvable."]);
            return;
        }

        echo json_encode([
            'success' => true,
            'serie'   => $serie
        ]);
    }
}
