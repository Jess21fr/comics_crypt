<?php

class IssuesController
{
    /* ============================
       IMPORTER LES ISSUES (ÉCRAN UNIQUE)
       ============================ */
    public function importer()
    {
        require_once __DIR__ . '/../models/Series.php';
        $seriesModel = new Series();

        // Liste des séries pour la DataTable
        $series = $seriesModel->getAllWithPublisherAndCountry();

        require __DIR__ . '/../views/issues/importer.php';
    }

    /* ============================
       PREVIEW JSON ISSUES
       ============================ */
    public function preview()
    {
        header('Content-Type: application/json');

        if (empty($_POST['json'])) {
            echo json_encode(['success' => false, 'message' => "JSON manquant."]);
            return;
        }

        $json = trim($_POST['json']);
        $data = json_decode($json, true);

        if ($data === null) {
            echo json_encode(['success' => false, 'message' => "JSON invalide."]);
            return;
        }

        echo json_encode([
            'success' => true,
            'issues'  => $data
        ]);
    }

    /* ============================
       IMPORT AJAX D’UNE ISSUE
       ============================ */
    public function ajaxAdd()
    {
        header('Content-Type: application/json');

        if (empty($_POST['issue'])) {
            echo json_encode(['success' => false, 'message' => "Aucune issue reçue."]);
            return;
        }

        $issue = json_decode($_POST['issue'], true);

        require_once __DIR__ . '/../models/Issues.php';
        $issuesModel = new Issues();

        if ($issuesModel->existsByIssueId($issue['id'])) {
            echo json_encode(['success' => false, 'message' => "Issue déjà importée."]);
            return;
        }

        $ok = $issuesModel->insertFromJson($issue);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? "Issue importée." : "Erreur lors de l'import."
        ]);
    }

    /* ============================
       INFO AJAX (JSON)
       ============================ */
    public function ajaxInfo()
    {
        header('Content-Type: application/json');

        if (empty($_POST['issue_id'])) {
            echo json_encode(['success' => false, 'message' => "ID manquant."]);
            return;
        }

        $issue_id = intval($_POST['issue_id']);

        require_once __DIR__ . '/../models/Issues.php';
        $issuesModel = new Issues();
        $issue = $issuesModel->getByIssueId($issue_id);

        echo json_encode([
            'success' => true,
            'issue'   => $issue
        ]);
    }

    /* ============================
       GÉRER LES ISSUES
       ============================ */
    public function gerer()
    {
        require_once __DIR__ . '/../models/Issues.php';
        $issuesModel = new Issues();
        $issues = $issuesModel->getAllWithSeries();

        require __DIR__ . '/../views/issues/gere.php';
    }

    /* ============================
       FORMULAIRE EDIT
       ============================ */
    public function edit()
    {
        if (empty($_GET['id'])) {
            echo "ID manquant.";
            return;
        }

        $id = intval($_GET['id']);

        require_once __DIR__ . '/../models/Issues.php';
        $issuesModel = new Issues();
        $issue = $issuesModel->getById($id);

        require __DIR__ . '/../views/issues/edit_form.php';
    }

    /* ============================
       UPDATE
       ============================ */
    public function update()
    {
        header('Content-Type: application/json');

        require_once __DIR__ . '/../models/Issues.php';
        $issuesModel = new Issues();

        $ok = $issuesModel->update($_POST);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? "Issue mise à jour." : "Erreur lors de la mise à jour."
        ]);
    }

    /* ============================
       DELETE
       ============================ */
    public function delete()
    {
        header('Content-Type: application/json');

        require_once __DIR__ . '/../models/Issues.php';
        $issuesModel = new Issues();

        $ok = $issuesModel->delete($_POST['id']);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? "Issue supprimée." : "Erreur lors de la suppression."
        ]);
    }

    /* ============================
       PRÉVISUALISATION DES COVERS (ÉTAPE 3)
       ============================ */
    public function previewCovers()
    {
        header('Content-Type: application/json');

        if (empty($_POST['json'])) {
            echo json_encode(['success' => false, 'message' => "JSON manquant."]);
            return;
        }

        $json = trim($_POST['json']);
        $data = json_decode($json, true);

        if ($data === null) {
            echo json_encode(['success' => false, 'message' => "JSON invalide."]);
            return;
        }

        echo json_encode([
            'success' => true,
            'covers'  => $data
        ]);
    }

    /* ============================
       IMPORT AJAX D’UNE COVER
       ============================ */
    public function ajaxAddCover()
    {
        header('Content-Type: application/json');

        if (empty($_POST['cover'])) {
            echo json_encode(['success' => false, 'message' => "Aucune cover reçue."]);
            return;
        }

        $cover = json_decode($_POST['cover'], true);

        require_once __DIR__ . '/../models/Issues.php';
        $issuesModel = new Issues();

        $issuesModel->updateCoverFromJson($cover);

        echo json_encode([
            'success' => true,
            'message' => "Cover {$cover['id']} importée et téléchargée."
        ]);
    }
}
