<?php

use App\Services\Plugin\PlotService;
use App\Services\Plugin\PluginApiService;

it('lists plots by name in natural order, not in the order the plugin sends them', function () {
    $plots = collect(['AB-023', 'ab-002', 'AB-028', 'AB-10', 'AB-001'])
        ->mapWithKeys(fn ($name) => [$name => ['permission' => 'OWNER', 'location' => ['min' => [], 'max' => []]]])
        ->all();

    $this->mock(PluginApiService::class, fn ($mock) => $mock->shouldReceive('get')->andReturn(['plots' => $plots]));

    $service = app(PlotService::class);
    $expected = ['AB-001', 'ab-002', 'AB-10', 'AB-023', 'AB-028'];

    expect(array_column($service->getAllPlots(), 'name'))->toBe($expected)
        ->and(array_column($service->getPlayerPlots('uuid'), 'name'))->toBe($expected);
});
