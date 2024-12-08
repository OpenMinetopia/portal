<?php

namespace App\Services\Plugin;

class PlayerService
{
    protected PluginApiService $apiService;

    public function __construct(PluginApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Get player data with fallbacks.
     *
     * @param string $playerName
     * @return array
     */
    public function getPlayerData(string $playerName): array
    {
        $data = $this->apiService->get("/api/player/{$playerName}", [], 10);

        return [
            'level' => $data['level'] ?? 'N/A',
            'fitness' => $data['fitness'] ?? 'N/A',
            'prefix' => $data['prefix'] ?? 'N/A',
        ];
    }

    /**
     * Get player prefixes with fallback.
     *
     * @param string $playerName
     * @return array
     */
    public function getPlayerPrefixes(string $playerName): array
    {
        $data = $this->apiService->get("/api/player/{$playerName}/prefixes", [], 10);

        return $data['prefixes'] ?? [];
    }
}
