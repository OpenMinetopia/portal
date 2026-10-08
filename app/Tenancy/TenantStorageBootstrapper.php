<?php

namespace App\Tenancy;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

/**
 * Gives every tenant its own public files and file cache, and makes the session
 * and auth pick up the tenant's database. The rate limiter keeps the central cache;
 * its keys carry the tenant id.
 *
 * Public files live in storage/app/public/tenants/{id} and are served through the
 * normal public/storage link, with relative URLs so every domain of the tenant works.
 */
class TenantStorageBootstrapper implements TenancyBootstrapper
{
    private array $original = [];

    public function __construct(private Application $app) {}

    public static function publicPath(string $tenantId): string
    {
        return storage_path('app/public/tenants/'.$tenantId);
    }

    public static function cachePath(string $tenantId): string
    {
        return storage_path('framework/cache/data/tenants/'.$tenantId);
    }

    public function bootstrap(Tenant $tenant)
    {
        $config = $this->app['config'];
        $id = $tenant->getTenantKey();

        $this->original = [
            'filesystems.disks.public.root' => $config->get('filesystems.disks.public.root'),
            'filesystems.disks.public.url' => $config->get('filesystems.disks.public.url'),
            'cache.stores.file.path' => $config->get('cache.stores.file.path'),
        ];

        $config->set('filesystems.disks.public.root', self::publicPath($id));
        $config->set('filesystems.disks.public.url', '/storage/tenants/'.$id);
        $config->set('cache.stores.file.path', self::cachePath($id));

        $this->forget();
    }

    public function revert()
    {
        foreach ($this->original as $key => $value) {
            $this->app['config']->set($key, $value);
        }

        $this->original = [];
        $this->forget();
    }

    private function forget(): void
    {
        Storage::forgetDisk('public');
        $this->app['cache']->forgetDriver('file');
        $this->app->forgetInstance('cache.store');

        // Session and auth hold on to the connection they were made with.
        $this->app->forgetInstance('session.store');
        $this->app['session']->forgetDrivers();
        $this->app['auth']->forgetGuards();
    }
}
