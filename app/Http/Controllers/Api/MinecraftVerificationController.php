<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MinecraftVerificationController extends Controller
{
    public function verify(Request $request)
    {
        try {
            $validated = $request->validate([
                'token' => 'required|string|size:8',
                'minecraft_uuid' => 'required|string|size:36',
                'minecraft_username' => 'required|string|max:16',
                'api_key' => 'required|string'
            ]);

            // Verify API key
            if ($request->api_key !== config('services.minecraft.api_key')) {
                Log::warning('Invalid API key used for Minecraft verification', [
                    'ip' => $request->ip(),
                    'minecraft_uuid' => $request->minecraft_uuid
                ]);
                return response()->json(['error' => 'Invalid API key'], 401);
            }

            // Find user by verification token
            $user = User::where('minecraft_verification_token', $validated['token'])
                       ->where('minecraft_verified', false)
                       ->first();

            if (!$user) {
                return response()->json([
                    'error' => 'Invalid or expired token',
                    'message' => 'Deze code is ongeldig of verlopen. Vraag een nieuwe aan via het panel.'
                ], 404);
            }

            // Update user with Minecraft data
            $user->update([
                'minecraft_uuid' => $validated['minecraft_uuid'],
                'minecraft_username' => $validated['minecraft_username'],
                'minecraft_verified' => true,
                'minecraft_verified_at' => now(),
                'minecraft_verification_token' => null
            ]);

            // Clear any cached data for this user
            Cache::tags(['minecraft_user'])->forget($user->id);

            return response()->json([
                'success' => true,
                'message' => 'Account succesvol gekoppeld!',
                'user' => [
                    'id' => $user->id,
                    'minecraft_uuid' => $user->minecraft_uuid,
                    'minecraft_username' => $user->minecraft_username,
                    'roles' => $user->roles->pluck('name')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Minecraft verification error', [
                'error' => $e->getMessage(),
                'minecraft_uuid' => $request->minecraft_uuid ?? null
            ]);

            return response()->json([
                'error' => 'Verification failed',
                'message' => 'Er is een fout opgetreden. Probeer het later opnieuw.'
            ], 500);
        }
    }

    public function getPlayerData(string $uuid)
    {
        $user = User::where('minecraft_uuid', $uuid)
                   ->with(['roles' => function($q) {
                       $q->where('is_game_role', true);
                   }])
                   ->first();

        if (!$user) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'minecraft_uuid' => $user->minecraft_uuid,
                'minecraft_username' => $user->minecraft_username,
                'game_roles' => $user->roles->pluck('name'),
                'verified' => true
            ]
        ]);
    }
} 