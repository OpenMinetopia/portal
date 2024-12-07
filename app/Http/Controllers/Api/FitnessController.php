<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Fitness;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FitnessController extends Controller
{
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'total_fitness' => 'required|integer|min:0',
                'max_fitness' => 'required|integer|min:0',
                'current_status' => 'nullable|string',
                'statistics' => 'required|array',
                'statistics.health' => 'required|integer|min:0|max:100',
                'statistics.stamina' => 'nullable|integer|min:0|max:100',
                'statistics.strength' => 'nullable|integer|min:0|max:100'
            ]);

            $user = User::where('minecraft_uuid', $validated['minecraft_uuid'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error_code' => 'user_not_found'
                ], 404);
            }

            $fitness = Fitness::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'total_fitness' => $validated['total_fitness'],
                    'max_fitness' => $validated['max_fitness'],
                    'current_status' => $validated['current_status'],
                    'statistics' => $validated['statistics']
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Fitness updated successfully',
                'data' => [
                    'total_fitness' => $fitness->total_fitness,
                    'max_fitness' => $fitness->max_fitness,
                    'percentage' => $user->getFitnessPercentage(),
                    'statistics' => $fitness->statistics
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating fitness', [
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

    public function get(Request $request)
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

            $fitness = $user->fitness;

            return response()->json([
                'success' => true,
                'data' => [
                    'total_fitness' => $user->getCurrentFitness(),
                    'max_fitness' => $user->getMaxFitness(),
                    'percentage' => $user->getFitnessPercentage(),
                    'current_status' => $fitness?->current_status,
                    'statistics' => $fitness?->statistics ?? [
                        'health' => 100,
                        'stamina' => 100,
                        'strength' => 100
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting fitness', [
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