<?php

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
