<?php

return [
    /*
     * single: one portal for one Minecraft server (self-hosted). Plugin URL and keys
     *         come from this .env.
     * hosted: the multi-tenant app on *.mtportal.nl; the tenant comes from the domain
     *         and the OMT website manages tenants through the provisioning API.
     */
    'mode' => env('PORTAL_MODE', 'single') === 'hosted' ? 'hosted' : 'single',

    /*
     * Demo mode: fake plugin data and a seeded portal, for screenshots and trying the
     * portal without a Minecraft server. Never in production.
     */
    'demo' => (bool) env('PORTAL_DEMO', false) && env('APP_ENV') !== 'production',

    // How long a link from `php artisan portal:admin-link` stays valid.
    'admin_link_hours' => 24,
];
