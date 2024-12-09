<?php

namespace App\Helpers;

use App\Services\MojangApiService;
use Illuminate\Support\Facades\Cache;

class MinecraftHelper
{
    /**
     * Get player data from UUID
     *
     * @param string $uuid
     * @return array|null
     */
    public static function getPlayer(string $uuid): ?array
    {
        return app(MojangApiService::class)->getPlayerDataFromUuid($uuid);
    }

    /**
     * Get player name from UUID
     *
     * @param string $uuid
     * @return string
     */
    public static function getName(string $uuid): string
    {
        $player = self::getPlayer($uuid);
        return $player['name'] ?? 'Unknown';
    }

    /**
     * Get player avatar URL
     *
     * @param string $uuid
     * @return string
     */
    public static function getAvatar(string $uuid): string
    {
        return "https://crafatar.com/avatars/{$uuid}?overlay=true";
    }

    /**
     * Get player head URL
     *
     * @param string $uuid
     * @return string
     */
    public static function getHead(string $uuid): string
    {
        return "https://crafatar.com/renders/head/{$uuid}?overlay=true";
    }
} 