<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SecurityItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecurityController extends Controller
{
    public function useItem(Request $request)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'target_uuid' => 'required|string|size:32',
                'type' => 'required|string',
                'effects' => 'required|array',
                'duration' => 'required|integer|min:1'
            ]);

            $user = User::where('minecraft_uuid', $validated['minecraft_uuid'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error_code' => 'user_not_found'
                ], 404);
            }

            $target = User::where('minecraft_uuid', $validated['target_uuid'])->first();
            if (!$target) {
                return response()->json([
                    'success' => false,
                    'message' => 'Target user not found',
                    'error_code' => 'target_not_found'
                ], 404);
            }

            $securityItem = SecurityItem::create([
                'user_id' => $target->id,
                'type' => $validated['type'],
                'effects' => $validated['effects'],
                'last_used' => now(),
                'cooldown_until' => now()->addSeconds($validated['duration'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Security item effect applied',
                'data' => [
                    'effect_id' => $securityItem->id,
                    'expires_at' => $securityItem->cooldown_until
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error applying security item', [
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

    public function removeEffect(Request $request)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'effect_id' => 'required|integer'
            ]);

            $user = User::where('minecraft_uuid', $validated['minecraft_uuid'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error_code' => 'user_not_found'
                ], 404);
            }

            $securityItem = SecurityItem::find($validated['effect_id']);
            if (!$securityItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Effect not found',
                    'error_code' => 'effect_not_found'
                ], 404);
            }

            $securityItem->update([
                'cooldown_until' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Security item effect removed'
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing security effect', [
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