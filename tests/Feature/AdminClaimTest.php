<?php

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Tenancy\AdminClaim;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->alpha = $this->createTenant('alpha');

    Http::fake(['api.mojang.com/*' => Http::response(['id' => '069a79f444e94726a5befca90e38aaf5', 'name' => 'Notch'])]);
});

function claimPath(Tenant $tenant): string
{
    return '/beheerder-worden/'.AdminClaim::issue($tenant);
}

/** The test client does not resend cookies, so carry the session cookie to the next request. */
function openClaim(string $url): \Illuminate\Testing\TestResponse
{
    $response = test()->get($url);
    test()->withUnencryptedCookie(config('session.cookie'), $response->getCookie(config('session.cookie'), false)->getValue());

    return $response;
}

function registerOn(string $host, array $override = []): \Illuminate\Testing\TestResponse
{
    return test()->post("http://{$host}/register", $override + [
        'name' => 'Eigenaar',
        'email' => 'eigenaar@example.com',
        'minecraft_username' => 'Notch',
        'password' => 'geheim123',
        'password_confirmation' => 'geheim123',
    ]);
}

function isAdmin(Tenant $tenant, string $email): bool
{
    return $tenant->run(fn () => User::where('email', $email)->firstOrFail()->isAdmin());
}

it('no longer makes the first user to register an admin', function () {
    registerOn('alpha.mtportal.test')->assertRedirect();

    expect(isAdmin($this->alpha, 'eigenaar@example.com'))->toBeFalse();
});

it('makes the guest who opens the link an admin after registering', function () {
    openClaim($this->tenantUrl('alpha', claimPath($this->alpha)))->assertRedirect($this->tenantUrl('alpha', '/register'));

    $this->get($this->tenantUrl('alpha', '/register'))->assertSee('Daarna ben je beheerder van dit portaal');

    registerOn('alpha.mtportal.test')->assertRedirect()->assertSessionHas('success');

    expect(isAdmin($this->alpha, 'eigenaar@example.com'))->toBeTrue()
        ->and($this->alpha->fresh()->admin_claim_token_hash)->toBeNull();
});

it('makes an existing user an admin after logging in', function () {
    $this->alpha->run(fn () => User::create([
        'name' => 'Eigenaar', 'email' => 'eigenaar@example.com', 'minecraft_username' => 'Notch',
        'password' => Hash::make('geheim123'), 'token' => Str::random(32),
    ]));

    openClaim($this->tenantUrl('alpha', claimPath($this->alpha)))->assertRedirect($this->tenantUrl('alpha', '/login'));
    $this->post($this->tenantUrl('alpha', '/login'), ['email' => 'eigenaar@example.com', 'password' => 'geheim123'])->assertRedirect();

    expect(isAdmin($this->alpha, 'eigenaar@example.com'))->toBeTrue();
});

it('can be used only once', function () {
    $path = claimPath($this->alpha);

    openClaim($this->tenantUrl('alpha', $path));
    registerOn('alpha.mtportal.test');
    expect(isAdmin($this->alpha, 'eigenaar@example.com'))->toBeTrue();

    // A second visitor with the same link gets nothing.
    $this->unencryptedCookies = [];
    app('auth')->forgetGuards();
    $this->get($this->tenantUrl('alpha', $path))->assertStatus(410)->assertSee('Deze link werkt niet meer');

    $this->alpha->run(fn () => User::create([
        'name' => 'Vreemde', 'email' => 'vreemde@example.com', 'minecraft_username' => 'Vreemde',
        'password' => Hash::make('geheim123'), 'token' => Str::random(32),
    ]));
    $this->actingAs($this->alpha->run(fn () => User::where('email', 'vreemde@example.com')->first()));
    $this->get($this->tenantUrl('alpha', $path))->assertStatus(410);

    expect(isAdmin($this->alpha, 'vreemde@example.com'))->toBeFalse();
});

it('does not survive a guest who opened it after it was used', function () {
    $path = claimPath($this->alpha);
    $token = Str::afterLast($path, '/');

    openClaim($this->tenantUrl('alpha', $path));

    // Someone else uses it first.
    $other = $this->alpha->run(fn () => User::create([
        'name' => 'Ander', 'email' => 'ander@example.com', 'minecraft_username' => 'Ander',
        'password' => Hash::make('geheim123'), 'token' => Str::random(32),
    ]));
    expect(AdminClaim::consume($this->alpha, $token, $other))->toBeTrue();

    registerOn('alpha.mtportal.test')->assertSessionHas('error');
    expect(isAdmin($this->alpha, 'eigenaar@example.com'))->toBeFalse();
});

it('expires', function () {
    $path = claimPath($this->alpha);

    $this->travel(config('tenancy.admin_claim_ttl_minutes') + 1)->minutes();

    $this->get($this->tenantUrl('alpha', $path))->assertStatus(410);
});

it('only works on its own portal', function () {
    $this->createTenant('beta');

    $this->get($this->tenantUrl('beta', claimPath($this->alpha)))->assertStatus(410);
});

it('is replaced by a newer link', function () {
    $old = claimPath($this->alpha);
    $new = claimPath($this->alpha);

    $this->get($this->tenantUrl('alpha', $old))->assertStatus(410);
    $this->get($this->tenantUrl('alpha', $new))->assertRedirect();
});
