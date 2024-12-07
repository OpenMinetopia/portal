<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VehicleController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'type' => 'required|string',
                'model' => 'required|string',
                'custom_data' => 'nullable|array'
            ]);

            $user = User::where('minecraft_uuid', $validated['minecraft_uuid'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error_code' => 'user_not_found'
                ], 404);
            }

            $vehicle = Vehicle::create([
                'user_id' => $user->id,
                'type' => $validated['type'],
                'model' => $validated['model'],
                'custom_data' => $validated['custom_data'] ?? null,
                'last_used' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vehicle created successfully',
                'data' => ['vehicle_id' => $vehicle->id]
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating vehicle', [
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

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'custom_data' => 'nullable|array'
            ]);

            $user = User::where('minecraft_uuid', $validated['minecraft_uuid'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error_code' => 'user_not_found'
                ], 404);
            }

            $vehicle = Vehicle::find($id);
            if (!$vehicle) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle not found',
                    'error_code' => 'vehicle_not_found'
                ], 404);
            }

            if ($vehicle->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authorized to update this vehicle',
                    'error_code' => 'not_authorized'
                ], 403);
            }

            $vehicle->update([
                'custom_data' => $validated['custom_data'] ?? $vehicle->custom_data,
                'last_used' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vehicle updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating vehicle', [
                'error' => $e->getMessage(),
                'vehicle_id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error_code' => 'server_error'
            ], 500);
        }
    }

    public function delete(Request $request, $id)
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

            $vehicle = Vehicle::find($id);
            if (!$vehicle) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehicle not found',
                    'error_code' => 'vehicle_not_found'
                ], 404);
            }

            if ($vehicle->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authorized to delete this vehicle',
                    'error_code' => 'not_authorized'
                ], 403);
            }

            $vehicle->delete();

            return response()->json([
                'success' => true,
                'message' => 'Vehicle deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting vehicle', [
                'error' => $e->getMessage(),
                'vehicle_id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error_code' => 'server_error'
            ], 500);
        }
    }
} 