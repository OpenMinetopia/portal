<?php

return [
    'api' => [
        // Set per tenant by App\Tenancy\TenantConfigBootstrapper.
        'url' => env('PLUGIN_API_URL'),
        'key' => env('PLUGIN_API_KEY'),
    ],

    'server_address' => env('MC_SERVER_ADDRESS'),

];
