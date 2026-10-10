<?php

namespace Tests\Fakes;

use App\Services\Plugin\PlotService;

/** Plots in memory, with owners and members as player UUIDs. */
class FakePlotService extends PlotService
{
    /** @var array<string, array{owners: list<string>, members: list<string>}> */
    public array $plots = [];

    public bool $reachable = true;

    public function __construct()
    {
    }

    public function addPlot(string $name, array $owners, array $members = []): void
    {
        $this->plots[$name] = ['owners' => $owners, 'members' => $members];
    }

    public function members(string $name): array
    {
        return $this->plots[$name]['members'];
    }

    public function getPlot(string $name): ?array
    {
        if (! $this->reachable || ! isset($this->plots[$name])) {
            return null;
        }

        return ['name' => $name, 'description' => null, 'priority' => 0, 'flags' => [],
            'location' => ['min' => ['x' => 0, 'y' => 60, 'z' => 0], 'max' => ['x' => 9, 'y' => 80, 'z' => 9]]] + $this->plots[$name];
    }

    public function getAllPlots(): array
    {
        return collect(array_keys($this->plots))->map(fn ($name) => $this->getPlot($name))->values()->all();
    }

    public function getPlayerPlots(string $uuid): array
    {
        return collect($this->plots)
            ->filter(fn ($plot) => in_array($uuid, [...$plot['owners'], ...$plot['members']], true))
            ->map(fn ($plot, $name) => $this->getPlot($name) + ['permission' => in_array($uuid, $plot['owners'], true) ? 'OWNER' : 'MEMBER'])
            ->values()->all();
    }

    public function addMember(string $plotName, string $playerUuid): bool
    {
        if (! $this->reachable) {
            return false;
        }

        $this->plots[$plotName]['members'][] = $playerUuid;

        return true;
    }

    public function removeMember(string $plotName, string $playerUuid): bool
    {
        if (! $this->reachable) {
            return false;
        }

        $this->plots[$plotName]['members'] = array_values(array_diff($this->plots[$plotName]['members'], [$playerUuid]));

        return true;
    }
}
