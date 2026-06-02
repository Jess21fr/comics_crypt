<?php
$config = require __DIR__ . '/../../Config/config.php';
$base = rtrim($config['base_url'], '/');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>ComicsCrypt Back‑Office</title>

    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DATATABLES CSS -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

    <!-- FANCYTABLES CSS -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/fancytable/dist/fancyTable.min.css">

    <!-- FONT AWESOME -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- TON CSS -->
    <link rel="stylesheet" href="<?= $base ?>/assets/css/app.css">

    <!-- JQUERY (SANS integrity) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DATATABLES JS -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <!-- FANCYTABLES JS -->
    <script src="https://cdn.jsdelivr.net/npm/fancytable/dist/fancyTable.min.js"></script>

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-dark text-light">
