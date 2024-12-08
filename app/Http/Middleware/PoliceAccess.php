<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PoliceAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user->isAdmin() || $user->roles->contains('id', 3)) {
            return $next($request);
        }

        abort(403, 'Je hebt geen toegang tot deze pagina.');
    }
} 