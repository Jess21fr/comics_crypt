<?php

class ApiRateLimiter
{
    /* ============================================================
       LOG : enregistre une requête
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
       USAGE : nombre de requêtes sur 1h glissante
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
       CHECK : peut-on faire une requête maintenant ?
    ============================================================ */
    public static function canMakeRequest(PDO $db, string $endpoint): bool
    {
        // quota 200/h
        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM api_requests
            WHERE endpoint = ?
            AND created_at >= (NOW() - INTERVAL 1 HOUR)
        ");
        $stmt->execute([$endpoint]);
        $used = (int)$stmt->fetchColumn();

        if ($used >= 200) {
            return false;
        }

        // fréquence : 1 requête toutes les 2 secondes
        $stmt = $db->prepare("
            SELECT created_at
            FROM api_requests
            WHERE endpoint = ?
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$endpoint]);
        $last = $stmt->fetchColumn();

        if ($last) {
            $lastTime = strtotime($last);
            if (time() - $lastTime < 2) {
                return false;
            }
        }

        return true;
    }

    /* ============================================================
       WAIT : attendre automatiquement le bon moment
    ============================================================ */
    public static function waitUntilAllowed(PDO $db, string $endpoint): void
    {
        while (!self::canMakeRequest($db, $endpoint)) {
            usleep(500000); // 0.5 seconde
        }
    }

    /* ============================================================
       REQUEST : exécuter une requête ComicVine en sécurité
    ============================================================ */
    public static function performRequest(PDO $db, string $endpoint, string $url): array
    {
        self::waitUntilAllowed($db, $endpoint);

        $response = @file_get_contents($url);

        self::logRequest($db, $endpoint);

        return $response ? json_decode($response, true) : [];
    }
}
