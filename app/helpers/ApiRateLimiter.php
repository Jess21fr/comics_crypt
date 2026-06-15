<?php

class ApiRateLimiter
{
    /* ============================================================
       LOG : Enregistre l'intention de requête pour un endpoint
    ============================================================ */
    public static function logRequest(PDO $db, string $endpoint): void
    {
        $stmt = $db->prepare("
            INSERT INTO api_requests (endpoint, created_at)
            VALUES (?, NOW())
        ");
        $stmt->execute([$endpoint]);
    }

    /* ============================================================
       USAGE : Statistiques sur 1 heure glissante PAR ENDPOINT
    ============================================================ */
    public static function getUsageForEndpoint(PDO $db, string $endpoint): array
    {
        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM api_requests
            WHERE endpoint = ?
            AND created_at >= (NOW() - INTERVAL 1 HOUR)
        ");
        $stmt->execute([$endpoint]);

        $used = (int)$stmt->fetchColumn();
        $remaining = max(0, 200 - $used);

        return [
            'used'      => $used,
            'remaining' => $remaining
        ];
    }

    /* ============================================================
       CHECK : Analyse des verrous spécifiques à l'endpoint
    ============================================================ */
    public static function canMakeRequest(PDO $db, string $endpoint): bool
    {
        // 1. Quota horaire propre à cet endpoint (limite de 200/h)
        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM api_requests
            WHERE endpoint = ?
            AND created_at >= (NOW() - INTERVAL 1 HOUR)
        ");
        $stmt->execute([$endpoint]);
        if ((int)$stmt->fetchColumn() >= 200) {
            return false;
        }

        // 2. Espacement anti-flood de 2 secondes propre à cet endpoint
        // (Empêche deux scripts asynchrones de frapper le même endpoint en même temps)
        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM api_requests
            WHERE endpoint = ?
            AND created_at >= (NOW() - INTERVAL 2 SECOND)
        ");
        $stmt->execute([$endpoint]);
        if ((int)$stmt->fetchColumn() > 0) {
            return false;
        }

        return true;
    }

    /* ============================================================
       WAIT : Mise en pause du thread PHP si l'endpoint est occupé
    ============================================================ */
    public static function waitUntilAllowed(PDO $db, string $endpoint): void
    {
        while (!self::canMakeRequest($db, $endpoint)) {
            usleep(400000); // Pause de 0.4 seconde avant de revérifier la BDD
        }
    }

    /* ============================================================
       PERFORM : Exécution sécurisée et isolée par endpoint
    ============================================================ */
    public static function performRequest(PDO $db, string $endpoint, string $url): array
    {
        // 1. On attend que l'endpoint ciblé soit disponible (respect des 2s et du quota)
        self::waitUntilAllowed($db, $endpoint);

        // 2. CRUCIAL : On enregistre le log IMMEDIATEMENT avant l'appel réseau.
        // Cela crée le verrou en BDD pour bloquer les requêtes AJAX concurrentes
        // qui cibleraient le même endpoint au même instant.
        self::logRequest($db, $endpoint);

        // 3. Configuration du contexte HTTP avec le User-Agent requis par Comic Vine
        $options = [
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: ComicsCrypt/1.0 (XAMPP local proxy; contact: dev@local.com)\r\n",
                'timeout' => 10 // Évite de figer PHP si l'API met du temps à répondre
            ]
        ];
        $context = stream_context_create($options);

        // 4. Consommation du flux de l'API
        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            return ['status_code' => 0, 'error' => "Impossible de récupérer les données pour l'endpoint : {$endpoint}"];
        }

        return json_decode($response, true) ?: [];
    }
}