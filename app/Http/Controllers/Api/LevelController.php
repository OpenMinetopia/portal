<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LevelProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LevelController extends Controller
{
    public function updateProgress(Request $request)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'points_from_plots' => 'integer|min:0',
                'points_from_balance' => 'integer|min:0',
                'points_from_vehicles' => 'integer|min:0',
                'points_from_prefix' => 'integer|min:0',
                'points_from_playtime' => 'integer|min:0',
                'points_from_fitness' => 'integer|min:0'
            ]);

            $user = User::where('minecraft_uuid', $validated['minecraft_uuid'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error_code' => 'user_not_found'
                ], 404);
            }

            $levelProgress = $user->levelProgress()->updateOrCreate(
                ['user_id' => $user->id],
                array_merge($validated, ['last_calculated' => now()])
            );

            // Update the user's calculated level
            $user->updateCalculatedLevel();

            return response()->json([
                'success' => true,
                'message' => 'Level progress updated',
                'data' => [
                    'current_level' => $user->calculated_level,
                    'total_points' => $levelProgress->getTotalPoints()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating level progress', [
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

    public function calculateLevel(Request $request)
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

            $user->updateCalculatedLevel();

            return response()->json([
                'success' => true,
                'message' => 'Level calculated successfully',
                'data' => [
                    'current_level' => $user->calculated_level
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error calculating level', [
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