<?php

return [
    'plugin_api' => [
        'url' => env('MINETOPIA_PLUGIN_API_URL', 'http://localhost:8080'),
        'key' => env('MINETOPIA_PLUGIN_API_KEY'),
        'timeout' => env('MINETOPIA_PLUGIN_API_TIMEOUT', 5),
    ],

    'server_address' => env('MC_SERVER_ADDRESS', 'demo.openminetopia.nl'),

];
