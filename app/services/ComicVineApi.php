<?php

require_once __DIR__ . '/../helpers/ApiRateLimiter.php';

class ComicVineApi
{
    private PDO $db;
    private string $apiKey;
    private string $baseUrl = "https://comicvine.gamespot.com/api/";

    /**
     * Constructeur de l'API Comic Vine
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;

        // Correction de la casse du chemin d'inclusion
        $config = require __DIR__ . '/../config/config.php';
        $this->apiKey = $config['comicvine_api_key'];
    }

    /**
     * Centralisation et exécution des requêtes HTTP vers Comic Vine
     */
    private function request(string $endpoint, string $params): array
    {
        // CORRECTION CRITIQUE : Remplacement de jsonp par json brut pour permettre l'analyse côté PHP
        $url = $this->baseUrl . $endpoint . "/?api_key=" . $this->apiKey . "&format=json&" . $params;

        return ApiRateLimiter::performRequest($this->db, $endpoint, $url);
    }

    /* ============================================================
       PUBLISHERS (ÉDITEURS)
    ============================================================ */

    /**
     * AJOUT CRITIQUE : Passerelle requise par PublishersController
     * Permet d'éviter l'erreur fatale "Call to undefined method"
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
        // Syntaxe Comic Vine : /publishers/?filter=name:{nom}
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