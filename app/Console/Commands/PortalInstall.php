<?php

namespace App\Console\Commands;

use App\Services\Tenancy\AdminClaim;
use App\Support\EnvFile;
use App\Support\Portal;
use Illuminate\Console\Command;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

/**
 * Sets up a self-hosted portal: asks a few questions, writes .env, generates the
 * keys, builds the database and prints what to put in the plugin's config.yml.
 * Safe to run again: existing values are offered as defaults and keys are kept.
 */
class PortalInstall extends Command
{
    protected $signature = 'portal:install
        {--name= : Naam van het portaal}
        {--url= : Adres van het portaal, bijvoorbeeld https://portaal.jouwserver.nl}
        {--server-address= : Serveradres voor spelers, bijvoorbeeld play.jouwserver.nl}
        {--plugin-host= : Waar de plugin draait (IP of hostnaam)}
        {--plugin-port= : Poort van de plugin-API (rest-api → port)}
        {--db-connection= : mysql, mariadb of sqlite}
        {--db-host=}
        {--db-port=}
        {--db-database=}
        {--db-username=}
        {--db-password=}
        {--new-keys : Maak nieuwe api-keys, ook als er al keys zijn}
        {--no-admin-link : Geen beheerderslink maken}
        {--environment=production : APP_ENV}';

    protected $description = 'Richt een eigen portaal in: .env, sleutels, database en de config.yml voor de plugin';

    public function handle(): int
    {
        if (Portal::hosted()) {
            $this->error('portal:install is voor een eigen portaal (PORTAL_MODE=single).');

            return self::FAILURE;
        }

        $env = new EnvFile($this->laravel->environmentFilePath());

        // An empty .env happens in Docker, where the file is mounted from the host.
        if (! $env->exists() || filesize($this->laravel->environmentFilePath()) === 0) {
            copy(base_path('.env.example'), $this->laravel->environmentFilePath());
        }

        $current = $env->read();
        $existing = fn (string $key, ?string $fallback = null) => ($current[$key] ?? '') !== '' ? $current[$key] : $fallback;

        $this->line('<options=bold>OpenMinetopia Portal installeren</>');
        $this->line('Druk op Enter om de voorgestelde waarde te houden.');
        $this->newLine();

        $name = $this->option('name') ?? text(
            label: 'Hoe heet je portaal?',
            placeholder: 'Griepje Portaal',
            default: in_array($existing('APP_NAME'), [null, 'Laravel', 'OpenMinetopia'], true) ? '' : $existing('APP_NAME'),
            required: 'Vul een naam in.',
            hint: 'Spelers zien deze naam bovenaan het portaal.',
        );

        $url = rtrim($this->option('url') ?? text(
            label: 'Op welk adres open je het portaal?',
            placeholder: 'https://portaal.jouwserver.nl',
            default: in_array($existing('APP_URL'), [null, 'http://localhost', 'https://central.mtportal.nl'], true) ? '' : $existing('APP_URL'),
            required: 'Vul het adres in.',
            validate: fn (string $value) => self::validUrl($value) ? null : 'Begin met http:// of https://, bijvoorbeeld https://portaal.jouwserver.nl.',
            hint: 'Met https:// als je een domein met certificaat hebt, anders http:// en het IP-adres.',
        ), '/');

        if (! self::validUrl($url)) {
            $this->error('Het adres moet beginnen met http:// of https://.');

            return self::FAILURE;
        }

        $serverAddress = $this->option('server-address') ?? text(
            label: 'Welk adres vullen spelers in Minecraft in?',
            placeholder: 'play.jouwserver.nl',
            default: (string) $existing('MC_SERVER_ADDRESS', ''),
            hint: 'Wordt in het portaal getoond. Mag leeg blijven.',
        );

        $currentPlugin = parse_url((string) $existing('PLUGIN_API_URL', ''));

        $pluginHost = $this->option('plugin-host') ?? text(
            label: 'Op welk adres draait de OpenMinetopia-plugin?',
            default: $currentPlugin['host'] ?? '127.0.0.1',
            required: 'Vul een IP-adres of hostnaam in.',
            hint: '127.0.0.1 als de Minecraft-server op deze machine draait, anders het IP-adres van die server.',
        );

        $pluginPort = (int) ($this->option('plugin-port') ?? text(
            label: 'Op welke poort luistert de plugin-API?',
            default: (string) ($currentPlugin['port'] ?? 4567),
            validate: fn (string $value) => self::validPort($value) ? null : 'Vul een poort tussen 1 en 65535 in.',
            hint: 'rest-api → port in de config.yml van de plugin.',
        ));

        if (! self::validPort((string) $pluginPort)) {
            $this->error('De poort van de plugin moet tussen 1 en 65535 liggen.');

            return self::FAILURE;
        }

        $database = $this->databaseSettings($existing);

        $keepKeys = ! $this->option('new-keys') && $existing('PLUGIN_API_KEY') && $existing('MINECRAFT_API_KEY')
            && ($this->option('no-interaction') || confirm('Er staan al api-keys in .env. Wil je die houden?', default: true,
                hint: 'Nieuwe keys betekent dat je ook de config.yml van de plugin moet aanpassen.'));

        $values = [
            'APP_NAME' => $name,
            'APP_ENV' => $this->option('environment'),
            'APP_DEBUG' => $this->option('environment') !== 'production',
            'APP_URL' => $url,
            'APP_KEY' => $existing('APP_KEY') ?? 'base64:'.base64_encode(Encrypter::generateKey(config('app.cipher'))),
            'PORTAL_MODE' => 'single',
            'MC_SERVER_ADDRESS' => $serverAddress,
            'PLUGIN_API_URL' => 'http://'.self::hostForUrl($pluginHost).':'.$pluginPort,
            'PLUGIN_API_KEY' => $keepKeys ? $existing('PLUGIN_API_KEY') : 'plt_'.bin2hex(random_bytes(32)),
            'MINECRAFT_API_KEY' => $keepKeys ? $existing('MINECRAFT_API_KEY') : 'ist_'.bin2hex(random_bytes(32)),
            'SESSION_DRIVER' => 'database',
            'CACHE_STORE' => 'file',
            'QUEUE_CONNECTION' => 'sync',
        ] + $database;

        $env->set($values);
        $this->applyToRunningConfig($values);
        $this->info('.env bijgewerkt.');

        if ($this->call('migrate', ['--force' => true]) !== self::SUCCESS) {
            $this->error('De database kon niet worden bijgewerkt. Controleer de databasegegevens in .env en draai dit commando opnieuw.');

            return self::FAILURE;
        }

        if (! file_exists(public_path('storage'))) {
            $this->callSilently('storage:link');
        }

        // The web server reads the cached config; make it pick up the new .env.
        if ($this->laravel->configurationIsCached()) {
            $this->callSilently('config:cache');
        }

        $this->printPluginConfig($values, $pluginHost, $pluginPort);

        if (! $this->option('no-admin-link')) {
            $this->newLine();
            $this->line('<options=bold>Beheerder worden</>');
            $this->call('portal:admin-link');
        }

        if (! $this->option('no-interaction')) {
            $this->line('Test daarna de verbinding met de plugin: <options=bold>php artisan portal:check</>');
        }

        return self::SUCCESS;
    }

    /** @return array<string, string> */
    private function databaseSettings(callable $existing): array
    {
        $connection = $this->option('db-connection') ?? ($this->option('no-interaction')
            ? $existing('DB_CONNECTION', 'mysql')
            : select(
                label: 'Welke database gebruik je?',
                options: ['mysql' => 'MySQL', 'mariadb' => 'MariaDB', 'sqlite' => 'SQLite (één bestand, geen databaseserver nodig)'],
                default: $existing('DB_CONNECTION', 'mysql'),
            ));

        if ($connection === 'sqlite') {
            $path = $this->option('db-database') ?? database_path('database.sqlite');
            if (! file_exists($path)) {
                touch($path);
            }

            return ['DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => $path];
        }

        $ask = fn (string $option, string $key, string $label, string $default) => $this->option($option)
            ?? ($this->option('no-interaction') ? $existing($key, $default) : text(label: $label, default: (string) $existing($key, $default), required: true));

        $settings = [
            'DB_CONNECTION' => $connection,
            'DB_HOST' => $ask('db-host', 'DB_HOST', 'Databaseserver', '127.0.0.1'),
            'DB_PORT' => $ask('db-port', 'DB_PORT', 'Poort van de database', '3306'),
            'DB_DATABASE' => $ask('db-database', 'DB_DATABASE', 'Naam van de database', 'openminetopia_portal'),
            'DB_USERNAME' => $ask('db-username', 'DB_USERNAME', 'Gebruikersnaam van de database', 'openminetopia'),
        ];

        $password = $this->option('db-password');

        if ($password === null && ! $this->option('no-interaction')) {
            $password = password(label: 'Wachtwoord van de database', hint: $existing('DB_PASSWORD') ? 'Laat leeg om het huidige wachtwoord te houden.' : '');
        }

        $settings['DB_PASSWORD'] = ($password === null || $password === '') ? (string) $existing('DB_PASSWORD', '') : $password;

        return $settings;
    }

    private function applyToRunningConfig(array $values): void
    {
        $connection = $values['DB_CONNECTION'];

        config([
            'app.name' => $values['APP_NAME'],
            'app.url' => $values['APP_URL'],
            'database.default' => $connection,
            "database.connections.{$connection}.database" => $values['DB_DATABASE'],
        ]);

        foreach (['host' => 'DB_HOST', 'port' => 'DB_PORT', 'username' => 'DB_USERNAME', 'password' => 'DB_PASSWORD'] as $key => $env) {
            if (array_key_exists($env, $values)) {
                config(["database.connections.{$connection}.{$key}" => $values[$env]]);
            }
        }

        DB::purge($connection);
        url()->forceRootUrl($values['APP_URL']);
    }

    private function printPluginConfig(array $values, string $pluginHost, int $pluginPort): void
    {
        $scheme = parse_url($values['APP_URL'], PHP_URL_SCHEME);
        $host = parse_url($values['APP_URL'], PHP_URL_HOST).(($port = parse_url($values['APP_URL'], PHP_URL_PORT)) ? ':'.$port : '');

        // The plugin puts https:// in front of portal.url itself (http:// only for .test).
        $portalUrl = $scheme === 'https' ? $host : $values['APP_URL'];

        // On the same machine the plugin only has to listen locally; otherwise the portal
        // must be able to reach it over the network.
        $bind = in_array($pluginHost, ['127.0.0.1', 'localhost', '::1'], true) ? '127.0.0.1' : '0.0.0.0';

        $this->newLine();
        $this->line('<options=bold>Zet dit in plugins/OpenMinetopia/config.yml en herstart de Minecraft-server:</>');
        $this->newLine();
        foreach ([
            'rest-api:',
            '  enabled: true',
            "  host: {$bind}",
            "  port: {$pluginPort}",
            "  api-key: {$values['PLUGIN_API_KEY']}",
            'portal:',
            '  enabled: true',
            "  url: {$portalUrl}",
            "  token: {$values['MINECRAFT_API_KEY']}",
        ] as $line) {
            $this->line('    '.$line);
        }

        if ($scheme !== 'https') {
            $this->newLine();
            $this->warn('Je portaal draait zonder HTTPS. De plugin koppelt accounts (/link) nu alleen via https://, dus koppelen werkt pas met HTTPS of met een plugin-versie die http:// in portal.url ondersteunt.');
        }
    }

    private static function validUrl(string $value): bool
    {
        return (bool) preg_match('#^https?://[^\s/]+#i', $value) && filter_var($value, FILTER_VALIDATE_URL);
    }

    private static function validPort(string $value): bool
    {
        return ctype_digit($value) && (int) $value >= 1 && (int) $value <= 65535;
    }

    private static function hostForUrl(string $host): string
    {
        return filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? "[{$host}]" : $host;
    }
}
