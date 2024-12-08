<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MinecraftApiController extends Controller
{
    public function getPlayer($username)
    {
        try {
            // Validate username format
            if (!preg_match('/^[a-zA-Z0-9_]{3,16}$/', $username)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid username format'
                ], 400);
            }

            // Try to get from cache first
            $cacheKey = 'minecraft_player_' . strtolower($username);

            return Cache::remember($cacheKey, 300, function () use ($username) {
                try {
                    Log::info('Fetching Minecraft player data', ['username' => $username]);

                    // Get UUID from Mojang API
                    $response = Http::timeout(5)
                        ->get("https://api.mojang.com/users/profiles/minecraft/{$username}");

                    if ($response->status() === 404) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Player not found'
                        ], 404);
                    }

                    if (!$response->successful()) {
                        Log::warning('Mojang API error', [
                            'username' => $username,
                            'status' => $response->status(),
                            'body' => $response->body()
                        ]);

                        return response()->json([
                            'success' => false,
                            'message' => 'Mojang API error'
                        ], $response->status());
                    }

                    $data = $response->json();

                    return response()->json([
                        'success' => true,
                        'name' => $data['name'],
                        'uuid' => $data['id'],
                        'skin_url' => "https://crafatar.com/avatars/{$data['id']}?overlay=true&size=64"
                    ]);

                } catch (\Exception $e) {
                    Log::error('Error fetching player data', [
                        'username' => $username,
                        'error' => $e->getMessage()
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to fetch player data'
                    ], 500);
                }
            });

        } catch (\Exception $e) {
            Log::error('Unexpected error in getPlayer', [
                'username' => $username,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred'
            ], 500);
        }
    }
}
