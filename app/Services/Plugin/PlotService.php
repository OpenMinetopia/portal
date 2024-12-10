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
     * Add owner to plot.
     *
     * @param string $plotName
     * @param string $playerUuid
     * @return bool
     */
    public function addOwner(string $plotName, string $playerUuid): bool
    {
        $response = $this->apiService->post("/api/plots/{$plotName}/owners/add", [
            'uuid' => $playerUuid
        ]);

        return $response['success'] ?? false;
    }

    /**
     * Remove owner from plot.
     *
     * @param string $plotName
     * @param string $playerUuid
     * @return bool
     */
    public function removeOwner(string $plotName, string $playerUuid): bool
    {
        $response = $this->apiService->post("/api/plots/{$plotName}/owners/remove", [
            'uuid' => $playerUuid
        ]);

        return $response['success'] ?? false;
    }

    /**
     * Add member to plot.
     *
     * @param string $plotName
     * @param string $playerUuid
     * @return bool
     */
    public function addMember(string $plotName, string $playerUuid): bool
    {
        $response = $this->apiService->post("/api/plots/{$plotName}/members/add", [
            'uuid' => $playerUuid
        ]);

        return $response['success'] ?? false;
    }

    /**
     * Remove member from plot.
     *
     * @param string $plotName
     * @param string $playerUuid
     * @return bool
     */
    public function removeMember(string $plotName, string $playerUuid): bool
    {
        $response = $this->apiService->post("/api/plots/{$plotName}/members/remove", [
            'uuid' => $playerUuid
        ]);

        return $response['success'] ?? false;
    }

    /**
     * Transfer plot ownership.
     *
     * @param string $plotName
     * @param string $newOwnerUuid
     * @return bool
     */
    public function transferOwnership(string $plotName, string $newOwnerUuid): bool
    {
        // Get current plot data
        $plots = $this->getPlayerPlots($newOwnerUuid);
        $plot = collect($plots)->firstWhere('name', $plotName);
        
        if (!$plot) {
            return false;
        }

        try {
            // Remove all members
            foreach ($plot['members'] as $memberUuid) {
                $this->removeMember($plotName, $memberUuid);
            }

            // Remove all owners except the last one
            foreach ($plot['owners'] as $ownerUuid) {
                $this->removeOwner($plotName, $ownerUuid);
            }

            // Add new owner
            return $this->addOwner($plotName, $newOwnerUuid);
        } catch (\Exception $e) {
            return false;
        }
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
