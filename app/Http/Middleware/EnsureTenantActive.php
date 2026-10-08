<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** A paused portal shows a friendly page; its API answers 423 so the plugin can tell. */
class EnsureTenantActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! tenancy()->initialized || tenant()->isActive()) {
            return $next($request);
        }

        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Dit portaal is gepauzeerd.',
                'error_code' => 'portal_paused',
            ], 423);
        }

        return response()->view('errors.paused', [], 503)->header('Retry-After', 3600);
    }
}
