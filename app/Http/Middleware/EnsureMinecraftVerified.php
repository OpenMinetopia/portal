<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMinecraftVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()->minecraft_verified) {
            return redirect()->route('minecraft.verify');
        }

        return $next($request);
    }
} 