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
});

afterEach(function () {
    TestCase::server()->exec("DROP DATABASE IF EXISTS `{$this->legacyDatabase}`");
});

it('keeps the same migration file names the existing databases ran', function () {
    $ran = collect(DB::select("SELECT migration FROM `{$this->legacyDatabase}`.migrations"))->pluck('migration')->sort()->values()->all();
    $files = collect(glob(database_path('migrations/tenant/*.php')))->map(fn ($file) => basename($file, '.php'))->sort()->values()->all();

    expect($files)->toBe($ran);
});

it('finds nothing pending on an existing portal database and keeps its data', function () {
    $tenant = $this->createTenant('legacy', ['tenancy_db_name' => $this->legacyDatabase]);

    expect(app(TenantMigrator::class)->pendingFor($tenant))->toBe([]);

    $this->artisan('tenants:migrate-safe')
        ->expectsOutputToContain('1 tenants, 0 failed.')
        ->assertSuccessful();

    expect(DB::table("{$this->legacyDatabase}.migrations")->count())->toBe(24)
        ->and($tenant->run(fn () => User::where('email', 'owner@legacy.test')->first()->isAdmin()))->toBeTrue();
});

it('lets the provisioning api adopt an existing database without migrating anything', function () {
    $id = (string) Str::uuid();

    provision('PUT', "/tenants/{$id}", desiredState('legacy', ['database' => ['name' => $this->legacyDatabase]]))
        ->assertOk()
        ->assertJsonPath('migrations.ran', []);

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
