<?php

class SeriesController
{
    /**
     * Page d'import des séries
     */
    public function importer()
    {
        require_once __DIR__ . '/../models/Publisher.php';
        $publisherModel = new Publisher();
        $publishers = $publisherModel->getActivePublishers();

        require __DIR__ . '/../views/series/importer.php';
    }

    /**
     * Prévisualisation JSON
     */
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

    /**
     * Import AJAX d'une série
     */
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

    /**
     * Informations détaillées d'une série (pour la modale d'import)
     */
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

    /* ============================================================
       GESTION DES SERIES (Gérer)
       ============================================================ */

    /**
     * Page : Gestion > Séries > Gérer
     */
    public function gerer()
    {
        require_once __DIR__ . '/../models/Series.php';
        $seriesModel = new Series();

        // Récupération des séries avec nom éditeur
        $series = $seriesModel->getAllWithPublisher();

        require __DIR__ . '/../views/series/gerer.php';
    }

    /**
     * Récupération d'une série pour la modale d'édition
     */
    public function edit()
    {
        if (empty($_GET['id'])) {
            echo "ID manquant.";
            return;
        }

        $id = intval($_GET['id']);

        require_once __DIR__ . '/../models/Series.php';
        $seriesModel = new Series();
        $serie = $seriesModel->getById($id);

        if (!$serie) {
            echo "Série introuvable.";
            return;
        }

        // Vue partielle : formulaire d'édition
        require __DIR__ . '/../views/series/edit_form.php';
    }

    /**
     * Mise à jour d'une série (AJAX)
     */
    public function update()
    {
        header('Content-Type: application/json');

        if (empty($_POST['id'])) {
            echo json_encode(['success' => false, 'message' => "ID manquant."]);
            return;
        }

        require_once __DIR__ . '/../models/Series.php';
        $seriesModel = new Series();

        $ok = $seriesModel->update($_POST);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? "Série mise à jour." : "Erreur lors de la mise à jour."
        ]);
    }

    /**
     * Suppression d'une série (AJAX)
     */
    public function delete()
    {
        header('Content-Type: application/json');

        if (empty($_POST['id'])) {
            echo json_encode(['success' => false, 'message' => "ID manquant."]);
            return;
        }

        $id = intval($_POST['id']);

        require_once __DIR__ . '/../models/Series.php';
        $seriesModel = new Series();

        $ok = $seriesModel->delete($id);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? "Série supprimée." : "Erreur lors de la suppression."
        ]);
    }
}
