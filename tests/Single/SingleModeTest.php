<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

it('builds the whole app with a plain migrate', function () {
    expect(Schema::hasTable('users'))->toBeTrue()
        ->and(Schema::hasTable('plot_listings'))->toBeTrue()
        ->and(Schema::hasTable('tenants'))->toBeFalse()
        ->and(Role::where('is_admin', true)->exists())->toBeTrue();
});

it('serves the portal on any host without tenants', function () {
    $this->get('http://portaal.test/login')->assertOk()->assertSee(config('app.name'));
    $this->get('http://127.0.0.1:8000/login')->assertOk();
});

it('has no provisioning api', function () {
    $this->getJson('http://central.mtportal.test/internal/v1/tenants/'.Str::uuid())->assertNotFound();
});

it('uses the plugin settings from .env', function () {
    expect(config('plugin.api.url'))->toBe('http://127.0.0.1:4567')
        ->and(config('plugin.api.key'))->toBe('plt_single')
        ->and(config('services.minecraft.api_key'))->toBe('ist_single');

    $this->postJson('http://portaal.test/api/minecraft/verify', [], ['X-API-Key' => 'ist_single'])->assertStatus(422);
    $this->postJson('http://portaal.test/api/minecraft/verify', [], ['X-API-Key' => 'ist_wrong'])->assertStatus(401);
});

it('does not make the first user to register an admin', function () {
    Http::fake(['api.mojang.com/*' => Http::response(['id' => '069a79f444e94726a5befca90e38aaf5', 'name' => 'Notch'])]);

    $this->post('http://portaal.test/register', [
        'name' => 'Eigenaar', 'email' => 'eigenaar@example.com', 'minecraft_username' => 'Notch',
        'password' => 'geheim123', 'password_confirmation' => 'geheim123',
    ])->assertRedirect();

    expect(User::first()->isAdmin())->toBeFalse();
});

it('makes an admin with a one-time link from portal:admin-link', function () {
    $this->artisan('portal:admin-link')->expectsOutputToContain('http://portaal.test/beheerder-worden/')->assertSuccessful();

    $token = Cache::get('portal:admin-claim');
    expect($token)->toBeArray()->and($token['expires_at'])->toBeGreaterThan(now()->addHours(23)->getTimestamp());

    $user = User::create([
        'name' => 'Eigenaar', 'email' => 'eigenaar@example.com', 'minecraft_username' => 'Notch',
        'password' => Hash::make('geheim123'), 'token' => Str::random(32),
    ]);

    $plain = \App\Services\Tenancy\AdminClaim::issue(null);
    $this->actingAs($user)->get('http://portaal.test/beheerder-worden/'.$plain)->assertRedirect(route('dashboard'));

    expect($user->fresh()->isAdmin())->toBeTrue();

    // Used up.
    $this->actingAs($user)->get('http://portaal.test/beheerder-worden/'.$plain)->assertStatus(410)->assertSee('portal:admin-link');
});

it('lets a guest claim the link after logging in', function () {
    User::create([
        'name' => 'Eigenaar', 'email' => 'eigenaar@example.com', 'minecraft_username' => 'Notch',
        'password' => Hash::make('geheim123'), 'token' => Str::random(32),
    ]);
    $token = \App\Services\Tenancy\AdminClaim::issue(null);

    $this->get('http://portaal.test/beheerder-worden/'.$token)->assertRedirect('http://portaal.test/login');
    $this->post('http://portaal.test/login', ['email' => 'eigenaar@example.com', 'password' => 'geheim123'])->assertRedirect();

    expect(User::first()->isAdmin())->toBeTrue();
});

it('expires the admin link', function () {
    $token = \App\Services\Tenancy\AdminClaim::issue(null);

    $this->travel(25)->hours();

    $this->get('http://portaal.test/beheerder-worden/'.$token)->assertStatus(410);
});

it('shows neutral wording on error pages', function () {
    $this->get('http://portaal.test/bestaat-niet')->assertNotFound()
        ->assertSee('Gemaakt met')
        ->assertDontSee('Een portaal van');
});

it('reaches a plugin on the local machine or network without the hosted address guard', function () {
    Http::fake(['*' => Http::response(['success' => true, 'plots' => []])]);

    expect(app(\App\Services\Plugin\PluginApiService::class)->get('/api/plots'))->toBe(['success' => true, 'plots' => []]);
    Http::assertSent(fn ($request) => $request->url() === 'http://127.0.0.1:4567/api/plots' && $request->hasHeader('X-API-Key', 'plt_single'));
});
