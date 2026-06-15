<?php

require_once __DIR__ . '/../helpers/ApiRateLimiter.php';

class ComicVineApi
{
    private PDO $db;
    private string $apiKey;
    private string $baseUrl = "https://comicvine.gamespot.com/api/";

    public function __construct(PDO $db)
    {
        $this->db = $db;

        $config = require __DIR__ . '/../Config/config.php';
        $this->apiKey = $config['comicvine_api_key'];
    }

    private function request(string $endpoint, string $params): array
    {
        $url = $this->baseUrl . $endpoint . "/?api_key=" . $this->apiKey . "&format=jsonp&" . $params;

        return ApiRateLimiter::performRequest($this->db, $endpoint, $url);
    }

    /* ============================================================
       PUBLISHERS
    ============================================================ */
    public function getPublishersByName(string $name): array
    {
        // équivalent de :
        // /publishers/?api_key=...&filter=name:{nom}&format=json
        return $this->request("publishers", "filter=name:" . urlencode($name));
    }

    /* ============================================================
       VOLUMES
    ============================================================ */
    public function searchVolumes(string $query): array
    {
        return $this->request("search", "query=" . urlencode($query) . "&resources=volume");
    }

    public function getVolume(int $volumeId): array
    {
        return $this->request("volume", "filter=id:$volumeId");
    }

    /* ============================================================
       SERIES
    ============================================================ */
    public function searchSeries(string $query): array
    {
        return $this->request("search", "query=" . urlencode($query) . "&resources=series");
    }

    public function getSeries(int $seriesId): array
    {
        return $this->request("series", "filter=id:$seriesId");
    }

    /* ============================================================
       ISSUES
    ============================================================ */
    public function getIssue(int $issueId): array
    {
        return $this->request("issue", "filter=id:$issueId");
    }

    public function getIssuesBySeries(int $seriesId, int $page = 1): array
    {
        return $this->request("issues", "filter=series:$seriesId&offset=" . (($page - 1) * 100));
    }

    /* ============================================================
       SEARCH GLOBAL
    ============================================================ */
    public function searchAll(string $query): array
    {
        return $this->request("search", "query=" . urlencode($query) . "&resources=publisher,volume,series,issue");
    }
}
