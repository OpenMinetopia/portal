<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MojangApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class MinecraftVerificationController extends Controller
{
    public function verify(Request $request)
    {
        try {
            $validated = $request->validate([
                'token' => 'required|string|size:32',
                'minecraft_username' => 'required|string|max:255',
                'minecraft_uuid' => 'required|string|size:36', // Allow UUID with dashes
            ]);

            // Find user by token
            $user = User::where('token', $validated['token'])->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token.',
                    'error_code' => 'invalid_token'
                ], 404);
            }

            // Check if account is already verified
            if ($user->minecraft_verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'This token has already been used.',
                    'error_code' => 'already_verified'
                ], 400);
            }

            // Check if minecraft username matches
            if (strtolower($user->minecraft_username) !== strtolower($validated['minecraft_username'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Minecraft username does not match the registered account.',
                    'error_code' => 'username_mismatch'
                ], 400);
            }

            if ($user->minecraft_plain_uuid !== $validated['minecraft_uuid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Minecraft UUID does not match the registered account.',
                    'error_code' => 'uuid_mismatch'
                ], 400);
            }

            // Verify the account
            $user->update([
                'minecraft_verified' => true,
                'minecraft_verified_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Account successfully verified.',
                'data' => [
                    'user_id' => $user->id,
                    'minecraft_username' => $user->minecraft_username,
                    'verified_at' => $user->minecraft_verified_at
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
                'error_code' => 'validation_failed'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during verification.',
                'error_code' => 'server_error'
            ], 500);
        }
    }
}
