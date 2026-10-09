<?php

use App\Demo\DemoWorld;
use App\Demo\FakeMojangApiService;
use App\Demo\FakePluginApiService;
use App\Models\User;
use App\Services\MojangApiService;
use App\Services\Plugin\PluginApiService;
use Database\Seeders\DemoSeeder;
use Illuminate\Support\Facades\Http;

it('refuses to seed demo data outside demo mode', function () {
    $this->seed(DemoSeeder::class);
})->throws(RuntimeException::class, 'PORTAL_DEMO=true');

it('fills a portal that works without a minecraft server', function () {
    Http::preventStrayRequests();
    config(['portal.demo' => true]);
    $this->app->bind(PluginApiService::class, FakePluginApiService::class);
    $this->app->bind(MojangApiService::class, FakeMojangApiService::class);

    $this->seed(DemoSeeder::class);
    $this->seed(DemoSeeder::class); // Twice is fine.

    $this->post('http://portaal.test/login', ['email' => DemoSeeder::EMAIL, 'password' => DemoSeeder::PASSWORD])->assertRedirect();

    $this->get('http://portaal.test/')->assertOk()->assertSee('Burgemeester');
    $this->get('http://portaal.test/portal/bank-accounts')->assertOk()->assertSee('Gemeente Westerdam');
    $this->get('http://portaal.test/portal/plots')->assertOk()->assertSee('westerdam_stadhuis');
    $this->get('http://portaal.test/plots/te-koop')->assertOk()->assertSee('westerdam_kade_12');
    $this->get('http://portaal.test/portal/companies')->assertOk();
    $this->get('http://portaal.test/portal/permits')->assertOk()->assertSee('Bouwvergunning');
    $this->get('http://portaal.test/portal/admin/settings')->assertOk();

    expect(User::count())->toBe(4);
});

it('has a demo login only in demo mode', function () {
    $this->get('http://portaal.test/demo')->assertNotFound();

    config(['portal.demo' => true]);
    $this->app->bind(PluginApiService::class, FakePluginApiService::class);
    $this->app->bind(MojangApiService::class, FakeMojangApiService::class);
    $this->seed(DemoSeeder::class);

    $this->get('http://portaal.test/login')->assertSee(DemoSeeder::EMAIL);
    $this->get('http://portaal.test/demo?theme=dark&naar=/portal/plots')->assertOk()->assertSee('"dark"', false)->assertSee('"\/portal\/plots"', false);
    $this->assertAuthenticated();
    $this->get('http://portaal.test/demo?naar=//evil.example')->assertSee('location.replace("\/")', false);
});

it('only shows a bank account to its own users', function () {
    config(['portal.demo' => true]);
    $this->app->bind(PluginApiService::class, FakePluginApiService::class);
    $this->app->bind(MojangApiService::class, FakeMojangApiService::class);
    $this->seed(DemoSeeder::class);

    $this->post('http://portaal.test/login', ['email' => DemoSeeder::EMAIL, 'password' => DemoSeeder::PASSWORD])->assertRedirect();

    $this->get('http://portaal.test/portal/bank-accounts/c3e85b0a-6d14-4f2e-b7a9-52d0e8f1a6c4')->assertOk()->assertSee('Gemeente Westerdam');
    $this->get('http://portaal.test/portal/bank-accounts/e9a24f71-3b8c-4d05-a6e2-8f1c7d3b2a90')->assertForbidden()->assertDontSee('Bakkerij Lotte');
    $this->get('http://portaal.test/portal/bank-accounts/'.DemoWorld::PLAYERS['Lotte'])->assertForbidden();
});
