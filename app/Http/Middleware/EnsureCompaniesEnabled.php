<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PortalFeature;

class EnsureCompaniesEnabled
{
    public function handle(Request $request, Closure $next)
    {
        if (!PortalFeature::where('key', 'companies')->where('is_enabled', true)->exists()) {
            abort(404);
        }

        return $next($request);
    }
} 