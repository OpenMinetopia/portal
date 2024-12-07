<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ChatMessage;
use App\Models\Prefix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'message' => 'required|string',
                'format' => 'required|string',
                'radius' => 'nullable|integer',
                'recipients' => 'nullable|array',
                'recipients.*' => 'string|size:32'
            ]);

            $user = User::where('minecraft_uuid', $validated['minecraft_uuid'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error_code' => 'user_not_found'
                ], 404);
            }

            // If recipients are specified, verify they exist
            $recipientIds = [];
            if (!empty($validated['recipients'])) {
                $recipients = User::whereIn('minecraft_uuid', $validated['recipients'])->get();
                if ($recipients->count() !== count($validated['recipients'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'One or more recipients not found',
                        'error_code' => 'invalid_recipients'
                    ], 404);
                }
                $recipientIds = $recipients->pluck('id')->toArray();
            }

            $message = ChatMessage::create([
                'user_id' => $user->id,
                'message' => $validated['message'],
                'format' => $validated['format'],
                'radius' => $validated['radius'] ?? null,
                'recipients' => $recipientIds
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Chat message stored',
                'data' => ['message_id' => $message->id]
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing chat message', [
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

    public function updatePrefix(Request $request)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'prefix' => 'required|string',
                'color' => 'nullable|string',
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

            // Deactivate all other prefixes if this one is being activated
            if ($validated['is_active'] ?? false) {
                $user->prefixes()->update(['is_active' => false]);
            }

            $prefix = Prefix::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => $validated['prefix']
                ],
                [
                    'color' => $validated['color'] ?? null,
                    'is_active' => $validated['is_active'] ?? false
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Prefix updated successfully',
                'data' => [
                    'prefix_id' => $prefix->id,
                    'is_active' => $prefix->is_active
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating prefix', [
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