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
            'active_prefix' => $data['active_prefix'] ?? 'N/A',
            'active_name_color' => $data['active_name_color'] ?? 'N/A',
            'playtime_seconds' => $data['playtimeSeconds'] ?? 'N/A',
            'active_chat_color' => $data['active_chat_color'] ?? 'N/A',
            'active_prefix_color' => $data['active_prefix_color'] ?? 'N/A',
            'active_level_color' => $data['active_level_color'] ?? 'N/A',
            'calculated_level' => $data['calculated_level'] ?? 'N/A',
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
