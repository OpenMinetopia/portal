<?php

namespace App\Services\Plugin;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PluginApiService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('plugin.api.url');
        $this->apiKey = config('plugin.api.key');
    }

    /**
     * Make a GET request to the plugin API.
     *
     * @param string $endpoint
     * @param array $query
     * @param int|null $cacheMinutes
     * @return mixed
     */
    public function get(string $endpoint, array $query = []): mixed
    {
        $url = $this->baseUrl . $endpoint;
        return $this->makeRequest('GET', $url, $query);
    }

    /**
     * Make a POST request to the plugin API.
     *
     * @param string $endpoint
     * @param array|string $data
     * @return mixed
     */
    public function post(string $endpoint, array|string $data = []): mixed
    {
        $url = $this->baseUrl . $endpoint;
        return $this->makeRequest('POST', $url, $data);
    }

    /**
     * Perform an HTTP request.
     *
     * @param string $method
     * @param string $url
     * @param array|string $data
     * @return mixed
     */
    private function makeRequest(string $method, string $url, array|string $data = []): mixed
    {
        try {
            $request = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->timeout(30);

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
            \Log::error('API request failed', [
                'method' => $method,
                'url' => $url,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            return null;
        }

        return null;
    }
}
