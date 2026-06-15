<?php

class SeriesController
{
    public function importer()
    {
        require_once __DIR__ . '/../models/Publishers.php';
        $publisherModel = new Publishers();
        $publishers = $publisherModel->getAll();

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

        $ok = $seriesModel->insertFromJson($serie);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? "Série importée avec succès." : "Erreur lors de l'import."
        ]);
    }

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

    public function gerer()
    {
        require_once __DIR__ . '/../models/Series.php';
        $seriesModel = new Series();
        $series = $seriesModel->getAllWithPublisher();

        require __DIR__ . '/../views/series/gerer.php';
    }

    public function edit()
    {
        if (empty($_GET['id'])) {
            echo "ID manquant.";
            return;
        }

        $id = intval($_GET['id']);

        require_once __DIR__ . '/../models/Series.php';
        require_once __DIR__ . '/../models/Publishers.php';

        $seriesModel    = new Series();
        $publisherModel = new Publishers();

        $serie = $seriesModel->getById($id);

        if (!$serie) {
            echo "Série introuvable.";
            return;
        }

        $publisher       = $publisherModel->getByPublisherId($serie['publisher_id']);
        $publishersActifs = []; // plus utilisé dans le formulaire actuel, laissé vide volontairement

        require __DIR__ . '/../views/series/edit_form.php';
    }

    public function update()
    {
        header('Content-Type: application/json');

        if (empty($_POST['id'])) {
            echo json_encode(['success' => false, 'message' => "ID manquant."]);
            return;
        }

        $id = (int)$_POST['id'];

        require_once __DIR__ . '/../models/Series.php';
        $seriesModel = new Series();

        $serie = $seriesModel->getById($id);
        if (!$serie) {
            echo json_encode(['success' => false, 'message' => "Série introuvable."]);
            return;
        }

        // actif (checkbox)
        $_POST['actif'] = isset($_POST['actif']) ? 1 : 0;

        // gestion du logo : si nouveau fichier → on écrase, sinon on garde l’ancien
        $logoFinal = $serie['logo'];

        if (!empty($_FILES['new_logo']) && $_FILES['new_logo']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['new_logo']['tmp_name'];

            // on garde le même nom de fichier si déjà présent, sinon {series_id}.jpg
            $fileName = $logoFinal ?: ($serie['series_id'] . '.jpg');

            $targetPath = __DIR__ . '/../../public/series/' . $fileName;

            @move_uploaded_file($tmpName, $targetPath);

            $logoFinal = $fileName;
        }

        $_POST['logo'] = $logoFinal;

        $ok = $seriesModel->update($_POST);

        echo json_encode([
            'success' => (bool)$ok,
            'message' => $ok ? "Série mise à jour." : "Erreur lors de la mise à jour."
        ]);
    }

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

    public function import()
    {
        header('Content-Type: application/json');

        if (empty($_POST['series_id'])) {
            echo json_encode(['success' => false, 'message' => "ID série manquant."]);
            return;
        }

        $series_id       = intval($_POST['series_id']);
        $name            = $_POST['name'] ?? '';
        $start_year      = $_POST['start_year'] ?? null;
        $count_of_issues = $_POST['count_of_issues'] ?? 0;
        $publisher_id    = $_POST['publisher_id'] ?? null;
        $original_url    = $_POST['original_url'] ?? '';

        require_once __DIR__ . '/../models/Series.php';
        $seriesModel = new Series();

        if ($seriesModel->existsBySeriesId($series_id)) {
            echo json_encode(['success' => false, 'message' => "Série déjà importée."]);
            return;
        }

        $localName = $series_id . ".jpg";
        $localPath = __DIR__ . "/../../public/series/" . $localName;

        if ($original_url) {
            $img = @file_get_contents($original_url);
            if ($img !== false) {
                @file_put_contents($localPath, $img);
            }
        }

        $cv = [
            'series_id'       => $series_id,
            'name'            => $name,
            'start_year'      => $start_year,
            'count_of_issues' => $count_of_issues,
            'publisher_id'    => $publisher_id,
            'logo'            => $localName
        ];

        $ok = $seriesModel->insertComicVine($cv);

        echo json_encode([
            'success' => (bool)$ok,
            'message' => $ok ? "Série importée." : "Erreur lors de l'import."
        ]);
    }
}
