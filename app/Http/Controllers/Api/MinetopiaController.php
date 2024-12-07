<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MinetopiaController extends Controller
{
    public function updateBalance(Request $request)
    {
        try {
            $validated = $request->validate([
                'minecraft_uuid' => 'required|string|size:32',
                'amount' => 'required|numeric',
                'transaction_type' => 'required|string|in:add,subtract,set',
                'description' => 'nullable|string'
            ]);

            $user = User::where('minecraft_uuid', $validated['minecraft_uuid'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error_code' => 'user_not_found'
                ], 404);
            }

            return DB::transaction(function () use ($user, $validated) {
                $bankAccount = BankAccount::firstOrCreate(
                    ['user_id' => $user->id],
                    ['balance' => 0]
                );

                $oldBalance = $bankAccount->balance;
                $newBalance = $oldBalance;

                switch ($validated['transaction_type']) {
                    case 'add':
                        $newBalance = $oldBalance + $validated['amount'];
                        break;
                    case 'subtract':
                        $newBalance = $oldBalance - $validated['amount'];
                        if ($newBalance < 0) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Insufficient balance',
                                'error_code' => 'insufficient_balance'
                            ], 400);
                        }
                        break;
                    case 'set':
                        $newBalance = $validated['amount'];
                        break;
                }

                $bankAccount->update([
                    'balance' => $newBalance,
                    'last_transaction' => now()
                ]);

                Transaction::create([
                    'bank_account_id' => $bankAccount->id,
                    'amount' => $validated['amount'],
                    'description' => $validated['description'] ?? 'Balance update',
                    'type' => $validated['transaction_type']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Balance updated successfully',
                    'data' => [
                        'old_balance' => $oldBalance,
                        'new_balance' => $newBalance,
                        'difference' => $newBalance - $oldBalance
                    ]
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Error updating balance', [
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

    public function getBalance(string $minecraft_uuid)
    {
        try {
            $user = User::where('minecraft_uuid', $minecraft_uuid)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error_code' => 'user_not_found'
                ], 404);
            }

            $bankAccount = $user->bankAccounts()->first();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'balance' => $bankAccount ? $bankAccount->balance : 0,
                    'last_transaction' => $bankAccount ? $bankAccount->last_transaction : null
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting balance', [
                'error' => $e->getMessage(),
                'minecraft_uuid' => $minecraft_uuid
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error_code' => 'server_error'
            ], 500);
        }
    }
} 