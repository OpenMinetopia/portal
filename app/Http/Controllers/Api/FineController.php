<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Fine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FineController extends Controller
{
    public function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'officer_uuid' => 'required|string|size:32',
                'amount' => 'required|numeric|min:0',
                'reason' => 'required|string'
            ]);

            $user = User::where('minecraft_uuid', $validated['minecraft_uuid'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error_code' => 'user_not_found'
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
                    'message' => 'Not authorized to issue fines',
                    'error_code' => 'not_authorized'
                ], 403);
            }

            $fine = Fine::create([
                'user_id' => $user->id,
                'officer_id' => $officer->id,
                'amount' => $validated['amount'],
                'reason' => $validated['reason'],
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Fine issued successfully',
                'data' => ['fine_id' => $fine->id]
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating fine', [
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

    public function pay(Request $request, $id)
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

            $fine = Fine::find($id);
            if (!$fine) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fine not found',
                    'error_code' => 'fine_not_found'
                ], 404);
            }

            if ($fine->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authorized to pay this fine',
                    'error_code' => 'not_authorized'
                ], 403);
            }

            if ($fine->status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Fine already paid',
                    'error_code' => 'already_paid'
                ], 400);
            }

            $fine->update([
                'status' => 'paid',
                'paid_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Fine paid successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error paying fine', [
                'error' => $e->getMessage(),
                'fine_id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error_code' => 'server_error'
            ], 500);
        }
    }
} 