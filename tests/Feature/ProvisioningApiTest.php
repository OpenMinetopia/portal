<?php

use App\Models\Domain;
use App\Models\Tenant;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

/**
 * Signs exactly like the website's App\Services\Hosting\PortalClient::sign().
 */
function websiteSign(string $secret, string $timestamp, string $nonce, string $method, string $path, string $body): string
{
    return hash_hmac('sha256', implode("\n", [$timestamp, $nonce, strtoupper($method), $path, $body]), $secret);
}

function provision(string $method, string $path, ?array $body = null, array $override = []): TestResponse
{
    $raw = $body === null ? '' : json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $fullPath = '/internal/v1'.$path;
    $timestamp = (string) ($override['timestamp'] ?? now()->getTimestamp());
    $nonce = $override['nonce'] ?? (string) Str::uuid();
    $signature = $override['signature'] ?? websiteSign($override['secret'] ?? 'test-secret', $timestamp, $nonce, $method, $fullPath, $raw);

    return test()->call($method, 'http://'.($override['host'] ?? Tests\TestCase::CENTRAL_DOMAIN).$fullPath, [], [], [], [
        'HTTP_X-OMT-Timestamp' => $timestamp,
        'HTTP_X-OMT-Nonce' => $nonce,
        'HTTP_X-OMT-Signature' => $signature,
        'HTTP_ACCEPT' => 'application/json',
        'CONTENT_TYPE' => 'application/json',
    ], $override['raw'] ?? $raw);
}

function desiredState(string $key, array $override = []): array
{
    return array_replace_recursive([
        'name' => ucfirst($key).' Portaal',
        'status' => 'active',
        'domains' => ["{$key}.mtportal.test"],
        'primary_domain' => "{$key}.mtportal.test",
        'database' => ['name' => Tests\TestCase::tenantDatabase($key), 'username' => env('DB_USERNAME'), 'password' => env('DB_PASSWORD')],
        'plugin' => ['url' => 'http://93.184.216.34:25570', 'api_key' => "plt_{$key}"],
        'minecraft_api_key' => "ist_{$key}",
        'server_address' => "play.{$key}.nl",
    ], $override);
}

describe('signature', function () {
    beforeEach(fn () => $this->createTenant('alpha'));

    it('accepts a request signed like the website does', function () {
        provision('GET', '/tenants/'.self::TENANTS['alpha'])->assertOk()->assertJson(['id' => self::TENANTS['alpha']]);
    });

    it('rejects a wrong signature', function () {
        provision('GET', '/tenants/'.self::TENANTS['alpha'], override: ['secret' => 'not-the-secret'])
            ->assertStatus(401)->assertJson(['message' => 'Invalid signature.']);
    });

    it('rejects a body that was changed after signing', function () {
        $body = desiredState('alpha');
        $raw = json_encode(['name' => 'Gekaapt'] + $body);

        provision('PUT', '/tenants/'.self::TENANTS['alpha'], $body, ['raw' => $raw])->assertStatus(401);
        expect(Tenant::find(self::TENANTS['alpha'])->name)->toBe('Alpha Portaal');
    });

    it('rejects a stale timestamp', function () {
        provision('GET', '/tenants/'.self::TENANTS['alpha'], override: ['timestamp' => now()->subSeconds(301)->getTimestamp()])
            ->assertStatus(401)->assertJson(['message' => 'Stale timestamp.']);

        provision('GET', '/tenants/'.self::TENANTS['alpha'], override: ['timestamp' => now()->addSeconds(301)->getTimestamp()])
            ->assertStatus(401);

        provision('GET', '/tenants/'.self::TENANTS['alpha'], override: ['timestamp' => now()->subSeconds(290)->getTimestamp()])
            ->assertOk();
    });

    it('rejects a replayed nonce', function () {
        $nonce = (string) Str::uuid();
        $timestamp = now()->getTimestamp();

        provision('GET', '/tenants/'.self::TENANTS['alpha'], override: compact('nonce', 'timestamp'))->assertOk();
        provision('GET', '/tenants/'.self::TENANTS['alpha'], override: compact('nonce', 'timestamp'))
            ->assertStatus(401)->assertJson(['message' => 'Replayed nonce.']);
    });

    it('rejects missing headers', function () {
        $this->getJson('http://'.self::CENTRAL_DOMAIN.'/internal/v1/tenants/'.self::TENANTS['alpha'])->assertStatus(401);
    });

    it('is not reachable on a tenant domain', function () {
        provision('GET', '/tenants/'.self::TENANTS['alpha'], override: ['host' => 'alpha.mtportal.test'])->assertNotFound();
    });
});

describe('upsert', function () {
    it('creates a tenant on a fresh database and runs all migrations', function () {
        $database = Tests\TestCase::tenantDatabase('gamma');
        Tests\TestCase::server()->exec("DROP DATABASE IF EXISTS `{$database}`; CREATE DATABASE `{$database}`");
        $id = (string) Str::uuid();

        try {
            provision('PUT', "/tenants/{$id}", desiredState('gamma'))
                ->assertOk()
                ->assertJson([
                    'id' => $id,
                    'name' => 'Gamma Portaal',
                    'status' => 'active',
                    'domains' => ['gamma.mtportal.test'],
                    'primary_domain' => 'gamma.mtportal.test',
                    'plugin' => ['url' => 'http://93.184.216.34:25570', 'has_api_key' => true],
                ])
                ->assertJsonCount(24, 'migrations.ran')
                ->assertJsonMissingPath('plugin.api_key');

            $tenant = Tenant::find($id);
            expect($tenant->plugin_api_key)->toBe('plt_gamma')
                ->and($tenant->getRawOriginal('plugin_api_key'))->not->toBe('plt_gamma')
                ->and($tenant->getRawOriginal('tenancy_db_password'))->not->toBe(env('DB_PASSWORD'))
                ->and($tenant->run(fn () => \App\Models\Role::where('slug', 'admin')->exists()))->toBeTrue();

            $this->get('http://gamma.mtportal.test/login')->assertOk()->assertSee('Gamma Portaal');
        } finally {
            Tests\TestCase::server()->exec("DROP DATABASE IF EXISTS `{$database}`");
        }
    });

    it('is idempotent and syncs domains to the desired state', function () {
        $id = self::TENANTS['alpha'];
        $state = desiredState('alpha', ['domains' => ['alpha.mtportal.test', 'portal.alpha.nl'], 'primary_domain' => 'portal.alpha.nl']);

        provision('PUT', "/tenants/{$id}", $state)->assertOk()->assertJsonPath('migrations.ran', []);
        provision('PUT', "/tenants/{$id}", $state)->assertOk()->assertJsonPath('primary_domain', 'portal.alpha.nl');

        expect(Tenant::count())->toBe(1)
            ->and(Domain::pluck('domain')->sort()->values()->all())->toBe(['alpha.mtportal.test', 'portal.alpha.nl']);

        provision('PUT', "/tenants/{$id}", desiredState('alpha', ['name' => 'Nieuwe naam']))
            ->assertOk()
            ->assertJson(['name' => 'Nieuwe naam', 'domains' => ['alpha.mtportal.test'], 'primary_domain' => 'alpha.mtportal.test']);

        $this->get('http://portal.alpha.nl/login')->assertNotFound()->assertSee('Hier staat geen portaal');
        $this->get('http://alpha.mtportal.test/login')->assertOk()->assertSee('Nieuwe naam');
    });

    it('refuses a domain that belongs to another tenant', function () {
        $this->createTenant('beta');

        provision('PUT', '/tenants/'.self::TENANTS['alpha'], desiredState('alpha', ['domains' => ['beta.mtportal.test'], 'primary_domain' => 'beta.mtportal.test']))
            ->assertStatus(409)->assertJson(['domain' => 'beta.mtportal.test']);

        expect(Tenant::find(self::TENANTS['alpha']))->toBeNull();
    });

    it('validates the desired state', function () {
        provision('PUT', '/tenants/'.self::TENANTS['alpha'], desiredState('alpha', ['status' => 'paused', 'primary_domain' => 'elders.nl', 'domains' => [self::CENTRAL_DOMAIN]]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status', 'primary_domain', 'domains.0']);
    });

    it('reports failed migrations', function () {
        provision('PUT', '/tenants/'.self::TENANTS['alpha'], desiredState('alpha', ['database' => ['name' => 'omt_portal_test_does_not_exist']]))
            ->assertStatus(500)
            ->assertJson(['error' => 'migration_failed', 'tenant' => ['id' => self::TENANTS['alpha']]]);
    });
});

it('pauses and resumes a tenant', function () {
    $this->createTenant('alpha');

    provision('PATCH', '/tenants/'.self::TENANTS['alpha'].'/status', ['status' => 'inactive'])->assertOk()->assertJson(['status' => 'inactive']);
    $this->get($this->tenantUrl('alpha', '/login'))->assertStatus(503);

    provision('PATCH', '/tenants/'.self::TENANTS['alpha'].'/status', ['status' => 'active'])->assertOk();
    $this->get($this->tenantUrl('alpha', '/login'))->assertOk();

    provision('PATCH', '/tenants/'.self::TENANTS['alpha'].'/status', ['status' => 'weg'])->assertStatus(422);
});

it('reports health', function () {
    $this->createTenant('alpha');

    provision('GET', '/tenants/'.self::TENANTS['alpha'])
        ->assertOk()
        ->assertJson(['status' => 'active', 'health' => ['database' => true, 'pending_migrations' => 0, 'users' => 0]]);

    provision('GET', '/tenants/'.Str::uuid())->assertNotFound();
});

it('deletes a tenant without dropping its database', function () {
    $this->createTenant('alpha');

    provision('DELETE', '/tenants/'.self::TENANTS['alpha'])->assertNoContent();

    expect(Tenant::count())->toBe(0)->and(Domain::count())->toBe(0);
    $this->get($this->tenantUrl('alpha', '/login'))->assertNotFound();

    $exists = Tests\TestCase::server()->query("SHOW DATABASES LIKE '".Tests\TestCase::tenantDatabase('alpha')."'")->fetchColumn();
    expect($exists)->toBe(Tests\TestCase::tenantDatabase('alpha'));

    provision('DELETE', '/tenants/'.self::TENANTS['alpha'])->assertNotFound();
});

describe('plugin check', function () {
    beforeEach(fn () => $this->createTenant('alpha'));

    $check = fn () => provision('POST', '/tenants/'.Tests\TestCase::TENANTS['alpha'].'/plugin-check');

    it('reports a working plugin', function () use ($check) {
        Http::fakeSequence()->push('Unauthorized request', 401)->push('Resource not found', 404);

        $check()->assertOk()->assertJson(['reachable' => true, 'auth_ok' => true, 'error_code' => null])
            ->assertJsonStructure(['latency_ms', 'message']);

        Http::assertSent(fn (HttpRequest $request) => $request->hasHeader('X-API-Key', 'plt_alpha'));
    });

    it('reports a wrong key', function () use ($check) {
        Http::fakeSequence()->push('Unauthorized request', 401)->push('Unauthorized request', 401);

        $check()->assertOk()->assertJson(['reachable' => true, 'auth_ok' => false]);
    });

    it('reports something that is not the plugin', function () use ($check) {
        Http::fake(['*' => Http::response('<html>nginx</html>', 200)]);

        $check()->assertOk()->assertJson(['reachable' => false, 'auth_ok' => false, 'error_code' => 'bad_response']);
    });

    it('maps connection errors to the codes the website explains', function (string $error, string $code) use ($check) {
        Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException($error));

        $check()->assertOk()->assertJson(['reachable' => false, 'auth_ok' => false, 'error_code' => $code]);
    })->with([
        ['cURL error 28: Connection timed out after 5001 milliseconds', 'timeout'],
        ['cURL error 7: Failed to connect to 93.184.216.34 port 25570: Connection refused', 'connection_refused'],
        ['cURL error 6: Could not resolve host: mc.example.nl', 'dns'],
    ]);

    it('blocks internal addresses without connecting', function (string $url) use ($check) {
        Http::fake();
        Tenant::find(self::TENANTS['alpha'])->update(['plugin_api_url' => $url]);

        $check()->assertOk()->assertJson(['reachable' => false, 'error_code' => 'blocked_address']);
        Http::assertNothingSent();
    })->with([
        'http://127.0.0.1:25570',
        'http://10.0.0.5:25570',
        'http://169.254.169.254:8080',
        'http://[::1]:25570',
        'http://93.184.216.34:80',
    ]);
});

describe('admin claim url', function () {
    it('hands out a one-time url on the primary domain', function () {
        $this->createTenant('alpha');

        $url = provision('POST', '/tenants/'.self::TENANTS['alpha'].'/admin-claim')->assertOk()->json('url');

        expect($url)->toStartWith('http://alpha.mtportal.test/beheerder-worden/')
            ->and(Tenant::find(self::TENANTS['alpha'])->admin_claim_token_hash)->toBe(hash('sha256', Str::afterLast($url, '/')));
    });
});
