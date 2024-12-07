<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Arrest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ArrestController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'officer_uuid' => 'required|string|size:32',
                'reason' => 'required|string',
                'duration' => 'required|integer|min:1',
            ]);

            // Find the arrested user
            $arrestedUser = User::where('minecraft_uuid', $validated['minecraft_uuid'])->first();
            if (!$arrestedUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arrested user not found',
                    'error_code' => 'user_not_found'
                ], 404);
            }

            // Find the officer
            $officer = User::where('minecraft_uuid', $validated['officer_uuid'])->first();
            if (!$officer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Officer not found',
                    'error_code' => 'officer_not_found'
                ], 404);
            }

            // Check if officer has police role
            if (!$officer->isPoliceOfficer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a police officer',
                    'error_code' => 'not_authorized'
                ], 403);
            }

            // Create the arrest record
            $arrest = Arrest::create([
                'arrested_user_id' => $arrestedUser->id,
                'officer_id' => $officer->id,
                'reason' => $validated['reason'],
                'duration' => $validated['duration'],
                'released_at' => now()->addSeconds($validated['duration']),
            ]);

            Log::info('Player arrested', [
                'arrested_user_id' => $arrestedUser->id,
                'officer_id' => $officer->id,
                'duration' => $validated['duration']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Arrest recorded successfully',
                'data' => [
                    'arrest_id' => $arrest->id,
                    'release_time' => $arrest->released_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating arrest record', [
                'error' => $e->getMessage(),
                'minecraft_uuid' => $request->minecraft_uuid ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while recording the arrest',
                'error_code' => 'server_error'
            ], 500);
        }
    }

    public function release(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'officer_uuid' => 'required|string|size:32',
            ]);

            $arrest = Arrest::find($id);
            if (!$arrest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arrest record not found',
                    'error_code' => 'arrest_not_found'
                ], 404);
            }

            $officer = User::where('minecraft_uuid', $validated['officer_uuid'])->first();
            if (!$officer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Officer not found',
                    'error_code' => 'officer_not_found'
                ], 404);
            }

            if (!$officer->isPoliceOfficer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a police officer',
                    'error_code' => 'not_authorized'
                ], 403);
            }

            $arrest->update([
                'released_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Arrest record updated successfully',
                'data' => [
                    'release_time' => $arrest->released_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error releasing arrest', [
                'error' => $e->getMessage(),
                'arrest_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while releasing the arrest',
                'error_code' => 'server_error'
            ], 500);
        }
    }
} 