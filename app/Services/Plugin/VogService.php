<?php

namespace App\Services\Plugin;

use App\Helpers\MinecraftHelper;
use Carbon\Carbon;

/**
 * VOGs (verklaringen omtrent gedrag) live in the plugin: it decides the outcome from the
 * strafblad and gives the officer the VOG book in-game. The portal shows them and asks
 * the plugin to issue one. Plugins from before VOGs answer neither call.
 */
class VogService
{
    /** Why the plugin refused, by the code it answers with. */
    private const ERRORS = [
        'officer_offline' => 'Je moet online zijn op de server: het VOG-boek komt in je inventory.',
        'no_permission' => 'Je mag in-game geen VOG afgeven (openminetopia.books.vog).',
        'book_disabled' => 'Het VOG-boek staat uit in books.yml van de plugin.',
        'player_not_found' => 'Deze speler is nog nooit op de server geweest.',
    ];

    public function __construct(protected PluginApiService $apiService) {}

    /**
     * The player's VOGs, newest first, or null when the plugin gave no answer.
     *
     * @return list<array{positive: bool, record_count: int, officer_name: ?string, date: Carbon, valid_until: Carbon, valid: bool}>|null
     */
    public function forPlayer(string $uuid): ?array
    {
        $data = $this->apiService->get("/api/player/{$uuid}/vogs");

        if (! is_array($data) || ! isset($data['vogs'])) {
            return null;
        }

        return array_map(fn (array $vog) => $this->present($vog), array_values($data['vogs']));
    }

    /**
     * Asks the plugin to issue a VOG by this officer.
     *
     * @return array{vog: array}|array{error: string}
     */
    public function issue(string $playerUuid, string $officerUuid): array
    {
        $data = $this->apiService->postOrError("/api/player/{$playerUuid}/vogs", ['officer' => $officerUuid]);

        if (($data['success'] ?? false) && isset($data['vog'])) {
            return ['vog' => $this->present($data['vog'])];
        }

        return ['error' => self::ERRORS[$data['code'] ?? null] ?? 'De plugin gaf geen VOG af. Is hij bereikbaar en bijgewerkt?'];
    }

    private function present(array $vog): array
    {
        $validUntil = Carbon::createFromTimestampMs($vog['valid_until']);

        return [
            'positive' => (bool) $vog['positive'],
            'record_count' => (int) ($vog['record_count'] ?? 0),
            'officer_name' => isset($vog['officer']) ? MinecraftHelper::getName($vog['officer']) : null,
            'date' => Carbon::createFromTimestampMs($vog['date']),
            'valid_until' => $validUntil,
            'valid' => $validUntil->isFuture(),
        ];
    }
}
