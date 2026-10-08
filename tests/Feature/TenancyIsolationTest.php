<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->alpha = $this->createTenant('alpha');
    $this->beta = $this->createTenant('beta');
});

function makeUser(Tenant $tenant, string $email = 'speler@example.com'): User
{
    return $tenant->run(fn () => User::create([
        'name' => 'Speler',
        'email' => $email,
        'minecraft_username' => 'Speler'.Str::random(4),
        'password' => Hash::make('geheim123'),
        'token' => Str::random(32),
        'minecraft_verified' => true,
    ]));
}

it('keeps users apart per tenant', function () {
    makeUser($this->alpha);

    expect($this->alpha->run(fn () => User::count()))->toBe(1)
        ->and($this->beta->run(fn () => User::count()))->toBe(0);

    $this->post($this->tenantUrl('beta', '/login'), ['email' => 'speler@example.com', 'password' => 'geheim123'])
        ->assertSessionHasErrors('email');
    $this->assertGuest();

    $this->post($this->tenantUrl('alpha', '/login'), ['email' => 'speler@example.com', 'password' => 'geheim123'])
        ->assertRedirect();
    $this->assertAuthenticated();
});

it('stores sessions in the tenant database and does not carry them to another tenant', function () {
    makeUser($this->alpha);

    $login = $this->post($this->tenantUrl('alpha', '/login'), ['email' => 'speler@example.com', 'password' => 'geheim123']);
    $cookie = $login->getCookie(config('session.cookie'), false);
    $sessionId = \Illuminate\Cookie\CookieValuePrefix::remove(app('encrypter')->decrypt($cookie->getValue(), false));

    expect(DB::table(\Tests\TestCase::tenantDatabase('alpha').'.sessions')->where('id', $sessionId)->value('user_id'))->not->toBeNull()
        ->and(DB::table(\Tests\TestCase::tenantDatabase('beta').'.sessions')->where('id', $sessionId)->exists())->toBeFalse();

    // The same cookie on the other portal is just an unknown session.
    $this->flushSession();
    app('auth')->forgetGuards();

    $this->withUnencryptedCookie(config('session.cookie'), $cookie->getValue())
        ->get($this->tenantUrl('beta', '/'))
        ->assertRedirect($this->tenantUrl('beta', '/login'));
});

it('gives every tenant its own public files with relative urls', function () {
    $alphaUrl = $this->alpha->run(function () {
        Storage::disk('public')->put('plot-listings/huis.jpg', 'alpha');

        return Storage::disk('public')->url('plot-listings/huis.jpg');
    });

    expect($alphaUrl)->toBe('/storage/tenants/'.$this->alpha->id.'/plot-listings/huis.jpg')
        ->and(File::exists(storage_path('app/public/tenants/'.$this->alpha->id.'/plot-listings/huis.jpg')))->toBeTrue()
        ->and($this->beta->run(fn () => Storage::disk('public')->exists('plot-listings/huis.jpg')))->toBeFalse()
        ->and(Storage::disk('public')->exists('plot-listings/huis.jpg'))->toBeFalse();

    File::deleteDirectory(storage_path('app/public/tenants/'.$this->alpha->id));
});

it('sets the config of the tenant for its own host', function () {
    $this->get($this->tenantUrl('alpha', '/login'))->assertOk()->assertSee('Alpha Portaal')->assertDontSee('Beta Portaal');
    $this->get($this->tenantUrl('beta', '/login'))->assertOk()->assertSee('Beta Portaal')->assertDontSee('Alpha Portaal');

    tenancy()->end();

    $config = fn () => [
        config('app.name'), config('plugin.api.url'), config('plugin.api.key'),
        config('services.minecraft.api_key'), config('plugin.server_address'),
    ];

    expect($this->alpha->run($config))->toBe(['Alpha Portaal', 'http://93.184.216.34:25570', 'plt_alpha', 'ist_alpha', 'play.alpha.nl'])
        ->and($this->beta->run($config))->toBe(['Beta Portaal', 'http://93.184.216.34:25570', 'plt_beta', 'ist_beta', 'play.beta.nl'])
        ->and(config('services.minecraft.api_key'))->toBe('central-key-must-never-work');
});

it('never falls back to the central keys when a tenant has none', function () {
    $this->alpha->update(['minecraft_api_key' => null, 'plugin_api_key' => null]);

    expect($this->alpha->fresh()->run(fn () => [config('services.minecraft.api_key'), config('plugin.api.key')]))->toBe([null, null]);

    $this->postJson($this->tenantUrl('alpha', '/api/minecraft/verify'), [], ['X-API-Key' => 'central-key-must-never-work'])
        ->assertStatus(401);
});

it('accepts only the tenant\'s own api key', function () {
    $url = $this->tenantUrl('alpha', '/api/minecraft/verify');

    $this->postJson($url, [], ['X-API-Key' => 'ist_beta'])->assertStatus(401)->assertJson(['error_code' => 'invalid_api_key']);
    $this->postJson($url, [], ['X-API-Key' => 'central-key-must-never-work'])->assertStatus(401);
    $this->postJson($url, [])->assertStatus(401);

    // Past the key check: the request itself is then validated.
    $this->postJson($url, [], ['X-API-Key' => 'ist_alpha'])->assertStatus(422);
});

it('shows a clean 404 for an unknown domain', function () {
    $this->get('http://nobody.mtportal.test/login')
        ->assertNotFound()
        ->assertSee('Hier staat geen portaal');
});

it('does not serve the portal on the central domain', function () {
    $this->get('http://'.self::CENTRAL_DOMAIN.'/login')->assertNotFound();
    $this->postJson('http://'.self::CENTRAL_DOMAIN.'/api/minecraft/verify')->assertNotFound();
});

it('shows the paused page for an inactive tenant and answers 423 on the api', function () {
    $this->alpha->update(['status' => Tenant::INACTIVE]);

    $this->get($this->tenantUrl('alpha', '/login'))
        ->assertStatus(503)
        ->assertSee('Dit portaal is even gepauzeerd')
        ->assertSee('Alpha Portaal');

    $this->postJson($this->tenantUrl('alpha', '/api/minecraft/verify'), [], ['X-API-Key' => 'ist_alpha'])
        ->assertStatus(423)
        ->assertJson(['error_code' => 'portal_paused']);

    $this->get($this->tenantUrl('beta', '/login'))->assertOk();
});

it('throttles logins per tenant and ip', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->post($this->tenantUrl('alpha', '/login'), ['email' => 'x@example.com', 'password' => 'fout']);
    }

    $this->post($this->tenantUrl('alpha', '/login'), ['email' => 'x@example.com', 'password' => 'fout'])->assertStatus(429);
    $this->post($this->tenantUrl('beta', '/login'), ['email' => 'x@example.com', 'password' => 'fout'])->assertStatus(302);
});

it('throttles minecraft verification per tenant and ip', function () {
    for ($i = 0; $i < 30; $i++) {
        $this->postJson($this->tenantUrl('alpha', '/api/minecraft/verify'), [], ['X-API-Key' => 'ist_alpha']);
    }

    $this->postJson($this->tenantUrl('alpha', '/api/minecraft/verify'), [], ['X-API-Key' => 'ist_alpha'])->assertStatus(429);
    $this->postJson($this->tenantUrl('beta', '/api/minecraft/verify'), [], ['X-API-Key' => 'ist_beta'])->assertStatus(422);
});
