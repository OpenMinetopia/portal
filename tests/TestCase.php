<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PDO;

/**
 * Hosted mode (PORTAL_MODE=hosted in phpunit.xml). Single mode has its own base
 * class in tests/Single.
 *
 * Runs against MySQL/MariaDB: a central database plus one database per test
 * tenant, the way the portal runs in production. Databases are migrated once per
 * run; before every test the central tables and all tenant data except the rows
 * the migrations seed are wiped.
 */
abstract class TestCase extends BaseTestCase
{
    public const CENTRAL_DOMAIN = 'central.mtportal.test';

    public const TENANTS = [
        'alpha' => '11111111-1111-4111-8111-111111111111',
        'beta' => '22222222-2222-4222-8222-222222222222',
    ];

    private static bool $prepared = false;

    /** @var array<string, list<string>> tables with seeded rows, per tenant database */
    private static array $seededTables = [];

    protected function setUp(): void
    {
        parent::setUp();

        // A cached config ignores phpunit.xml, and setup below runs migrate:fresh.
        if ($this->app->configurationIsCached() || config('database.connections.mysql.database') !== env('DB_DATABASE')) {
            throw new \RuntimeException('Run `php artisan config:clear` first: the tests would use the wrong database.');
        }

        $this->withoutVite();

        if (! self::$prepared) {
            $this->prepareDatabases();
            self::$prepared = true;
        }

        $this->resetDatabases();
    }

    protected function tearDown(): void
    {
        if (tenancy()->initialized) {
            tenancy()->end();
        }

        parent::tearDown();
    }

    public static function server(): PDO
    {
        return new PDO('mysql:host='.env('DB_HOST').';port='.env('DB_PORT'), env('DB_USERNAME'), env('DB_PASSWORD'), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    public static function tenantDatabase(string $key): string
    {
        return env('DB_DATABASE').'_'.$key;
    }

    protected function createTenant(string $key = 'alpha', array $attributes = []): Tenant
    {
        $tenant = Tenant::create($attributes + [
            'id' => self::TENANTS[$key] ?? (string) Str::uuid(),
            'name' => ucfirst($key).' Portaal',
            'status' => Tenant::ACTIVE,
            'tenancy_db_name' => self::tenantDatabase($key),
            'tenancy_db_username' => env('DB_USERNAME'),
            'tenancy_db_password' => env('DB_PASSWORD'),
            'plugin_api_url' => 'http://93.184.216.34:25570',
            'plugin_api_key' => "plt_{$key}",
            'minecraft_api_key' => "ist_{$key}",
            'server_address' => "play.{$key}.nl",
        ]);

        $tenant->domains()->create(['domain' => $this->tenantHost($key), 'is_primary' => true]);

        return $tenant;
    }

    protected function tenantHost(string $key): string
    {
        return "{$key}.mtportal.test";
    }

    protected function tenantUrl(string $key, string $path = '/'): string
    {
        return 'http://'.$this->tenantHost($key).$path;
    }

    private function prepareDatabases(): void
    {
        $server = self::server();
        $server->exec('CREATE DATABASE IF NOT EXISTS `'.env('DB_DATABASE').'`');

        Artisan::call('migrate:fresh', ['--force' => true, '--path' => 'database/migrations/central']);

        foreach (array_keys(self::TENANTS) as $key) {
            $database = self::tenantDatabase($key);
            $server->exec("DROP DATABASE IF EXISTS `{$database}`");
            $server->exec("CREATE DATABASE `{$database}`");

            $result = app(\App\Services\Tenancy\TenantMigrator::class)->migrate($this->createTenant($key));
            if (! $result['ok']) {
                throw new \RuntimeException("Migrating {$database} failed: {$result['error']}");
            }

            self::$seededTables[$database] = collect(DB::select('SELECT TABLE_NAME AS name, TABLE_ROWS AS est FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?', [$database]))
                ->pluck('name')
                ->filter(fn ($table) => $table === 'migrations' || $server->query("SELECT COUNT(*) FROM `{$database}`.`{$table}`")->fetchColumn() > 0)
                ->values()->all();
        }
    }

    private function resetDatabases(): void
    {
        $server = self::server();
        $server->exec('SET FOREIGN_KEY_CHECKS=0');

        foreach (['domains', 'tenants', 'cache', 'cache_locks'] as $table) {
            $server->exec('TRUNCATE `'.env('DB_DATABASE')."`.`{$table}`");
        }

        foreach (self::$seededTables as $database => $keep) {
            $tables = $server->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '{$database}'")->fetchAll(PDO::FETCH_COLUMN);

            foreach (array_diff($tables, $keep) as $table) {
                $server->exec("TRUNCATE `{$database}`.`{$table}`");
            }
        }

        $server->exec('SET FOREIGN_KEY_CHECKS=1');
    }
}
