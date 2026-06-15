<?php

class SeriesController
{
    /* ============================================================
       PAGE : Importer les séries
    ============================================================ */
    public function importer()
    {
        require_once __DIR__ . '/../models/Publishers.php';
        $publisherModel = new Publishers(); // ← CORRECTION
        $publishers = $publisherModel->getAll(); // ← CORRECTION

        require __DIR__ . '/../views/series/importer.php';
    }

    /* ============================================================
       PRÉVISUALISATION JSON
    ============================================================ */
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

    /* ============================================================
       AJOUT AJAX D’UNE SÉRIE
    ============================================================ */
    public function ajax_add()
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

        // Ton modèle original utilise insertFromJson()
        $ok = $seriesModel->insertFromJson($serie);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? "Série importée avec succès." : "Erreur lors de l'import."
        ]);
    }

    /* ============================================================
       INFO AJAX POUR MODALE
    ============================================================ */
    public function ajax_info()
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
       PAGE : Gérer les séries
    ============================================================ */
    public function gerer()
    {
        require_once __DIR__ . '/../models/Series.php';
        $seriesModel = new Series();

        // Ton modèle original utilise getAllWithPublisher()
        $series = $seriesModel->getAllWithPublisher();

        require __DIR__ . '/../views/series/gerer.php';
    }

    /* ============================================================
       FORMULAIRE D’ÉDITION (modale)
    ============================================================ */
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

        require __DIR__ . '/../views/series/edit_form.php';
    }

    /* ============================================================
       MISE À JOUR AJAX
    ============================================================ */
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

    /* ============================================================
       SUPPRESSION AJAX
    ============================================================ */
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
