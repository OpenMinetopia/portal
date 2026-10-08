<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureMinecraftVerified;
use App\Http\Middleware\AdminAccess;
use App\Http\Middleware\ValidateApiKey;
use App\Http\Middleware\CanManagePermits;
use App\Http\Middleware\EnsurePermitsEnabled;
use App\Http\Middleware\EnsureCompaniesEnabled;
use App\Http\Middleware\CanManageCompanies;
use App\Http\Middleware\PoliceAccess;
use App\Http\Middleware\EnsureBrokerEnabled;
use App\Http\Middleware\EnsureTransactionsEnabled;
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

        $middleware->alias([
            'minecraft.verified' => EnsureMinecraftVerified::class,
            'api.key' => ValidateApiKey::class,
            'admin' => AdminAccess::class,
            'permit.manage'  => CanManagePermits::class,
            'permits.enabled' => EnsurePermitsEnabled::class,
            'companies.enabled' => EnsureCompaniesEnabled::class,
            'companies.manage' => CanManageCompanies::class,
            'police.access' => PoliceAccess::class,
            'broker.enabled' => EnsureBrokerEnabled::class,
            'transactions.enabled' => EnsureTransactionsEnabled::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
