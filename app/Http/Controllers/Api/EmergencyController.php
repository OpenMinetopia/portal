<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmergencyCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmergencyController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'location' => 'required|array',
                'message' => 'required|string'
            ]);

            $user = User::where('minecraft_uuid', $validated['minecraft_uuid'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error_code' => 'user_not_found'
                ], 404);
            }

            $call = EmergencyCall::create([
                'caller_id' => $user->id,
                'location' => $validated['location'],
                'message' => $validated['message'],
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Emergency call created',
                'data' => [
                    'call_id' => $call->id
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating emergency call', [
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

    public function respond(Request $request, $id)
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

            $call = EmergencyCall::find($id);
            if (!$call) {
                return response()->json([
                    'success' => false,
                    'message' => 'Emergency call not found',
                    'error_code' => 'call_not_found'
                ], 404);
            }

            $call->update([
                'responded_by' => $user->id,
                'status' => 'responded'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Emergency call updated'
            ]);
        } catch (\Exception $e) {
            Log::error('Error responding to emergency call', [
                'error' => $e->getMessage(),
                'call_id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error_code' => 'server_error'
            ], 500);
        }
    }

    public function close(Request $request, $id)
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

            $call = EmergencyCall::find($id);
            if (!$call) {
                return response()->json([
                    'success' => false,
                    'message' => 'Emergency call not found',
                    'error_code' => 'call_not_found'
                ], 404);
            }

            if ($call->responded_by !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authorized to close this call',
                    'error_code' => 'not_authorized'
                ], 403);
            }

            $call->update([
                'status' => 'closed'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Emergency call closed'
            ]);
        } catch (\Exception $e) {
            Log::error('Error closing emergency call', [
                'error' => $e->getMessage(),
                'call_id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error_code' => 'server_error'
            ], 500);
        }
    }
} 