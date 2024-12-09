<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CanManagePermits
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user()->isAdmin() && 
            !auth()->user()->roles->pluck('id')->intersect(
                \App\Models\PermitType::pluck('authorized_roles')->flatten()->unique()
            )->isNotEmpty()) {
            abort(403, 'Je hebt geen toegang tot deze functie.');
        }

        return $next($request);
    }
} 