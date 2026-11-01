<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureFeatureEnabled;
use App\Http\Middleware\EnsureMinecraftVerified;
use App\Http\Middleware\ValidateApiKey;
use App\Http\Middleware\EnsureTenantActive;
use App\Http\Middleware\IdentifyTenant;
use App\Http\Middleware\TenantDomainOnly;
use App\Http\Middleware\VerifyProvisioningSignature;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            if (config('portal.mode') !== 'hosted') {
                return;
            }

            // The provisioning API answers on the central domains only.
            foreach (config('tenancy.central_domains') as $domain) {
                Route::domain($domain)
                    ->prefix('internal/v1')
                    ->middleware([VerifyProvisioningSignature::class])
                    ->group(base_path('routes/internal.php'));
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Before StartSession, so sessions and auth use the tenant's database. These
        // check the portal mode per request (config is not loaded yet at this point)
        // and do nothing in single mode.
        $middleware->append([IdentifyTenant::class, EnsureTenantActive::class]);
        $middleware->web(prepend: TenantDomainOnly::class);
        $middleware->api(prepend: TenantDomainOnly::class);

        // Cloudflare's edge ranges (cloudflare.com/ips), so request()->ip() is the visitor.
        $middleware->trustProxies(at: [
            '173.245.48.0/20', '103.21.244.0/22', '103.22.200.0/22', '103.31.4.0/22',
            '141.101.64.0/18', '108.162.192.0/18', '190.93.240.0/20', '188.114.96.0/20',
            '197.234.240.0/22', '198.41.128.0/17', '162.158.0.0/15', '104.16.0.0/13',
            '104.24.0.0/14', '172.64.0.0/13', '131.0.72.0/22',
            '2400:cb00::/32', '2606:4700::/32', '2803:f800::/32', '2405:b500::/32',
            '2405:8100::/32', '2a06:98c0::/29', '2c0f:f248::/32',
        ]);

        $middleware->alias([
            'minecraft.verified' => EnsureMinecraftVerified::class,
            'api.key' => ValidateApiKey::class,
            'feature' => EnsureFeatureEnabled::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
