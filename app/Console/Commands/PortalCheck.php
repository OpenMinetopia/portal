<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\Plugin\PluginConnectionCheck;
use App\Support\Portal;
use Illuminate\Console\Command;

class PortalCheck extends Command
{
    protected $signature = 'portal:check {--tenant= : Alleen op het gehoste platform: het tenant-id}';

    protected $description = 'Test of het portaal de OpenMinetopia-plugin bereikt en de api-key klopt';

    public function handle(PluginConnectionCheck $check): int
    {
        if (Portal::hosted()) {
            $tenant = Tenant::find($this->option('tenant'));

            if (! $tenant) {
                $this->error('Geef een bestaand tenant-id op met --tenant=.');

                return self::FAILURE;
            }

            [$url, $key] = [(string) $tenant->plugin_api_url, (string) $tenant->plugin_api_key];
        } else {
            [$url, $key] = [(string) config('plugin.api.url'), (string) config('plugin.api.key')];
        }

        if ($url === '' || $key === '') {
            $this->error('PLUGIN_API_URL en PLUGIN_API_KEY zijn niet ingevuld. Draai eerst php artisan portal:install.');

            return self::FAILURE;
        }

        $this->line("Plugin testen op {$url} …");
        $result = $check->check($url, $key);
        $ok = $result['reachable'] && $result['auth_ok'];

        $ok ? $this->info(self::message($url, $result)) : $this->error(self::message($url, $result));

        return $ok ? self::SUCCESS : self::FAILURE;
    }

    /**
     * The same advice the OMT website gives for the hosted check
     * (InstanceController::pluginCheckMessage there).
     */
    public static function message(string $url, array $result): string
    {
        $host = (string) parse_url($url, PHP_URL_HOST);
        $port = parse_url($url, PHP_URL_PORT) ?? (str_starts_with($url, 'https') ? 443 : 80);
        $where = $host.':'.$port;

        if ($result['reachable'] && $result['auth_ok']) {
            return 'Verbonden'.(isset($result['latency_ms']) ? " in {$result['latency_ms']} ms" : '').'. Het portaal bereikt de plugin.';
        }

        if ($result['reachable']) {
            return 'De plugin antwoordt, maar de api-key klopt niet. Kopieer rest-api → api-key opnieuw naar de config.yml en herstart de server.';
        }

        return match ($result['error_code'] ?? null) {
            'timeout' => "We krijgen geen antwoord van {$where}. Staat de server aan, staat bij rest-api enabled: true en host: 0.0.0.0, en is poort {$port} open bij je host of in je firewall?",
            'connection_refused' => "{$where} weigert de verbinding. Draait de plugin, en is {$port} dezelfde poort als rest-api → port in de config.yml?",
            'dns' => "We kunnen {$host} niet vinden. Controleer het adres, of gebruik het IP-adres van je server.",
            'blocked_address' => "{$host} is een intern adres. Vul het openbare IP-adres van je server in.",
            default => "Het portaal bereikt de plugin op {$where} niet.".(isset($result['message']) ? ' ('.$result['message'].')' : ''),
        };
    }
}
