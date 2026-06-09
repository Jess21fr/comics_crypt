<?php

class GoogleImages
{
    /**
     * Recherche Google Images et renvoie la meilleure URL HD trouvée.
     * On utilise l’API "tbm=isch" + parsing JSON intégré dans la page.
     */
    public static function search($query)
    {
        $q = urlencode($query);

        $url = "https://www.google.com/search?tbm=isch&q={$q}";

        $html = self::curlGet($url);

        if (!$html) {
            return null;
        }

        // Google renvoie un JSON dans "AF_initDataCallback"
        // On extrait toutes les URLs d’images HD
        preg_match_all('/"ou":"(.*?)"/', $html, $matches);

        if (!empty($matches[1])) {
            // On renvoie la première URL HD trouvée
            return $matches[1][0];
        }

        return null;
    }

    /**
     * Télécharge une image depuis une URL Google Images.
     */
    public static function download($url, $dest)
    {
        $data = self::curlGet($url);

        if (!$data) {
            return false;
        }

        // Création du dossier si nécessaire
        $dir = dirname($dest);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return file_put_contents($dest, $data) !== false;
    }

    /**
     * cURL GET générique
     */
    private static function curlGet($url)
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows NT 10.0; Win64; x64)",
            CURLOPT_TIMEOUT        => 10
        ]);

        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }
}
