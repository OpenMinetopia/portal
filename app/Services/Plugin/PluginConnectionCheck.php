<?php

namespace App\Services\Plugin;

use App\Models\Tenant;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;

/**
 * Checks whether the portal can reach a tenant's plugin and whether the key works.
 *
 * The plugin checks X-API-Key on every path before routing, so a probe without the
 * key must get 401 (proving it is the plugin), and the same probe with the key must
 * get anything but 401. That needs no endpoint that could be slow on a big server.
 *
 * @phpstan-type Result array{reachable: bool, auth_ok: bool, latency_ms: ?int, error_code: ?string, message: ?string}
 */
class PluginConnectionCheck
{
    public const PROBE_PATH = '/api/omt-portal-check';

    public function __construct(private PluginHttp $http) {}

    /** @return Result */
    public function checkTenant(Tenant $tenant): array
    {
        return $this->check((string) $tenant->plugin_api_url, (string) $tenant->plugin_api_key);
    }

    /** @return Result */
    public function check(string $url, string $apiKey): array
    {
        $url = rtrim($url, '/');

        if ($url === '') {
            return $this->result(false, false, null, 'dns', 'No plugin URL is set.');
        }

        try {
            $client = $this->http->client($url);

            $anonymous = $client->get($url.self::PROBE_PATH);

            if ($anonymous->status() !== 401) {
                return $this->result(false, false, null, 'bad_response', $this->unexpected($anonymous));
            }

            $started = hrtime(true);
            $authenticated = $client->withHeaders(['X-API-Key' => $apiKey])->get($url.self::PROBE_PATH);
            $latency = (int) round((hrtime(true) - $started) / 1e6);
        } catch (PluginAddressException $exception) {
            return $this->result(false, false, null, $exception->errorCode, $exception->getMessage());
        } catch (ConnectionException $exception) {
            return $this->result(false, false, null, $this->connectionErrorCode($exception), $exception->getMessage());
        }

        if ($authenticated->status() === 401) {
            return $this->result(true, false, $latency, null, 'The plugin rejected the API key.');
        }

        if ($authenticated->serverError()) {
            return $this->result(false, false, $latency, 'bad_response', $this->unexpected($authenticated));
        }

        return $this->result(true, true, $latency, null, null);
    }

    private function connectionErrorCode(ConnectionException $exception): string
    {
        $message = $exception->getMessage();
        $curlError = preg_match('/cURL error (\d+)/', $message, $match) ? (int) $match[1] : null;

        return match (true) {
            $curlError === 28 || stripos($message, 'timed out') !== false => 'timeout',
            $curlError === 7 && stripos($message, 'refused') !== false => 'connection_refused',
            $curlError === 6 => 'dns',
            $curlError === 7 => 'timeout', // Unreachable host or a firewall dropping the connection.
            default => 'bad_response',
        };
    }

    private function unexpected(Response $response): string
    {
        return "Unexpected HTTP {$response->status()}; this does not look like the OpenMinetopia plugin.";
    }

    /** @return Result */
    private function result(bool $reachable, bool $authOk, ?int $latency, ?string $code, ?string $message): array
    {
        return ['reachable' => $reachable, 'auth_ok' => $authOk, 'latency_ms' => $latency, 'error_code' => $code, 'message' => $message];
    }
}
