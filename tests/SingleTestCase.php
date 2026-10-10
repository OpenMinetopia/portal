<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Single mode: one self-hosted portal with one database, as after a fresh clone.
 * phpunit.xml sets hosted mode for tests/Feature; these tests switch it back
 * before the application boots.
 */
abstract class SingleTestCase extends BaseTestCase
{
    use RefreshDatabase;

    private static ?string $database = null;

    private const ENV = [
        'PORTAL_MODE' => 'single',
        'PLUGIN_API_URL' => 'http://127.0.0.1:4567',
        'PLUGIN_API_KEY' => 'plt_single',
        'MINECRAFT_API_KEY' => 'ist_single',
        'MC_SERVER_ADDRESS' => 'play.single.nl',
    ];

    private array $previous = [];

    /**
     * The hosted test database plus _single, so a checkout run with its own
     * DB_DATABASE (e.g. a worktree) never shares databases with another run.
     */
    public static function database(): string
    {
        return self::$database ??= (getenv('DB_DATABASE') ?: 'omt_portal_test').'_single';
    }

    public function createApplication()
    {
        foreach (self::ENV + ['DB_DATABASE' => self::database()] as $key => $value) {
            $this->previous[$key] = $_ENV[$key] ?? null;
            $_ENV[$key] = $_SERVER[$key] = $value;
            putenv("{$key}={$value}");
        }

        TestCase::server()->exec('CREATE DATABASE IF NOT EXISTS `'.self::database().'`');

        return parent::createApplication();
    }

    /** Before RefreshDatabase runs migrate:fresh. */
    protected function beforeRefreshingDatabase()
    {
        if ($this->app->configurationIsCached() || config('database.connections.mysql.database') !== self::database()) {
            throw new \RuntimeException('Run `php artisan config:clear` first: the tests would use the wrong database.');
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        foreach ($this->previous as $key => $value) {
            $_ENV[$key] = $_SERVER[$key] = $value;
            $value === null ? putenv($key) : putenv("{$key}={$value}");
        }
    }
}
