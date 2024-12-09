<?php

namespace App\Services\Plugin;

use App\Services\MojangApiService;
use Illuminate\Support\Facades\Cache;

class CriminalRecordService
{
    protected PluginApiService $apiService;
    protected MojangApiService $mojangApi;

    public function __construct(PluginApiService $apiService, MojangApiService $mojangApi)
    {
        $this->apiService = $apiService;
        $this->mojangApi = $mojangApi;
    }

    /**
     * Get criminal records for a player.
     *
     * @param string $uuid
     * @return array
     */
    public function getPlayerRecords(string $uuid): array
    {
        $data = $this->apiService->get("/api/player/{$uuid}/criminalrecords", [], 5);
        $records = $data['criminalrecords'] ?? [];

        // Enhance records with officer details
        return collect($records)->map(function ($record) {
            $officerDetails = $this->mojangApi->getPlayerDataFromUuid($record['officer']);

            return array_merge($record, [
                'officer_name' => $officerDetails['name'] ?? 'Onbekend',
                'officer_uuid' => $officerDetails['uuid'] ?? null,
                'officer_skin_url' => $officerDetails['skin_url'] ?? null,
            ]);
        })->toArray();
    }
}
