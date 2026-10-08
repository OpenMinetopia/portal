<?php

namespace App\Http\Middleware;

use App\Support\Portal;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** The portal itself only exists on tenant domains; the central domain serves the provisioning API. */
class TenantDomainOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(Portal::single() || tenancy()->initialized, 404);

        return $next($request);
    }
}
