<?php

declare(strict_types=1);

use App\Models\Domain;
use App\Models\Tenant;

return [
    'tenant_model' => Tenant::class,
    'id_generator' => null, // The website sends the id.
    'domain_model' => Domain::class,

    /*
     * Hosts that never resolve to a tenant. Only these serve the provisioning API.
     */
    'central_domains' => array_values(array_filter(array_map('trim', explode(',', (string) env('TENANCY_CENTRAL_DOMAINS', 'central.mtportal.nl'))))),

    'bootstrappers' => [
        Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper::class,
        App\Tenancy\TenantConfigBootstrapper::class,
        App\Tenancy\TenantStorageBootstrapper::class,
    ],

    'database' => [
        'central_connection' => env('DB_CONNECTION', 'mysql'),

        // Tenant databases live on the same MySQL server as the central one; each
        // tenant brings its own database name, username and password.
        'template_tenant_connection' => env('TENANCY_TEMPLATE_CONNECTION', 'mysql'),

        'prefix' => 'tenant',
        'suffix' => '',

        'managers' => [
            'sqlite' => Stancl\Tenancy\TenantDatabaseManagers\SQLiteDatabaseManager::class,
            'mysql' => Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager::class,
            'mariadb' => Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager::class,
            'pgsql' => Stancl\Tenancy\TenantDatabaseManagers\PostgreSQLDatabaseManager::class,
        ],
    ],

    'cache' => [
        'tag_base' => 'tenant',
    ],

    'filesystem' => [
        'suffix_base' => 'tenant',
        'disks' => [],
        'root_override' => [],
        'suffix_storage_path' => false,
        'asset_helper_tenancy' => false,
    ],

    'redis' => [
        'prefix_base' => 'tenant',
        'prefixed_connections' => [],
    ],

    'features' => [],

    // Tenant routes are the normal web and api routes; there is no routes/tenant.php.
    'routes' => false,

    'migration_parameters' => [
        '--force' => true,
        '--path' => [database_path('migrations')],
        '--realpath' => true,
    ],

    'seeder_parameters' => [
        '--class' => 'DatabaseSeeder',
    ],

    /*
     * Provisioning API (central domain only), signed by the OMT website.
     */
    'provisioning' => [
        'secret' => env('PORTAL_PROVISIONING_SECRET'),
        'max_clock_skew' => 300,
        'nonce_ttl' => 600,
    ],

    // How long the one-time admin claim link from the website stays valid.
    'admin_claim_ttl_minutes' => (int) env('ADMIN_CLAIM_TTL_MINUTES', 30),

    // Scheme of the tenant URLs the provisioning API hands out.
    'tenant_url_scheme' => env('TENANT_URL_SCHEME', 'https'),
];
