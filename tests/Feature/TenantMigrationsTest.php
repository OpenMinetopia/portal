<?php

use App\Models\Tenant;
use App\Models\User;
use App\Services\Tenancy\TenantMigrator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

beforeEach(function () {
    $this->legacyDatabase = TestCase::tenantDatabase('legacy');
    $server = TestCase::server();
    $server->exec("DROP DATABASE IF EXISTS `{$this->legacyDatabase}`; CREATE DATABASE `{$this->legacyDatabase}`");
    $server->exec("USE `{$this->legacyDatabase}`");
    $server->exec(file_get_contents(base_path('tests/Fixtures/legacy-portal.sql')));

    $this->legacyMigrations = collect(DB::select("SELECT migration FROM `{$this->legacyDatabase}`.migrations"))->pluck('migration')->sort()->values()->all();
    $this->migrationFiles = collect(glob(database_path('migrations/*.php')))->map(fn ($file) => basename($file, '.php'))->sort()->values()->all();
    $this->newerMigrations = array_values(array_diff($this->migrationFiles, $this->legacyMigrations));
});

afterEach(function () {
    TestCase::server()->exec("DROP DATABASE IF EXISTS `{$this->legacyDatabase}`");
});

it('keeps the same migration file names the existing databases ran', function () {
    expect(array_values(array_diff($this->legacyMigrations, $this->migrationFiles)))->toBe([]);
});

it('only runs the newer migrations on an existing portal database and keeps its data', function () {
    $tenant = $this->createTenant('legacy', ['tenancy_db_name' => $this->legacyDatabase]);

    expect(app(TenantMigrator::class)->pendingFor($tenant))->toBe($this->newerMigrations);

    $this->artisan('tenants:migrate-safe')
        ->expectsOutputToContain('1 tenants, 0 failed.')
        ->assertSuccessful();

    expect(DB::table("{$this->legacyDatabase}.migrations")->count())->toBe(count($this->migrationFiles))
        ->and($tenant->run(fn () => User::where('email', 'owner@legacy.test')->first()->isAdmin()))->toBeTrue();
});

it('lets the provisioning api adopt an existing database and run only the newer migrations', function () {
    $id = (string) Str::uuid();

    provision('PUT', "/tenants/{$id}", desiredState('legacy', ['database' => ['name' => $this->legacyDatabase]]))
        ->assertOk()
        ->assertJsonPath('migrations.ran', $this->newerMigrations);

    $this->get('http://legacy.mtportal.test/login')->assertOk();
});

it('migrates every tenant and does not stop at a broken one', function () {
    $broken = $this->createTenant('broken', ['tenancy_db_name' => 'omt_portal_test_missing', 'name' => 'Aaa kapot']);
    $this->createTenant('legacy', ['tenancy_db_name' => $this->legacyDatabase, 'name' => 'Bbb legacy']);
    $this->createTenant('alpha');

    $this->artisan('tenants:migrate-safe')
        ->expectsOutputToContain('3 tenants, 1 failed.')
        ->assertFailed();

    expect(Tenant::count())->toBe(3);
});
