<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalkieTalkie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WalkieTalkieController extends Controller
{
    public function setChannel(Request $request)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'channel' => 'required|string'
            ]);

            $user = User::where('minecraft_uuid', $validated['minecraft_uuid'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error_code' => 'user_not_found'
                ], 404);
            }

            $walkieTalkie = WalkieTalkie::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'channel' => $validated['channel'],
                    'last_used' => now()
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Channel set successfully',
                'data' => [
                    'channel' => $walkieTalkie->channel
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error setting walkie-talkie channel', [
                'error' => $e->getMessage(),
                'minecraft_uuid' => $request->minecraft_uuid ?? null
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error_code' => 'server_error'
            ], 500);
        }
    }

    public function useEmergency(Request $request)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32'
            ]);

            $user = User::where('minecraft_uuid', $validated['minecraft_uuid'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error_code' => 'user_not_found'
                ], 404);
            }

            $walkieTalkie = $user->walkieTalkie;
            if (!$walkieTalkie) {
                return response()->json([
                    'success' => false,
                    'message' => 'User does not have a walkie-talkie',
                    'error_code' => 'no_walkie_talkie'
                ], 404);
            }

            if ($walkieTalkie->isOnEmergencyCooldown()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Emergency function is on cooldown',
                    'error_code' => 'emergency_cooldown'
                ], 400);
            }

            $walkieTalkie->update([
                'last_used' => now(),
                'emergency_cooldown_until' => now()->addMinutes(5)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Emergency signal sent successfully',
                'data' => [
                    'cooldown_until' => $walkieTalkie->emergency_cooldown_until
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error using walkie-talkie emergency', [
                'error' => $e->getMessage(),
                'minecraft_uuid' => $request->minecraft_uuid ?? null
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error_code' => 'server_error'
            ], 500);
        }
    }
} 