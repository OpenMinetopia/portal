<?php

namespace App\Services\Plugin;

use Illuminate\Support\Facades\Log;

/**
 * Talks to the OpenMinetopia plugin of the current tenant. Not a singleton: the URL
 * and key are read from config, which the tenancy bootstrapper sets per tenant.
 */
class PluginApiService
{
    protected ?string $baseUrl;
    protected ?string $apiKey;

    public function __construct(protected PluginAddressGuard $guard)
    {
        $this->baseUrl = rtrim((string) config('plugin.api.url'), '/') ?: null;
        $this->apiKey = config('plugin.api.key');
    }

    /**
     * Make a GET request to the plugin API.
     */
    public function get(string $endpoint, array $query = []): mixed
    {
        return $this->makeRequest('GET', $endpoint, $query);
    }

    /**
     * Make a POST request to the plugin API.
     */
    public function post(string $endpoint, array|string $data = []): mixed
    {
        return $this->makeRequest('POST', $endpoint, $data);
    }

    private function makeRequest(string $method, string $endpoint, array|string $data = []): mixed
    {
        if (! $this->baseUrl || ! $this->apiKey) {
            return null;
        }

        $url = $this->baseUrl . $endpoint;

        try {
            $request = $this->guard->client($this->baseUrl)->withHeaders([
                'X-API-Key' => $this->apiKey,
            ]);

            $response = match ($method) {
                'GET' => $request->get($url, $data),
                'POST' => is_string($data) ? $request->withBody($data, 'text/plain')->post($url)
                                         : $request->post($url, $data),
                default => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}")
            };

            if ($response->successful()) {
                $json = $response->json();

                if (isset($json['success']) && !$json['success']) {
                    return null;
                }

                return $json;
            }
        } catch (\Exception $e) {
            Log::error('API request failed', [
                'tenant' => tenant('id'),
                'method' => $method,
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            return null;
        }

        return null;
    }
}
