<?php

namespace App\Demo;

use App\Services\MojangApiService;

/** Knows the demo players, so demo mode never calls Mojang. */
class FakeMojangApiService extends MojangApiService
{
    public function getPlayerData(string $username)
    {
        $uuid = DemoWorld::PLAYERS[$username] ?? null;

        return $uuid ? ['uuid' => DemoWorld::plain($uuid), 'name' => $username, 'skin_url' => null] : null;
    }

    public function getPlayerDataFromUuid(string $uuid)
    {
        $name = DemoWorld::nameFor($uuid);

        return $name ? ['uuid' => $uuid, 'name' => $name, 'skin_url' => null] : null;
    }
}
