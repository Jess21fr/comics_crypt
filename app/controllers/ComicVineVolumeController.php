<?php

class ComicVineVolumeController
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $config = require __DIR__ . '/../config.php';
        $this->apiKey  = $config['comicvine_api_key'];
        $this->baseUrl = $config['comicvine_base_url'];
    }

    /* ============================================================
       1) Recherche du volume ComicVine correspondant à une série
    ============================================================ */
    public function findVolume()
    {
        $name      = $_POST['name'] ?? null;
        $year      = $_POST['year'] ?? null;
        $publisher = $_POST['publisher'] ?? null;

        if (!$name || !$year) {
            echo json_encode([
                'success' => false,
                'message' => 'Paramètres manquants (name, year)'
            ]);
            return;
        }

        // 1) Recherche brute ComicVine
        $url = $this->baseUrl
            . "/search/?api_key={$this->apiKey}&format=json"
            . "&query=" . urlencode($name)
            . "&resources=volume";

        $json = $this->curlGet($url);

        if (!$json || empty($json['results'])) {
            echo json_encode([
                'success' => false,
                'message' => "Aucun volume trouvé sur ComicVine"
            ]);
            return;
        }

        $results = $json['results'];

        // 2) Filtrage intelligent
        $bestMatch = null;

        foreach ($results as $vol) {

            // Vérification année
            if (!empty($vol['start_year']) && intval($vol['start_year']) !== intval($year)) {
                continue;
            }

            // Vérification éditeur si fourni
            if ($publisher && isset($vol['publisher']['name'])) {
                $cvPub = strtolower(trim($vol['publisher']['name']));
                $myPub = strtolower(trim($publisher));

                if ($cvPub !== $myPub) {
                    continue;
                }
            }

            // Vérification nom (tolérance)
            $cvName = strtolower(trim($vol['name']));
            $myName = strtolower(trim($name));

            if (similar_text($cvName, $myName) < 70) {
                continue;
            }

            // OK, c'est un bon match
            $bestMatch = $vol;
            break;
        }

        if (!$bestMatch) {
            echo json_encode([
                'success' => false,
                'message' => "Aucun volume correspondant (nom/année/éditeur)"
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'volume_id'   => $bestMatch['id'],
            'volume_name' => $bestMatch['name'],
            'start_year'  => $bestMatch['start_year'],
            'publisher'   => $bestMatch['publisher']['name'] ?? null
        ]);
    }

    /* ============================================================
       CURL GET générique
    ============================================================ */
    private function curlGet(string $url): ?array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_USERAGENT      => 'ComicsCrypt/1.0'
        ]);

        $response = curl_exec($ch);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($err || !$response) {
            return null;
        }

        return json_decode($response, true);
    }
}
