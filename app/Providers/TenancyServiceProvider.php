<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Events;
use Stancl\Tenancy\Listeners;
use Stancl\Tenancy\Middleware;

class TenancyServiceProvider extends ServiceProvider
{
    /**
     * No database creation or deletion pipelines: tenant databases are created and
     * dropped through Shipways by the website. Migrations run on provisioning and
     * through tenants:migrate-safe.
     */
    public function events(): array
    {
        return [
            Events\TenancyInitialized::class => [
                Listeners\BootstrapTenancy::class,
            ],
            Events\TenancyEnded::class => [
                Listeners\RevertToCentralContext::class,
            ],
        ];
    }

    public function boot(): void
    {
        foreach ($this->events() as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }

        $this->app[\Illuminate\Contracts\Http\Kernel::class]->prependToMiddlewarePriority(Middleware\InitializeTenancyByDomain::class);
    }
}
