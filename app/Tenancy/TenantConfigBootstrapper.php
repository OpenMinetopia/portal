<?php

namespace App\Tenancy;

use Illuminate\Contracts\Config\Repository;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

/**
 * Puts the tenant's settings where the app reads them. Unlike stancl's TenantConfig
 * feature this also sets empty values, so a tenant without a key never falls back
 * to whatever the central .env holds.
 */
class TenantConfigBootstrapper implements TenancyBootstrapper
{
    public const MAP = [
        'name' => 'app.name',
        'plugin_api_url' => 'plugin.api.url',
        'plugin_api_key' => 'plugin.api.key',
        'minecraft_api_key' => 'services.minecraft.api_key',
        'server_address' => 'plugin.server_address',
    ];

    private array $original = [];

    public function __construct(private Repository $config) {}

    public function bootstrap(Tenant $tenant)
    {
        foreach (self::MAP as $attribute => $key) {
            $this->original[$key] ??= $this->config->get($key);
            $this->config->set($key, $tenant->getAttribute($attribute));
        }

        // Absolute URLs (redirects, links) follow the host the visitor used.
        $this->original['app.url'] ??= $this->config->get('app.url');
        $this->config->set('app.url', request()->getSchemeAndHttpHost());
    }

    public function revert()
    {
        foreach ($this->original as $key => $value) {
            $this->config->set($key, $value);
        }

        $this->original = [];
    }
}
