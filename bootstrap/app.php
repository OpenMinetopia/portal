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

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
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
