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
        \Log::info("Adding owner to plot", [
            'plot' => $plotName,
            'player' => $playerUuid
        ]);

        $response = $this->apiService->post("/api/plots/{$plotName}/owners/add", [
            'uuid' => $playerUuid
        ]);

        if (!($response['success'] ?? false)) {
            \Log::error("Failed to add owner to plot", [
                'plot' => $plotName,
                'player' => $playerUuid,
                'response' => $response
            ]);
        }

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
        \Log::info("Removing owner from plot", [
            'plot' => $plotName,
            'player' => $playerUuid
        ]);

        $response = $this->apiService->post("/api/plots/{$plotName}/owners/remove", [
            'uuid' => $playerUuid
        ]);

        if (!($response['success'] ?? false)) {
            \Log::error("Failed to remove owner from plot", [
                'plot' => $plotName,
                'player' => $playerUuid,
                'response' => $response
            ]);
        }

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
        try {
            // Get plot data using the new endpoint
            $response = $this->apiService->get("/api/plots/{$plotName}");

            if (!isset($response['plot']) || !$response['success']) {
                return false;
            }

            $plot = $response['plot'];

            // Remove all members first
            if (!empty($plot['members'])) {
                foreach ($plot['members'] as $memberUuid) {
                    if (!$this->removeMember($plotName, $memberUuid)) {
                        \Log::error("Failed to remove member during transfer", [
                            'plot' => $plotName,
                            'member' => $memberUuid
                        ]);
                        return false;
                    }
                }
            }

            // Remove all current owners
            if (!empty($plot['owners'])) {
                foreach ($plot['owners'] as $ownerUuid) {
                    if (!$this->removeOwner($plotName, $ownerUuid)) {
                        \Log::error("Failed to remove owner during transfer", [
                            'plot' => $plotName,
                            'owner' => $ownerUuid
                        ]);
                        return false;
                    }
                }
            }

            // Wait a bit to ensure all removals are processed
            usleep(500000); // 0.5 seconds

            // Add the new owner
            $success = $this->addOwner($plotName, $newOwnerUuid);

            if ($success) {
                \Log::info("Successfully transferred plot ownership", [
                    'plot' => $plotName,
                    'new_owner' => $newOwnerUuid
                ]);
            } else {
                \Log::error("Failed to add new owner to plot", [
                    'plot' => $plotName,
                    'new_owner' => $newOwnerUuid
                ]);
            }

            return $success;
        } catch (\Exception $e) {
            \Log::error('Plot transfer failed with exception', [
                'plot' => $plotName,
                'new_owner' => $newOwnerUuid,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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

    /**
     * Get all plots.
     *
     * @return array
     */
    public function getAllPlots(): array
    {
        $data = $this->apiService->get("/api/plots");
        
        if (!isset($data['plots']) || !is_array($data['plots'])) {
            return [];
        }

        return collect($data['plots'])->map(function ($plot, $plotName) {
            return [
                'name' => $plotName,
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

    /**
     * Get a single plot.
     *
     * @param string $name
     * @return array|null
     */
    public function getPlot(string $name): ?array
    {
        $data = $this->apiService->get("/api/plots/{$name}");
        
        if (!isset($data['plot'])) {
            return null;
        }

        $plot = $data['plot'];
        return [
            'name' => $name,
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
    }
}
