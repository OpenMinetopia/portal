<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DetectionGate;
use App\Models\DetectionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DetectionGateController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'location' => 'required|array',
                'flagged_materials' => 'required|array',
                'is_active' => 'boolean'
            ]);

            $user = User::where('minecraft_uuid', $validated['minecraft_uuid'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error_code' => 'user_not_found'
                ], 404);
            }

            if (!$user->isPoliceOfficer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authorized to create detection gates',
                    'error_code' => 'not_authorized'
                ], 403);
            }

            $gate = DetectionGate::create([
                'created_by' => $user->id,
                'location' => $validated['location'],
                'flagged_materials' => $validated['flagged_materials'],
                'is_active' => $validated['is_active'] ?? true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Detection gate created successfully',
                'data' => ['gate_id' => $gate->id]
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating detection gate', [
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

    public function logDetection(Request $request)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'gate_id' => 'required|integer',
                'detected_items' => 'required|array'
            ]);

            $user = User::where('minecraft_uuid', $validated['minecraft_uuid'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error_code' => 'user_not_found'
                ], 404);
            }

            $gate = DetectionGate::find($validated['gate_id']);
            if (!$gate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detection gate not found',
                    'error_code' => 'gate_not_found'
                ], 404);
            }

            if (!$gate->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detection gate is not active',
                    'error_code' => 'gate_inactive'
                ], 400);
            }

            $log = DetectionLog::create([
                'detection_gate_id' => $gate->id,
                'user_id' => $user->id,
                'detected_items' => $validated['detected_items']
            ]);

            $gate->update([
                'last_triggered' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Detection logged successfully',
                'data' => ['log_id' => $log->id]
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging detection', [
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

    public function toggleActive(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'is_active' => 'required|boolean'
            ]);

            $user = User::where('minecraft_uuid', $validated['minecraft_uuid'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error_code' => 'user_not_found'
                ], 404);
            }

            $gate = DetectionGate::find($id);
            if (!$gate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detection gate not found',
                    'error_code' => 'gate_not_found'
                ], 404);
            }

            if ($gate->created_by !== $user->id && !$user->isPoliceOfficer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authorized to modify this gate',
                    'error_code' => 'not_authorized'
                ], 403);
            }

            $gate->update([
                'is_active' => $validated['is_active']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Detection gate status updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling detection gate', [
                'error' => $e->getMessage(),
                'gate_id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error_code' => 'server_error'
            ], 500);
        }
    }
} 