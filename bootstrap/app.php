<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureMinecraftVerified;
use App\Http\Middleware\AdminAccess;
use App\Http\Middleware\ValidateApiKey;
use App\Http\Middleware\PoliceAccess;

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
            'police.access' => PoliceAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

