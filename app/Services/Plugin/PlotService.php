<?php

namespace App\Services\Plugin;

class PlotService
{
    protected PluginApiService $apiService;

    public function __construct(PluginApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Get player plots.
     *
     * @param string $uuid
     * @return array
     */
    public function getPlayerPlots(string $uuid): array
    {
        $data = $this->apiService->get("/api/player/{$uuid}/plots");

        if (!isset($data['plots']) || !is_array($data['plots'])) {
            return [];
        }

        return collect($data['plots'])->map(function ($plot, $plotName) {
            return [
                'name' => $plotName,
                'permission' => $plot['permission'] ?? 'MEMBER',
                'owners' => $plot['owners'] ?? [],
                'members' => $plot['members'] ?? [],
                'description' => $plot['description'] ?? null,
                'location' => [
                    'min' => $plot['location']['min'] ?? [],
                    'max' => $plot['location']['max'] ?? [],
                ],
                'flags' => $plot['flags'] ?? [],
                'priority' => $plot['priority'] ?? 0,
            ];
        })->values()->toArray();
    }
}
