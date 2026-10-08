<?php

use App\Services\Plugin\PluginAddressException;
use App\Services\Plugin\PluginAddressGuard;
use App\Services\Plugin\PluginApiService;
use Illuminate\Support\Facades\Http;

function guardWith(array $dns = []): PluginAddressGuard
{
    return new PluginAddressGuard(fn (string $host) => $dns[$host] ?? []);
}

function blockedCode(PluginAddressGuard $guard, string $url): ?string
{
    try {
        $guard->resolve($url);

        return null;
    } catch (PluginAddressException $exception) {
        return $exception->errorCode;
    }
}

it('blocks private, loopback, link-local and metadata addresses', function (string $url) {
    expect(blockedCode(guardWith(), $url))->toBe('blocked_address');
})->with([
    'http://127.0.0.1:25570', 'http://127.1.2.3:25570', 'http://10.1.2.3:25570', 'http://172.16.0.1:25570',
    'http://192.168.1.10:25570', 'http://169.254.169.254:25570', 'http://100.64.0.1:25570', 'http://0.0.0.0:25570',
    'http://[::1]:25570', 'http://[fd00::1]:25570', 'http://[fe80::1]:25570', 'http://[::ffff:127.0.0.1]:25570',
]);

it('allows public addresses on ports 1024 to 65535', function (string $url) {
    expect(blockedCode(guardWith(), $url))->toBeNull();
})->with(['http://93.184.216.34:25570', 'http://93.184.216.34:1024', 'https://93.184.216.34:65535', 'http://[2606:2800:220:1::248]:4567']);

it('only allows ports 1024 to 65535', function (string $url) {
    expect(blockedCode(guardWith(), $url))->toBe('blocked_address');
})->with(['http://93.184.216.34', 'http://93.184.216.34:22', 'http://93.184.216.34:1023', 'https://93.184.216.34']);

it('checks every address a hostname resolves to', function () {
    expect(blockedCode(guardWith(['mc.example.nl' => ['93.184.216.34']]), 'http://mc.example.nl:25570'))->toBeNull()
        ->and(blockedCode(guardWith(['mc.example.nl' => ['93.184.216.34', '10.0.0.1']]), 'http://mc.example.nl:25570'))->toBe('blocked_address')
        ->and(blockedCode(guardWith(['metadata.example.nl' => ['169.254.169.254']]), 'http://metadata.example.nl:25570'))->toBe('blocked_address')
        ->and(blockedCode(guardWith(), 'http://nowhere.example.nl:25570'))->toBe('dns')
        ->and(blockedCode(guardWith(), 'ftp://93.184.216.34:25570'))->toBe('dns')
        ->and(blockedCode(guardWith(), 'http://user:pass@93.184.216.34:25570'))->toBe('dns');
});

it('pins the request to the checked address with a 5 second timeout', function () {
    $options = guardWith(['mc.example.nl' => ['93.184.216.34']])->client('http://mc.example.nl:25570')->getOptions();

    expect($options['curl'][CURLOPT_RESOLVE])->toBe(['mc.example.nl:25570:93.184.216.34'])
        ->and($options['timeout'])->toBe(5)
        ->and($options['connect_timeout'])->toBe(5)
        ->and($options['allow_redirects'])->toBeFalse();
});

it('never lets the portal call an internal plugin url', function () {
    Http::fake();
    config(['plugin.api.url' => 'http://169.254.169.254:8080', 'plugin.api.key' => 'plt_x']);

    expect(app(PluginApiService::class)->get('/api/plots'))->toBeNull();
    Http::assertNothingSent();
});

it('calls a public plugin url with the tenant key', function () {
    Http::fake(['*' => Http::response(['success' => true, 'plots' => []])]);
    config(['plugin.api.url' => 'http://93.184.216.34:25570', 'plugin.api.key' => 'plt_x']);

    expect(app(PluginApiService::class)->get('/api/plots'))->toBe(['success' => true, 'plots' => []]);
    Http::assertSent(fn ($request) => $request->url() === 'http://93.184.216.34:25570/api/plots' && $request->hasHeader('X-API-Key', 'plt_x'));
});
