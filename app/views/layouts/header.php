<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? "Back‑Office" ?></title>

    <!-- Bootstrap 5 (CDN officiel) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- FancyTable -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fancytable/dist/fancyTable.min.css">

    <!-- Styles custom -->
    <link rel="stylesheet" href="/comics_crypt/public/assets/css/app.css">

    <!-- jQuery -->
    <script src="/comics_crypt/public/assets/js/jquery.min.js"></script>

    <!-- Bootstrap JS (CDN officiel) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- FancyTable JS -->
    <script src="https://cdn.jsdelivr.net/npm/fancytable/dist/fancyTable.min.js"></script>
</head>

<body>

<?php include __DIR__ . "/menu.php"; ?>

<div class="page-container">
