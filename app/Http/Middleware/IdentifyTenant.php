<?php

namespace App\Http\Middleware;

use App\Support\Portal;
use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Symfony\Component\HttpFoundation\Response;

/**
 * Runs globally, before the session starts, so the session, auth and everything
 * after it use the tenant's database. Central domains pass through untouched.
 * Hosted mode only; a single portal has no tenants.
 */
class IdentifyTenant
{
    public function __construct(private InitializeTenancyByDomain $initialize) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (Portal::single()) {
            return $next($request);
        }

        // Long-running processes (tests, Octane) must not carry a tenant into the next request.
        if (tenancy()->initialized) {
            tenancy()->end();
        }

        if (in_array($request->getHost(), config('tenancy.central_domains'), true)) {
            return $next($request);
        }

        InitializeTenancyByDomain::$onFail ??= fn () => response()->view('errors.unknown-portal', [], 404);

        return $this->initialize->handle($request, $next);
    }
}
