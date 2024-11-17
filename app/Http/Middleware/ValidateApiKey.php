<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key');
        
        if (!$apiKey || $apiKey !== config('services.minecraft.api_key')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key.',
                'error_code' => 'invalid_api_key'
            ], 401);
        }

        return $next($request);
    }
} 