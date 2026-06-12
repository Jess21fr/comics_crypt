<?php
$pdo = new PDO(
    "mysql:host=localhost;dbname=comics_crypt;charset=utf8mb4",
    "root",
    "",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Mise à jour AJAX : actif
if (isset($_POST['toggle_id'])) {
    $stmt = $pdo->prepare("UPDATE publishers SET actif = :a WHERE id = :id");
    $stmt->execute([
        ":a" => (int)$_POST['value'],
        ":id" => (int)$_POST['toggle_id']
    ]);
    exit;
}

// Upload logo
if (!empty($_FILES['logo']) && isset($_POST['publisher_id'])) {

    $id = (int)$_POST['publisher_id'];
    $file = $_FILES['logo'];

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['png','svg'])) {
        die("Format non supporté");
    }

    $filename = "logos/publisher_" . $id . "." . $ext;
    move_uploaded_file($file['tmp_name'], $filename);

    $stmt = $pdo->prepare("UPDATE publishers SET logo = :l WHERE id = :id");
    $stmt->execute([":l" => $filename, ":id" => $id]);

    header("Location: publisher_management.php");
    exit;
}

$publishers = $pdo->query("SELECT * FROM publishers ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des éditeurs</title>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a2e0e9c6c1.js" crossorigin="anonymous"></script>

</head>
<body class="p-4">

<h2>Gestion des éditeurs</h2>

<table id="pubTable" class="display">
<thead>
<tr>
    <th>Nom</th>
    <th>Pays</th>
    <th>Actif</th>
    <th>Logo</th>
</tr>
</thead>
<tbody>
<?php foreach ($publishers as $p): ?>
<tr>
    <td><?= htmlspecialchars($p['name']) ?></td>
    <td><?= htmlspecialchars($p['country']) ?></td>
    <td>
        <input type="checkbox" class="toggle" data-id="<?= $p['id'] ?>" <?= $p['actif'] ? 'checked' : '' ?>>
    </td>
    <td>
        <a href="#" class="openModal" data-id="<?= $p['id'] ?>">
            <i class="fa-solid fa-image fa-xl"></i>
        </a>
        <?php if ($p['logo']): ?>
            <img src="<?= $p['logo'] ?>" height="30">
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<!-- Modal upload -->
<div class="modal fade" id="logoModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Uploader un logo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="publisher_id" id="publisher_id">
        <input type="file" name="logo" accept=".png,.svg" required>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary">Uploader</button>
      </div>
    </form>
  </div>
</div>

<script>
$(document).ready(function(){
    $('#pubTable').DataTable();

    $('.toggle').on('change', function(){
        $.post("publisher_management.php", {
            toggle_id: $(this).data('id'),
            value: $(this).is(':checked') ? 1 : 0
        });
    });

    $('.openModal').on('click', function(){
        $('#publisher_id').val($(this).data('id'));
        new bootstrap.Modal(document.getElementById('logoModal')).show();
    });
});
</script>

</body>
</html>