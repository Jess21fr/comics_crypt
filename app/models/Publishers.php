<?php

class Publishers
{
    // Repassé en privé : le contrôleur n'a plus besoin de manipuler l'objet PDO directement
    private PDO $db;

    /**
     * Constructeur adaptatif
     * Permet d'injecter une connexion PDO existante ou d'en créer une nouvelle à la volée.
     */
    public function __construct(?PDO $pdo = null)
    {
        if ($pdo !== null) {
            $this->db = $pdo;
        } else {
            // Correction de la casse du chemin d'inclusion
            $config = require __DIR__ . '/../config/config.php';

            $this->db = new PDO(
                "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset={$config['db']['charset']}",
                $config['db']['user'],
                $config['db']['pass'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        }
    }

    /* ============================================================
       MÉTHODES DE LECTURE (READ)
    ============================================================ */

    /**
     * Récupère tous les éditeurs triés par nom
     */
    public function getAll(): array
    {
        return $this->db->query("SELECT * FROM publishers ORDER BY name ASC")->fetchAll();
    }

    /**
     * Récupère un éditeur par son ID interne (auto-incrémenté)
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM publishers WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Vérifie si un éditeur existe déjà via son ID officiel Comic Vine
     */
    public function existsByPublisherId(int $publisherId): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM publishers WHERE publisher_id = ?");
        $stmt->execute([$publisherId]);
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Récupère un éditeur complet via son ID officiel Comic Vine
     */
    public function getByPublisherId(int $publisherId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM publishers WHERE publisher_id = ?");
        $stmt->execute([$publisherId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /* ============================================================
       MÉTHODES D'ÉCRITURE / MODIFICATION (CREATE / UPDATE)
    ============================================================ */

    /**
     * Insère un nouvel éditeur importé depuis Comic Vine
     */
    public function insertComicVine(array $cv): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO publishers
            (publisher_id, name, logo, actif, last_sync)
            VALUES (?, ?, ?, 1, NOW())
        ");

        $stmt->execute([
            (int)$cv['publisher_id'],
            trim($cv['name']),
            $cv['logo']
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * METTRE À JOUR UN ÉDITEUR (Rapatrié du contrôleur pour un code propre !)
     */
    public function update(int $id, string $name, ?string $logoFilename, int $actif): bool
    {
        $stmt = $this->db->prepare("
            UPDATE publishers SET
                name = ?,
                logo = ?,
                actif = ?,
                last_sync = NOW()
            WHERE id = ?
        ");

        return $stmt->execute([
            trim($name),
            $logoFilename,
            $actif,
            $id
        ]);
    }

    /**
     * Active ou désactive un éditeur (Bascule d'état booléen)
     */
    public function toggleActif(int $id): int
    {
        $stmt = $this->db->prepare("SELECT actif FROM publishers WHERE id = ?");
        $stmt->execute([$id]);
        $current = (int)$stmt->fetchColumn();

        $new = $current ? 0 : 1;

        $stmt = $this->db->prepare("UPDATE publishers SET actif = ? WHERE id = ?");
        $stmt->execute([$new, $id]);

        return $new;
    }
}