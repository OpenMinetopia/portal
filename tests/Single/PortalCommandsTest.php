<?php

use App\Console\Commands\PortalCheck;
use App\Support\EnvFile;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->envDir = sys_get_temp_dir().'/omt-install-'.uniqid();
    mkdir($this->envDir);
    $this->app->useEnvironmentPath($this->envDir);
});

afterEach(function () {
    @unlink($this->envDir.'/.env');
    @rmdir($this->envDir);
});

function installFlags(array $override = []): array
{
    return $override + [
        '--no-interaction' => true,
        '--name' => 'Griepje Portaal',
        '--url' => 'https://portaal.griepje.nl',
        '--server-address' => 'play.griepje.nl',
        '--plugin-host' => '127.0.0.1',
        '--plugin-port' => '4567',
        '--db-connection' => 'mysql',
        '--db-host' => env('DB_HOST'),
        '--db-port' => env('DB_PORT'),
        '--db-database' => Tests\SingleTestCase::DATABASE,
        '--db-username' => env('DB_USERNAME'),
        '--db-password' => 'geheim wachtwoord',
        '--environment' => 'production',
    ];
}

it('installs without questions and prints the plugin config', function () {
    config(['database.connections.mysql.password' => env('DB_PASSWORD')]);

    $this->artisan('portal:install', installFlags(['--db-password' => (string) env('DB_PASSWORD')]))
        ->expectsOutputToContain('rest-api:')
        ->expectsOutputToContain('  host: 127.0.0.1')
        ->expectsOutputToContain('  port: 4567')
        ->expectsOutputToContain('  url: portaal.griepje.nl')
        ->expectsOutputToContain('https://portaal.griepje.nl/beheerder-worden/')
        ->assertSuccessful();

    $env = (new EnvFile($this->envDir.'/.env'))->read();

    expect($env['APP_NAME'])->toBe('Griepje Portaal')
        ->and($env['APP_URL'])->toBe('https://portaal.griepje.nl')
        ->and($env['APP_KEY'])->toStartWith('base64:')
        ->and($env['APP_ENV'])->toBe('production')
        ->and($env['APP_DEBUG'])->toBe('false')
        ->and($env['PORTAL_MODE'])->toBe('single')
        ->and($env['PLUGIN_API_URL'])->toBe('http://127.0.0.1:4567')
        ->and($env['PLUGIN_API_KEY'])->toMatch('/^plt_[0-9a-f]{64}$/')
        ->and($env['MINECRAFT_API_KEY'])->toMatch('/^ist_[0-9a-f]{64}$/')
        ->and($env['MC_SERVER_ADDRESS'])->toBe('play.griepje.nl')
        ->and($env['DB_DATABASE'])->toBe(Tests\SingleTestCase::DATABASE);
});

it('keeps keys and the app key when run again', function () {
    $this->artisan('portal:install', installFlags(['--db-password' => (string) env('DB_PASSWORD')]))->assertSuccessful();
    $first = (new EnvFile($this->envDir.'/.env'))->read();

    $this->artisan('portal:install', installFlags(['--db-password' => (string) env('DB_PASSWORD'), '--name' => 'Andere naam']))->assertSuccessful();
    $second = (new EnvFile($this->envDir.'/.env'))->read();

    expect($second['APP_NAME'])->toBe('Andere naam')
        ->and($second['APP_KEY'])->toBe($first['APP_KEY'])
        ->and($second['PLUGIN_API_KEY'])->toBe($first['PLUGIN_API_KEY'])
        ->and($second['MINECRAFT_API_KEY'])->toBe($first['MINECRAFT_API_KEY']);
});

it('quotes values with spaces and keeps other lines', function () {
    $file = new EnvFile($this->envDir.'/.env');
    file_put_contents($this->envDir.'/.env', "# comment\nAPP_NAME=Oud\nOTHER=blijft\n");

    $file->set(['APP_NAME' => 'Griepje "MC" Portaal', 'DB_PASSWORD' => 'a b$c', 'NEW' => 'x']);

    expect(file_get_contents($this->envDir.'/.env'))->toContain("# comment\n")->toContain("OTHER=blijft\n")
        ->and($file->read())->toMatchArray(['APP_NAME' => 'Griepje "MC" Portaal', 'DB_PASSWORD' => 'a b$c', 'NEW' => 'x', 'OTHER' => 'blijft']);
});

it('binds the plugin to the network and warns without https', function () {
    $this->artisan('portal:install', installFlags([
        '--db-password' => (string) env('DB_PASSWORD'),
        '--url' => 'http://203.0.113.5:8000',
        '--plugin-host' => '203.0.113.20',
        '--plugin-port' => '25570',
    ]))
        ->expectsOutputToContain('  host: 0.0.0.0')
        ->expectsOutputToContain('  url: http://203.0.113.5:8000')
        ->expectsOutputToContain('zonder HTTPS')
        ->assertSuccessful();
});

it('refuses an address without http', function () {
    $this->artisan('portal:install', installFlags(['--url' => 'portaal.griepje.nl']))->assertFailed();
});

it('reports a working plugin', function () {
    Http::fakeSequence()->push('Unauthorized request', 401)->push('Resource not found', 404);

    $this->artisan('portal:check')->expectsOutputToContain('Het portaal bereikt de plugin.')->assertSuccessful();
});

it('explains what is wrong, like the website does', function (callable $fake, string $message) {
    $fake();

    $this->artisan('portal:check')->expectsOutputToContain($message)->assertFailed();
})->with([
    'wrong key' => [fn () => Http::fakeSequence()->push('', 401)->push('', 401), 'de api-key klopt niet'],
    'timeout' => [fn () => Http::fake(fn () => throw new ConnectionException('cURL error 28: Operation timed out')), 'We krijgen geen antwoord van 127.0.0.1:4567'],
    'refused' => [fn () => Http::fake(fn () => throw new ConnectionException('cURL error 7: Failed to connect: Connection refused')), '127.0.0.1:4567 weigert de verbinding'],
    'not the plugin' => [fn () => Http::fake(['*' => Http::response('hallo', 200)]), 'Het portaal bereikt de plugin op 127.0.0.1:4567 niet.'],
]);

it('words the messages exactly like the website', function () {
    expect(PortalCheck::message('http://mc.example.nl:25570', ['reachable' => false, 'auth_ok' => false, 'error_code' => 'dns']))
        ->toBe('We kunnen mc.example.nl niet vinden. Controleer het adres, of gebruik het IP-adres van je server.');
});
