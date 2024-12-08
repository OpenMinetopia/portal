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
    public function get(string $endpoint, array $query = [], int $cacheMinutes = null)
    {
        $url = $this->baseUrl . $endpoint;

        if ($cacheMinutes) {
            return Cache::remember($url . '?' . http_build_query($query), $cacheMinutes, function () use ($url, $query) {
                return $this->makeRequest('GET', $url, $query);
            });
        }

        return $this->makeRequest('GET', $url, $query);
    }

    /**
     * Perform an HTTP request.
     *
     * @param string $method
     * @param string $url
     * @param array $data
     * @return mixed
     */
    private function makeRequest(string $method, string $url, array $data = [])
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->{$method}($url, $data);

            if ($response->successful()) {
                $json = $response->json();

                // Return fallback if success is false
                if (isset($json['success']) && !$json['success']) {
                    return null;
                }

                return $json;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }
}
