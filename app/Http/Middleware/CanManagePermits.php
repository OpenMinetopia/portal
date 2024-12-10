<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CanManagePermits
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('manage-permits')) {
            abort(403, 'Je hebt geen toegang tot deze functie.');
        }

        return $next($request);
    }
}
