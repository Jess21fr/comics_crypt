<?php

require_once __DIR__ . '/../models/Langue.php';
require_once __DIR__ . '/../models/Publisher.php';

class PublisherController
{
    public function importer()
    {
        $config = require __DIR__ . '/../Config/config.php';

        $publishers = [];

        if (!empty($_FILES['json_file']['tmp_name'])) {
            $json = file_get_contents($_FILES['json_file']['tmp_name']);
            $_SESSION['last_publishers_json'] = $json;

            $data = json_decode($json, true);

            if (is_array($data)) {
                $publishers = $data;
            }
        }

        require __DIR__ . '/../views/publishers/importer.php';
    }

    public function ajaxAdd()
    {
        header('Content-Type: application/json; charset=utf-8');

        if (empty($_SESSION['last_publishers_json'])) {
            echo json_encode(['success' => false, 'message' => "Aucune donnée JSON chargée"]);
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => "ID manquant"]);
            exit;
        }

        $publishers = json_decode($_SESSION['last_publishers_json'], true);

        $publisher = null;
        foreach ($publishers as $p) {
            if ((string)$p['id'] === (string)$id) {
                $publisher = $p;
                break;
            }
        }

        if (!$publisher) {
            echo json_encode(['success' => false, 'message' => "Éditeur introuvable"]);
            exit;
        }

        $model = new Publisher();

        if ($model->existsByPublisherId($publisher['id'])) {
            echo json_encode(['success' => false, 'message' => "Cet éditeur existe déjà"]);
            exit;
        }

        $model->insertFromJson($publisher);

        echo json_encode(['success' => true, 'message' => "Éditeur ajouté en base"]);
        exit;
    }

    public function ajaxInfo()
    {
        $config = require __DIR__ . '/../Config/config.php';

        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo "<p>ID manquant.</p>";
            exit;
        }

        if (empty($_SESSION['last_publishers_json'])) {
            echo "<p>Aucune donnée chargée.</p>";
            exit;
        }

        $publishers = json_decode($_SESSION['last_publishers_json'], true);

        $publisher = null;
        foreach ($publishers as $p) {
            if ((string)$p['id'] === (string)$id) {
                $publisher = $p;
                break;
            }
        }

        if (!$publisher) {
            echo "<p>Éditeur introuvable.</p>";
            exit;
        }

        $langueModel = new Langue();
        $langue = $langueModel->getByComicsOrgId($publisher['country']);

        if (!$langue) {
            $langue = [
                'nom' => 'Inconnu',
                'nom_court' => '-',
                'drapeau' => null
            ];
        }

        ?>
        <table class="table table-dark table-bordered">
            <tr><th>Nom</th><td><?= htmlspecialchars($publisher['name']) ?></td></tr>
            <tr><th>Année de création</th><td><?= htmlspecialchars($publisher['year_began'] ?? '-') ?></td></tr>
            <tr><th>Pays</th>
                <td>
                    <?= htmlspecialchars($langue['nom']) ?>
                    <?php if ($langue['drapeau']): ?>
                        <img src="<?= $config['base_url'] ?>/assets/img/flags/<?= $langue['drapeau'] ?>"
                             style="height:20px;margin-left:8px;">
                    <?php endif; ?>
                </td>
            </tr>
            <tr><th>Notes</th><td><?= nl2br(htmlspecialchars($publisher['notes'] ?? '-')) ?></td></tr>
            <tr><th>URL</th>
                <td>
                    <?php if (!empty($publisher['url'])): ?>
                        <a href="<?= htmlspecialchars($publisher['url']) ?>" target="_blank">
                            <?= htmlspecialchars($publisher['url']) ?>
                        </a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <tr><th>Nombre de gammes</th><td><?= htmlspecialchars($publisher['brand_count'] ?? 0) ?></td></tr>
            <tr><th>Nombre de séries publiées</th><td><?= htmlspecialchars($publisher['series_count'] ?? 0) ?></td></tr>
            <tr><th>Nombre de tomes édités</th><td><?= htmlspecialchars($publisher['issue_count'] ?? 0) ?></td></tr>
        </table>
        <?php

        exit;
    }
}
