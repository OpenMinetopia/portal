<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CanManageCompanies
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->hasPermission('manage-companies')) {
            abort(403, 'Je hebt geen toegang tot deze functie.');
        }

        return $next($request);
    }
}
