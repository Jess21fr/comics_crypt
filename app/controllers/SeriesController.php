<?php

class SeriesController
{
    private Series $model;

    public function __construct()
    {
        require_once __DIR__ . '/../models/Series.php';
        $this->model = new Series();
    }

    // Affichage de la table de gestion principale
    public function gerer()
    {
        // On récupère toutes les séries avec le nom de l'éditeur (et son logo s'il y est)
        $series = $this->model->getAllWithPublisher();
        
        // On charge la vue
        require __DIR__ . '/../views/series/gerer.php';
    }

    // Affichage du formulaire d'importation ComicVine
    public function importer()
    {
        // Chargement du modèle Publishers (avec S)
        require_once __DIR__ . '/../models/Publishers.php';
        $pubModel = new Publishers(); // 🎯 CORRIGÉ : Instanciation de la classe avec un S
        $publishers = $pubModel->getAll();

        require __DIR__ . '/../views/series/importer.php';
    }

    // 🔐 Proxy de recherche sécurisé relié au Limiter
    public function search()
    {
        header('Content-Type: application/json');

        $query = trim($_POST['query'] ?? '');
        $publisherId = intval($_POST['publisher_id'] ?? 0);
        $page = intval($_POST['page'] ?? 1);

        if (empty($query) || empty($publisherId)) {
            echo json_encode(['success' => false, 'message' => "Paramètres manquants."]);
            return;
        }

        require_once __DIR__ . '/../services/ComicVineApi.php';

        $params = [
            'resources'  => 'volume',
            'query'      => $query,
            'page'       => $page,
            'limit'      => 100,
            'field_list' => 'name,image,id,publisher,start_year,count_of_issues'
        ];

        // L'appel passe au crible de ton gestionnaire de quotas
        $apiResult = ComicVineApi::call('search/', $params);

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

    // Réception du formulaire de modification asynchrone (Modale)
    public function edit()
    {
        $id = intval($_GET['id'] ?? 0);
        $serie = $this->model->getById($id);

        if (!$serie) {
            echo "Série introuvable.";
            return;
        }

        // On récupère l'éditeur lié pour afficher sa fiche dans la modale
        require_once __DIR__ . '/../models/Publishers.php';
        $pubModel = new Publishers(); // 🎯 CORRIGÉ AUSSI ICI : Avec un S
        $publisher = $pubModel->getByPublisherId($serie['publisher_id']);

        // Renvoie uniquement le fragment HTML du formulaire
        require __DIR__ . '/../views/series/edit_form.php';
    }

    // Traitement du POST de mise à jour (Nom, année, statut actif, nouveau logo)
    public function update()
    {
        header('Content-Type: application/json');

        $id = intval($_POST['id'] ?? 0);
        $serie = $this->model->getById($id);

        if (!$serie) {
            echo json_encode(['success' => false, 'message' => 'Série inexistante.']);
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $startYear = !empty($_POST['start_year']) ? intval($_POST['start_year']) : null;
        $countOfIssues = isset($_POST['count_of_issues']) ? intval($_POST['count_of_issues']) : 0;
        $publisherId = intval($_POST['publisher_id'] ?? 0);
        $actif = isset($_POST['actif']) ? 1 : 0;
        $logoName = $serie['logo'];

        // Gestion de l'upload d'un nouveau fichier local
        if (isset($_FILES['new_logo']) && $_FILES['new_logo']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['new_logo']['name'], PATHINFO_EXTENSION);
            $newName = $serie['series_id'] . '.' . strtolower($ext);
            $targetPath = __DIR__ . '/../../public/series/' . $newName;

            if (move_uploaded_file($_FILES['new_logo']['tmp_name'], $targetPath)) {
                $logoName = $newName;
            }
        }

        $success = $this->model->update([
            'id' => $id,
            'name' => $name,
            'start_year' => $startYear,
            'count_of_issues' => $countOfIssues,
            'publisher_id' => $publisherId,
            'logo' => $logoName,
            'actif' => $actif
        ]);

        echo json_encode(['success' => $success, 'message' => $success ? '' : 'Erreur lors de la mise à jour.']);
    }

    // Suppression d'une série
    public function delete()
    {
        header('Content-Type: application/json');
        $id = intval($_POST['id'] ?? 0);

        $success = $this->model->delete($id);

        echo json_encode(['success' => $success]);
    }

    // Reçoit les données de l'import d'une série en AJAX (Chantier 2)
    public function import()
    {
        header('Content-Type: application/json');

        if (empty($_POST['series_id']) || empty($_POST['name'])) {
            echo json_encode(['success' => false, 'message' => 'Données incomplètes.']);
            return;
        }

        // On passe le relais au modèle qui télécharge l'image et gère la BDD
        $success = $this->model->importFromApi($_POST);

        echo json_encode(['success' => $success]);
    }
}