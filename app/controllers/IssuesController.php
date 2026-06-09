<?php

require_once __DIR__ . '/../models/Issues.php';
require_once __DIR__ . '/../models/Series.php';

class IssuesController
{
    /* ============================================================
       PAGE IMPORTER
    ============================================================ */
    public function importer()
    {
        $seriesModel = new Series();
        $series = $seriesModel->getAllWithPublisherAndCountry();

        require __DIR__ . '/../views/issues/importer.php';
    }

    /* ============================================================
       PRÉVISUALISATION DES ISSUES (ÉTAPE 2)
    ============================================================ */
    public function preview()
    {
        if (!isset($_POST['json'])) {
            echo json_encode([
                "success" => false,
                "message" => "Aucun JSON reçu."
            ]);
            return;
        }

        $json = trim($_POST['json']);
        $data = json_decode($json, true);

        if (!$data) {
            echo json_encode([
                "success" => false,
                "message" => "JSON invalide."
            ]);
            return;
        }

        // On renvoie les issues telles quelles
        echo json_encode([
            "success" => true,
            "issues"  => $data
        ]);
    }

    /* ============================================================
       PRÉVISUALISATION DES COVERS (ÉTAPE 3)
    ============================================================ */
    public function previewCovers()
    {
        if (!isset($_POST['json'])) {
            echo json_encode([
                "success" => false,
                "message" => "Aucun JSON reçu."
            ]);
            return;
        }

        $json = trim($_POST['json']);
        $data = json_decode($json, true);

        if (!$data) {
            echo json_encode([
                "success" => false,
                "message" => "JSON invalide."
            ]);
            return;
        }

        echo json_encode([
            "success" => true,
            "covers"  => $data
        ]);
    }

    /* ============================================================
       AJOUT D’UNE ISSUE (si tu veux importer les issues en BDD)
    ============================================================ */
    public function ajaxAdd()
    {
        if (!isset($_POST['issue'])) {
            echo json_encode([
                "success" => false,
                "message" => "Aucune issue reçue."
            ]);
            return;
        }

        $issue = json_decode($_POST['issue'], true);

        if (!$issue) {
            echo json_encode([
                "success" => false,
                "message" => "Issue invalide."
            ]);
            return;
        }

        $model = new Issues();

        // Vérifier si l’issue existe déjà
        $existing = $model->getByIssueId($issue['id']);

        if ($existing) {
            $model->updateIssue($existing['id'], [
                "number"       => $issue['number'],
                "on_sale_date" => $issue['on_sale_date'],
                "title"        => $issue['title'],
                "synopsis"     => $issue['synopsis']
            ]);

            echo json_encode([
                "success" => true,
                "message" => "Issue mise à jour."
            ]);
            return;
        }

        // Sinon on ajoute
        $newId = $model->addIssue([
            "issue_id"     => $issue['id'],
            "series_id"    => $issue['series_id'],
            "number"       => $issue['number'],
            "on_sale_date" => $issue['on_sale_date'],
            "title"        => $issue['title'],
            "synopsis"     => $issue['synopsis']
        ]);

        echo json_encode([
            "success" => true,
            "message" => "Issue ajoutée.",
            "id"      => $newId
        ]);
    }

    /* ============================================================
       AJOUT D’UNE COVER (GOOGLE IMAGES)
    ============================================================ */
    public function ajaxAddCover()
    {
        if (!isset($_POST['cover'])) {
            echo json_encode([
                "success" => false,
                "message" => "Aucune cover reçue."
            ]);
            return;
        }

        $cover = json_decode($_POST['cover'], true);

        if (!$cover) {
            echo json_encode([
                "success" => false,
                "message" => "Cover invalide."
            ]);
            return;
        }

        $issueId = $cover['issue'];
        $coverId = $cover['id'];

        $model = new Issues();

        $res = $model->importCoverFromGoogle($issueId, $coverId);

        echo json_encode($res);
    }
}
