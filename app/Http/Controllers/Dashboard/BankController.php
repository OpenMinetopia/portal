<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $bankAccount = $user->bankAccount;

        if (!$bankAccount) {
            $bankAccount = new BankAccount([
                'balance' => 0,
                'last_transaction' => now()
            ]);
        }

        $recentTransactions = $bankAccount->transactions()
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.bank.index', [
            'bankAccount' => $bankAccount,
            'recentTransactions' => $recentTransactions
        ]);
    }

    public function show(Request $request)
    {
        $user = auth()->user();
        $bankAccount = $user->bankAccount;

        if (!$bankAccount) {
            return redirect()->route('dashboard.bank.index');
        }

        $transactions = $bankAccount->transactions()
            ->latest()
            ->paginate(15);

        return view('dashboard.bank.show', [
            'bankAccount' => $bankAccount,
            'transactions' => $transactions
        ]);
    }
} 