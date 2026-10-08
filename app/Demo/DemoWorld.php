<?php

namespace App\Demo;

/**
 * The made-up server behind demo mode: players, bank accounts, plots and a
 * criminal record, shaped like the plugin's REST API answers.
 */
class DemoWorld
{
    public const PLAYERS = [
        'Burgemeester' => '4f7c2a1e-8b3d-4c6a-9e2f-1a5b7c9d0e11',
        'Lotte' => '7a1b3c5d-2e4f-4a6b-8c0d-9e1f3a5b7c22',
        'Daan' => '9c8b7a6f-5e4d-4c3b-a2a1-0f9e8d7c6b33',
        'Agent_Sanne' => '2b4d6f8a-1c3e-4a5b-9d7f-0e2c4a6b8d44',
    ];

    public const ACCOUNTS = [
        'a1f0c9e2-0000-4000-8000-000000000001' => ['name' => 'Privérekening', 'type' => 'PRIVATE', 'balance' => 18450.75, 'frozen' => false, 'owner' => 'Burgemeester'],
        'a1f0c9e2-0000-4000-8000-000000000002' => ['name' => 'Gemeente Westerdam', 'type' => 'GOVERNMENT', 'balance' => 1250000.00, 'frozen' => false, 'owner' => 'Burgemeester'],
        'a1f0c9e2-0000-4000-8000-000000000003' => ['name' => 'Privérekening', 'type' => 'PRIVATE', 'balance' => 3275.40, 'frozen' => false, 'owner' => 'Lotte'],
        'a1f0c9e2-0000-4000-8000-000000000004' => ['name' => 'Bakkerij Lotte', 'type' => 'BUSINESS', 'balance' => 42980.00, 'frozen' => false, 'owner' => 'Lotte'],
        'a1f0c9e2-0000-4000-8000-000000000005' => ['name' => 'Privérekening', 'type' => 'PRIVATE', 'balance' => 512.10, 'frozen' => true, 'owner' => 'Daan'],
        'a1f0c9e2-0000-4000-8000-000000000006' => ['name' => 'Privérekening', 'type' => 'PRIVATE', 'balance' => 7820.00, 'frozen' => false, 'owner' => 'Agent_Sanne'],
    ];

    public const PLOTS = [
        'westerdam_stadhuis' => ['owners' => ['Burgemeester'], 'members' => ['Agent_Sanne'], 'description' => 'Stadhuis van Westerdam', 'min' => [120, 60, -340], 'max' => [168, 110, -290]],
        'westerdam_bakkerij' => ['owners' => ['Lotte'], 'members' => ['Daan'], 'description' => 'Bakkerij aan de Markt', 'min' => [201, 64, -312], 'max' => [219, 90, -296]],
        'westerdam_kade_12' => ['owners' => ['Burgemeester'], 'members' => [], 'description' => 'Grachtenpand aan de Kade', 'min' => [240, 64, -280], 'max' => [252, 95, -268]],
        'westerdam_politie' => ['owners' => ['Agent_Sanne'], 'members' => ['Burgemeester'], 'description' => 'Politiebureau', 'min' => [90, 64, -250], 'max' => [130, 92, -210]],
        'noordhaven_loods_3' => ['owners' => ['Daan'], 'members' => [], 'description' => 'Loods in de haven', 'min' => [-410, 63, 520], 'max' => [-380, 80, 548]],
    ];

    public const PLAYER_STATS = [
        'Burgemeester' => ['level' => 74, 'fitness' => 186, 'prefix' => 'Burgemeester', 'playtime' => 1_245_600],
        'Lotte' => ['level' => 41, 'fitness' => 132, 'prefix' => 'Bakker', 'playtime' => 512_400],
        'Daan' => ['level' => 18, 'fitness' => 97, 'prefix' => 'Havenarbeider', 'playtime' => 158_200],
        'Agent_Sanne' => ['level' => 55, 'fitness' => 171, 'prefix' => 'Agent', 'playtime' => 804_300],
    ];

    public static function plain(string $uuid): string
    {
        return str_replace('-', '', $uuid);
    }

    public static function nameFor(string $uuid): ?string
    {
        $uuid = strtolower($uuid);

        foreach (self::PLAYERS as $name => $playerUuid) {
            if ($uuid === $playerUuid || $uuid === self::plain($playerUuid)) {
                return $name;
            }
        }

        return null;
    }
}
