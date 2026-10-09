<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Models\PortalFeature;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Demo mode: fake plugin and Mojang answers, never in production (see config/portal.php).
        if (config('portal.demo')) {
            $this->app->bind(\App\Services\Plugin\PluginApiService::class, \App\Demo\FakePluginApiService::class);
            $this->app->bind(\App\Services\MojangApiService::class, \App\Demo\FakeMojangApiService::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::if('feature', function ($feature) {
            return PortalFeature::isEnabled($feature);
        });

        // Per tenant and IP, so one busy portal never locks out another.
        $perTenant = fn (Request $request) => (tenant('id') ?? 'central').'|'.$request->ip();

        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by('login|'.$perTenant($request)));
        RateLimiter::for('register', fn (Request $request) => Limit::perMinute(3)->by('register|'.$perTenant($request)));
        RateLimiter::for('password-reset', fn (Request $request) => Limit::perMinute(5)->by('password-reset|'.$perTenant($request)));
        RateLimiter::for('admin-claim', fn (Request $request) => Limit::perMinute(10)->by('admin-claim|'.$perTenant($request)));
        RateLimiter::for('minecraft-verify', fn (Request $request) => Limit::perMinute(30)->by('minecraft-verify|'.$perTenant($request)));
    }
}
