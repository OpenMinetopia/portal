<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Teleporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TeleporterController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'name' => 'required|string',
                'location' => 'required|array',
                'display_lines' => 'required|array',
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

            $teleporter = Teleporter::create([
                'created_by' => $user->id,
                'name' => $validated['name'],
                'location' => $validated['location'],
                'display_lines' => $validated['display_lines'],
                'is_active' => $validated['is_active'] ?? true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Teleporter created successfully',
                'data' => ['teleporter_id' => $teleporter->id]
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating teleporter', [
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
                'name' => 'string',
                'display_lines' => 'array',
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

            $teleporter = Teleporter::find($id);
            if (!$teleporter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Teleporter not found',
                    'error_code' => 'teleporter_not_found'
                ], 404);
            }

            if ($teleporter->created_by !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authorized to update this teleporter',
                    'error_code' => 'not_authorized'
                ], 403);
            }

            $teleporter->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Teleporter updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating teleporter', [
                'error' => $e->getMessage(),
                'teleporter_id' => $id
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

            $teleporter = Teleporter::find($id);
            if (!$teleporter) {
                return response()->json([
                    'success' => false,
                    'message' => 'Teleporter not found',
                    'error_code' => 'teleporter_not_found'
                ], 404);
            }

            if ($teleporter->created_by !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authorized to delete this teleporter',
                    'error_code' => 'not_authorized'
                ], 403);
            }

            $teleporter->delete();

            return response()->json([
                'success' => true,
                'message' => 'Teleporter deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting teleporter', [
                'error' => $e->getMessage(),
                'teleporter_id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error_code' => 'server_error'
            ], 500);
        }
    }
} 