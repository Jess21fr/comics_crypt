<?php

return [

    // Configuration de la base de données (XAMPP local)
    'db' => [
        'host'    => 'localhost',
        'name'    => 'comics_crypt',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],

    // Racine publique du projet
    'base_url' => '/comics_crypt/public',

    // Configuration de l'API ComicVine
    'comicvine_api_key'  => 'b3dbb0ee3b99331ebcf840f56441b0b87771f0e3',
    'comicvine_base_url' => 'https://comicvine.gamespot.com/api',
    
    // Crucial : ComicVine exige un User-Agent personnalisé sous peine de bloquer la requête (Erreur 403)
    'comicvine_user_agent' => 'ComicsCryptApp/1.0 (Contact: ton-email-ou-pseudo)',
];