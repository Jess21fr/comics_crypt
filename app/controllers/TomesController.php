<?php
// app/controllers/TomesController.php

class TomesController {
    private $tomeModel;

    public function __construct() {
        // 🧩 On inclut manuellement le modèle puisque l'autoloader du routeur ne gère que les contrôleurs
        require_once __DIR__ . '/../models/Tomes.php';
        
        // Maintenant PHP sait exactement ce qu'est "Tomes" !
        $this->tomeModel = new Tomes();
    }

    /**
     * Affiche le formulaire de création de tome
     * URL : index.php?route=gestion_tomes_creer
     */
    public function create() {
        // 1. Récupération des données pour alimenter les listes déroulantes (<select>)
        $series = $this->tomeModel->getAllSeries();
        $universes = $this->tomeModel->getAllUniverses();
        $collections = $this->tomeModel->getAllCollections();

        // 2. Inclusion de ta vue (les variables ci-dessus y seront injectées automatiquement)
        require __DIR__ . '/../views/tomes/creer.php';
    }

    /**
     * Traite la soumission du formulaire (POST)
     * URL : index.php?route=gestion_tomes_save
     */
    public function save($data) {
        $name = trim($data['name'] ?? '');
        $issues = $data['issues'] ?? []; // Tableau d'IDs d'épisodes issus du JS
        
        if (empty($name)) {
            header('Location: index.php?route=gestion_tomes_creer&status=error');
            exit;
        }

        // 🛠️ On filtre et associe proprement les clés pour éviter les erreurs de tokens PDO
        $tomeData = [
            'name'             => $name,
            'series_id'        => !empty($data['series_id']) ? (int)$data['series_id'] : null,
            'universe_id'      => !empty($data['universe_id']) ? (int)$data['universe_id'] : null,
            'collection_id'    => !empty($data['collection_id']) ? (int)$data['collection_id'] : null,
            'publisher_vf'     => trim($data['publisher_vf'] ?? ''),
            'publisher_vo'     => trim($data['publisher_vo'] ?? ''),
            'format'           => trim($data['format'] ?? ''),
            'tome_number'      => !empty($data['tome_number']) ? (int)$data['tome_number'] : null,
            'isbn'             => trim($data['isbn'] ?? ''),
            'page_count'       => !empty($data['page_count']) ? (int)$data['page_count'] : null,
            'publication_date' => !empty($data['publication_date']) ? $data['publication_date'] : null,
            'price_original'   => !empty($data['price_original']) ? (float)$data['price_original'] : 0.00,
            'is_owned'         => isset($data['is_owned']) ? 1 : 0,
            'is_wanted'        => isset($data['is_wanted']) ? 1 : 0,
            'is_read'          => isset($data['is_read']) ? 1 : 0,
            'cover_image'      => trim($data['cover_image'] ?? '')
        ];

        // Envoi au modèle pour insertion globale sécurisée
        $success = $this->tomeModel->insertTomeWithIssues($tomeData, $issues);

        if ($success) {
            header('Location: index.php?route=home&status=success');
        } else {
            header('Location: index.php?route=gestion_tomes_creer&status=error');
        }
        exit;
    }

    /**
     * AJAX : Recherche des épisodes US
     * URL : index.php?route=search_issues_ajax&q=...
     */
    public function searchIssuesAjax($query) {
        header('Content-Type: application/json');
        
        if (strlen($query) < 2) {
            echo json_encode([]);
            exit;
        }

        $results = $this->tomeModel->searchIssues($query);
        echo json_encode($results);
        exit;
    }

    /**
     * AJAX : Ajout rapide d'un Univers depuis la modale
     */
    public function quickAddUniverse($name) {
        header('Content-Type: application/json');
        $name = trim($name);

        if (empty($name)) {
            echo json_encode(['success' => false]);
            exit;
        }

        // CORRIGÉ : On appelle insertUniverse() pour matcher ton modèle
        $newId = $this->tomeModel->insertUniverse($name);
        
        echo json_encode([
            'success' => $newId ? true : false,
            'id' => $newId,
            'name' => htmlspecialchars($name)
        ]);
        exit;
    }

    /**
     * AJAX : Ajout rapide d'une Collection/Gamme depuis la modale
     */
    public function quickAddCollection($name) {
        header('Content-Type: application/json');
        $name = trim($name);

        if (empty($name)) {
            echo json_encode(['success' => false]);
            exit;
        }

        // CORRIGÉ : On appelle insertCollection() pour matcher ton modèle
        $newId = $this->tomeModel->insertCollection($name);
        
        echo json_encode([
            'success' => $newId ? true : false,
            'id' => $newId,
            'name' => htmlspecialchars($name)
        ]);
        exit;
    }
}