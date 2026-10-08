<?php

namespace App\Demo;

use App\Services\Plugin\PluginApiService;

/**
 * Answers like the OpenMinetopia plugin would, from DemoWorld, without a Minecraft
 * server. Bound instead of the real service in demo mode only. Writes succeed and
 * change nothing.
 */
class FakePluginApiService extends PluginApiService
{
    public function __construct() {}

    public function get(string $endpoint, array $query = []): mixed
    {
        $path = trim($endpoint, '/');

        return match (true) {
            $path === 'api/plots' => ['success' => true, 'plots' => collect(DemoWorld::PLOTS)->map(fn ($plot) => $this->plot($plot))->all()],
            (bool) preg_match('#^api/plots/([^/]+)$#', $path, $m) => isset(DemoWorld::PLOTS[$m[1]]) ? ['success' => true, 'plot' => $this->plot(DemoWorld::PLOTS[$m[1]])] : null,
            (bool) preg_match('#^api/player/([^/]+)/plots$#', $path, $m) => ['success' => true, 'plots' => $this->playerPlots(DemoWorld::nameFor($m[1]))],
            (bool) preg_match('#^api/player/([^/]+)/bankaccounts$#', $path, $m) => ['success' => true, 'accounts' => $this->playerAccounts(DemoWorld::nameFor($m[1]))],
            (bool) preg_match('#^api/player/([^/]+)/criminalrecords$#', $path, $m) => ['success' => true, 'criminalrecords' => $this->records(DemoWorld::nameFor($m[1]))],
            (bool) preg_match('#^api/player/([^/]+)/colors$#', $path, $m) => ['success' => true, 'colors' => []],
            (bool) preg_match('#^api/player/([^/]+)/prefixes$#', $path, $m) => ['success' => true, 'prefixes' => []],
            (bool) preg_match('#^api/player/([^/]+)$#', $path, $m) => $this->player(DemoWorld::nameFor($m[1])),
            (bool) preg_match('#^api/bankaccount/([^/]+)/users$#', $path, $m) => $this->accountUsers($m[1]),
            (bool) preg_match('#^api/bankaccount/([^/]+)$#', $path, $m) => isset(DemoWorld::ACCOUNTS[$m[1]]) ? $this->account(DemoWorld::ACCOUNTS[$m[1]]) + ['success' => true, 'uuid' => $m[1]] : null,
            default => null,
        };
    }

    public function post(string $endpoint, array|string $data = []): mixed
    {
        return ['success' => true];
    }

    private function plot(array $plot): array
    {
        return [
            'owners' => array_map(fn ($name) => DemoWorld::PLAYERS[$name], $plot['owners']),
            'members' => array_map(fn ($name) => DemoWorld::PLAYERS[$name], $plot['members']),
            'description' => $plot['description'],
            'location' => [
                'min' => array_combine(['x', 'y', 'z'], $plot['min']),
                'max' => array_combine(['x', 'y', 'z'], $plot['max']),
            ],
            'flags' => [],
            'priority' => 0,
        ];
    }

    private function playerPlots(?string $name): array
    {
        return collect(DemoWorld::PLOTS)
            ->filter(fn ($plot) => in_array($name, [...$plot['owners'], ...$plot['members']], true))
            ->map(fn ($plot) => $this->plot($plot) + ['permission' => in_array($name, $plot['owners'], true) ? 'OWNER' : 'MEMBER'])
            ->all();
    }

    private function account(array $account): array
    {
        return ['name' => $account['name'], 'type' => $account['type'], 'balance' => $account['balance'], 'frozen' => $account['frozen']];
    }

    private function playerAccounts(?string $name): array
    {
        return collect(DemoWorld::ACCOUNTS)
            ->filter(fn ($account) => $account['owner'] === $name || ($account['type'] === 'GOVERNMENT' && $name === 'Agent_Sanne'))
            ->map(fn ($account) => $this->account($account))
            ->all();
    }

    private function accountUsers(string $uuid): ?array
    {
        $account = DemoWorld::ACCOUNTS[$uuid] ?? null;

        return $account ? ['success' => true, 'users' => [DemoWorld::PLAYERS[$account['owner']] => ['permission' => 'ADMIN']]] : null;
    }

    private function player(?string $name): ?array
    {
        if (! $name) {
            return null;
        }

        $stats = DemoWorld::PLAYER_STATS[$name];

        return [
            'success' => true,
            'level' => $stats['level'],
            'calculated_level' => $stats['level'],
            'fitness' => $stats['fitness'],
            'active_prefix' => $stats['prefix'],
            'default_prefix' => 'Zwerver',
            'playtime_seconds' => $stats['playtime'],
            'active_name_color' => '<white>',
            'active_chat_color' => '<gray>',
            'active_prefix_color' => '<gold>',
            'active_level_color' => '<green>',
            'name_color' => '<white>',
            'chat_color' => '<gray>',
            'prefix_color' => '<gold>',
            'level_color' => '<green>',
        ];
    }

    private function records(?string $name): array
    {
        if ($name !== 'Daan') {
            return [];
        }

        return [
            ['id' => 1, 'reason' => 'Door rood licht gereden op de Markt', 'officer' => DemoWorld::PLAYERS['Agent_Sanne'], 'date' => now()->subDays(12)->getTimestampMs()],
            ['id' => 2, 'reason' => 'Zonder vergunning gevist in de haven', 'officer' => DemoWorld::PLAYERS['Agent_Sanne'], 'date' => now()->subDays(3)->getTimestampMs()],
        ];
    }
}
