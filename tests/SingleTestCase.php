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

    public const DATABASE = 'omt_portal_test_single';

    private const ENV = [
        'PORTAL_MODE' => 'single',
        'DB_DATABASE' => self::DATABASE,
        'PLUGIN_API_URL' => 'http://127.0.0.1:4567',
        'PLUGIN_API_KEY' => 'plt_single',
        'MINECRAFT_API_KEY' => 'ist_single',
        'MC_SERVER_ADDRESS' => 'play.single.nl',
    ];

    private array $previous = [];

    public function createApplication()
    {
        foreach (self::ENV as $key => $value) {
            $this->previous[$key] = $_ENV[$key] ?? null;
            $_ENV[$key] = $_SERVER[$key] = $value;
            putenv("{$key}={$value}");
        }

        TestCase::server()->exec('CREATE DATABASE IF NOT EXISTS `'.self::DATABASE.'`');

        return parent::createApplication();
    }

    /** Before RefreshDatabase runs migrate:fresh. */
    protected function beforeRefreshingDatabase()
    {
        if ($this->app->configurationIsCached() || config('database.connections.mysql.database') !== self::DATABASE) {
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
