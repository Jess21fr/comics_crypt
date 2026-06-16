<?php

require_once __DIR__ . '/../helpers/ApiRateLimiter.php';

class ComicVineApi
{
    private PDO $db;
    private string $apiKey;
    private string $baseUrl = "https://comicvine.gamespot.com/api/";

    /**
     * Constructeur de l'API Comic Vine
     * MODIFICATION : $db devient optionnel (?PDO) pour permettre l'appel statique sécurisé
     */
    public function __construct(?PDO $db = null)
    {
        if ($db === null) {
            // Si le contrôleur appelle l'API sans lui passer PDO, on s'auto-connecte proprement
            $config = require __DIR__ . '/../Config/config.php';
            $this->db = new PDO(
                "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset={$config['db']['charset']}",
                $config['db']['user'],
                $config['db']['pass'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } else {
            $this->db = $db;
        }

        // On charge la clé API globale
        $config = require __DIR__ . '/../Config/config.php';
        $this->apiKey = $config['comicvine_api_key'];
    }

    /**
     * Centralisation et exécution des requêtes HTTP vers Comic Vine
     */
    private function request(string $endpoint, string $params): array
    {
        // Remplacement de jsonp par json brut pour permettre l'analyse côté PHP
        $url = $this->baseUrl . $endpoint . "/?api_key=" . $this->apiKey . "&format=json&" . $params;

        return ApiRateLimiter::performRequest($this->db, $endpoint, $url);
    }

    /**
     * 🟢 AJOUT CRITIQUE POUR LE CONTROLLER
     * Permet d'effectuer des appels dynamiques (avec pagination, filtres complexes)
     * tout en passant sagement au crible de ton ApiRateLimiter !
     */
    public static function call(string $endpoint, array $params = []): array
    {
        $instance = new self();
        $endpoint = trim($endpoint, '/');
        
        // On transforme le tableau de filtres en chaîne de paramètres GET
        $queryString = http_build_query($params);
        
        return $instance->request($endpoint, $queryString);
    }

    /* ============================================================
       PUBLISHERS (ÉDITEURS)
    ============================================================ */

    /**
     * Passerelle requise par PublishersController
     */
    public function search(string $name): array
    {
        return $this->getPublishersByName($name);
    }

    /**
     * Recherche des éditeurs par leur nom exact ou partiel
     */
    public function getPublishersByName(string $name): array
    {
        return $this->request("publishers", "filter=name:" . urlencode($name));
    }

    /* ============================================================
       VOLUMES (SÉRIES EN VERSION COMICVINE)
    ============================================================ */

    /**
     * Recherche globale de volumes par mots-clés
     */
    public function searchVolumes(string $query): array
    {
        return $this->request("search", "query=" . urlencode($query) . "&resources=volume");
    }

    /**
     * Récupère les détails d'un volume spécifique via son ID
     */
    public function getVolume(int $volumeId): array
    {
        return $this->request("volume", "filter=id:$volumeId");
    }

    /* ============================================================
       SERIES
    ============================================================ */

    /**
     * Recherche globale de séries par mots-clés
     */
    public function searchSeries(string $query): array
    {
        return $this->request("search", "query=" . urlencode($query) . "&resources=series");
    }

    /**
     * Récupère les détails d'une série spécifique via son ID
     */
    public function getSeries(int $seriesId): array
    {
        return $this->request("series", "filter=id:$seriesId");
    }

    /* ============================================================
       ISSUES (NUMÉROS / COMICS INDIVIDUELS)
    ============================================================ */

    /**
     * Récupère les détails d'un numéro spécifique via son ID
     */
    public function getIssue(int $issueId): array
    {
        return $this->request("issue", "filter=id:$issueId");
    }

    /**
     * Récupère la liste des numéros liés à une série avec système de pagination
     */
    public function getIssuesBySeries(int $seriesId, int $page = 1): array
    {
        $offset = ($page - 1) * 100;
        return $this->request("issues", "filter=series:$seriesId&offset=" . $offset);
    }

    /* ============================================================
       SEARCH GLOBAL
    ============================================================ */

    /**
     * Recherche multi-ressources simultanée (Éditeurs, Volumes, Séries, Numéros)
     */
    public function searchAll(string $query): array
    {
        return $this->request("search", "query=" . urlencode($query) . "&resources=publisher,volume,series,issue");
    }
}