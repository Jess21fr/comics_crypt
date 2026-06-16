<?php

class IssuesController
{
    private Issues $model;

    public function __construct()
    {
        require_once __DIR__ . '/../models/Issues.php';
        $this->model = new Issues();
    }

    // Affichage de la page principale de synchronisation
    public function importer()
    {
        // On récupère les séries actives avec leur nombre d'épisodes locaux et infos éditeurs
        $seriesList = $this->model->getActiveSeriesWithCount();
        
        require __DIR__ . '/../views/issues/importer.php';
    }

    // 🔍 Recherche d'épisodes sur l'API ComicVine via le proxy sécurisé
    public function search()
    {
        header('Content-Type: application/json');

        $seriesId = intval($_POST['series_id'] ?? 0);
        $page = intval($_POST['page'] ?? 1);

        if (empty($seriesId)) {
            echo json_encode(['success' => false, 'message' => "Identifiant de série manquant."]);
            return;
        }

        require_once __DIR__ . '/../services/ComicVineApi.php';

        $params = [
            'resources'  => 'issue',
            'filter'     => 'volume:' . $seriesId,
            'page'       => $page,
            'limit'      => 50,
            'field_list' => 'id,name,issue_number,cover_date,image,description,volume'
        ];

        $apiResult = ComicVineApi::call('issues/', $params);

        if (!$apiResult) {
            echo json_encode([
                'success' => false, 
                'message' => "Erreur API ComicVine ou quota horaire dépassé !"
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'results' => $apiResult
        ]);
    }

    // 📥 Traitement de l'importation par lot d'un épisode
    public function import()
    {
        header('Content-Type: application/json');

        if (empty($_POST['issue_id']) || empty($_POST['series_id'])) {
            echo json_encode(['success' => false, 'message' => 'Données incomplètes.']);
            return;
        }

        $success = $this->model->importFromApi($_POST);
        echo json_encode(['success' => $success]);
    }


    // ============================================================
    // 🎯 NOUVELLE MÉTHODE : LISTER LES ÉPISODES LOCAUX EN BDD
    // ============================================================
    public function listLocal()
    {
        // Debug temporaire
        error_log("Tentative d'accès à la série ID : " . ($_POST['series_id'] ?? 'NULL'));

        header('Content-Type: application/json');
        
        $seriesId = isset($_POST['series_id']) ? intval($_POST['series_id']) : 0;

        if ($seriesId === 0) {
            echo json_encode([]);
            return;
        }

        // On appelle une méthode du modèle Issues pour récupérer les lignes en BDD
        $issues = $this->model->getBySeriesId($seriesId);

        echo json_encode($issues);
        exit;
    }

    // ============================================================
    // 🎯 NOUVELLE MÉTHODE : METTRE À JOUR UN ÉPISODE UNIQUE (MODALE)
    // ============================================================
    public function updateSingle()
    {
        header('Content-Type: application/json');

        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $issueNumber = isset($_POST['issue_number']) ? trim($_POST['issue_number']) : '';
        $coverDate = !empty($_POST['cover_date']) ? $_POST['cover_date'] : null;
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';

        if ($id === 0 || empty($issueNumber)) {
            echo json_encode([
                'success' => false, 
                'message' => 'Données incomplètes ou identifiant invalide.'
            ]);
            return;
        }

        // Envoi des données nettoyées au modèle pour exécution de l'UPDATE SQL
        $success = $this->model->updateSingleIssue([
            'id' => $id,
            'name' => $name,
            'issue_number' => $issueNumber,
            'cover_date' => $coverDate,
            'description' => $description
        ]);

        echo json_encode([
            'success' => $success, 
            'message' => $success ? '' : 'Erreur lors de la mise à jour en base de données.'
        ]);
        exit;
    }
}