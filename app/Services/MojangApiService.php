<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class MojangApiService
{
    public function getPlayerData(string $username)
    {
        try {
            // First, get UUID from username using Mojang's API
            $response = Http::get("https://api.mojang.com/users/profiles/minecraft/{$username}");

            if (!$response->successful()) {
                return null;
            }

            $playerData = $response->json();

            return [
                'uuid' => $playerData['id'], // Store UUID without dashes
                'name' => $playerData['name'],
                'skin_url' => $this->getSkinUrl($playerData['id'])
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get player name from UUID
     *
     * @param string $uuid
     * @return array|null
     */
    public function getPlayerDataFromUuid(string $uuid)
    {
        try {
            // Cache the result for 1 hour to prevent excessive API calls
            return Cache::remember('mojang_player_' . $uuid, 3600, function () use ($uuid) {
                // Get the profile data from Mojang API
                $response = Http::get("https://api.mojang.com/user/profile/{$uuid}");

                if (!$response->successful()) {
                    return null;
                }

                $profileData = $response->json();

                if (!is_array($profileData) || !isset($profileData['name'])) {
                    return null;
                }

                return [
                    'uuid' => $uuid,
                    'name' => $profileData['name'],
                    'skin_url' => $this->getSkinUrl($uuid)
                ];
            });
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function getSkinUrl(string $uuid)
    {
        try {
            return "https://crafatar.com/avatars/{$uuid}?overlay=true";
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Convert dashed UUID to undashed format
     */
    public static function stripUuidDashes(string $uuid): string
    {
        return str_replace('-', '', $uuid);
    }

    /**
     * Add dashes to UUID for display purposes only
     */
    public static function formatUuid(string $uuid): string
    {
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($uuid, 0, 8),
            substr($uuid, 8, 4),
            substr($uuid, 12, 4),
            substr($uuid, 16, 4),
            substr($uuid, 20)
        );
    }
}
