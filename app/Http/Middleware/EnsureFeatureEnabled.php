<?php

namespace App\Http\Middleware;

use App\Support\Features;
use Closure;
use Illuminate\Http\Request;

/** feature:broker — the page does not exist while the feature is off. */
class EnsureFeatureEnabled
{
    public function handle(Request $request, Closure $next, string ...$features)
    {
        foreach ($features as $feature) {
            abort_unless(Features::enabled($feature), 404);
        }

        return $next($request);
    }
}
