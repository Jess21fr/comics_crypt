<?php
// /public/save_cover.php

require_once __DIR__ . '/../app/models/Issues.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Méthode non autorisée"]);
    exit;
}

if (!isset($_POST['issue_id'], $_POST['cover_id'], $_POST['base64'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Données manquantes"]);
    exit;
}

$issueId = (int) $_POST['issue_id'];
$coverId = (int) $_POST['cover_id'];
$base64  = $_POST['base64'];

if ($issueId <= 0 || $coverId <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "IDs invalides"]);
    exit;
}

// Nettoyage du base64
if (strpos($base64, ',') !== false) {
    $base64 = explode(',', $base64)[1];
}

$imageData = base64_decode($base64);
if (!$imageData) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Base64 invalide"]);
    exit;
}

// Dossier de stockage : /public/covers/{issueId}/
$baseDir = __DIR__ . "/covers/{$issueId}";
if (!is_dir($baseDir)) {
    mkdir($baseDir, 0777, true);
}

$fullPath  = "{$baseDir}/{$coverId}_full.jpg";
$thumbPath = "{$baseDir}/{$coverId}_thumb.jpg";

// Sauvegarde de l'image HD
if (file_put_contents($fullPath, $imageData) === false) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Échec de l'écriture du fichier"]);
    exit;
}

// Génération miniature + enregistrement BDD
$model = new Issues();

if (!$model->generateThumbnail($fullPath, $thumbPath, 300)) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Impossible de générer la miniature"]);
    exit;
}

$existing = $model->getCover($issueId, $coverId);

if ($existing) {
    $model->updateCover($issueId, $coverId, $fullPath, $thumbPath);
} else {
    $model->addCover($issueId, $coverId, $fullPath, $thumbPath);
}

echo json_encode([
    "success" => true,
    "message" => "Cover importée avec succès.",
    "full"    => $fullPath,
    "thumb"   => $thumbPath
]);
