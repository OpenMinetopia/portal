<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $bankAccount = $user->bankAccount;

        return view('portal.bank.index', [
            'bankAccount' => $bankAccount,
            'recentTransactions' => $bankAccount ? $bankAccount->transactions()
                ->latest()
                ->take(5)
                ->get() : collect(),
            'stats' => [
                'balance' => $user->getCurrentBalance(),
                'income' => $bankAccount ? $bankAccount->transactions()
                    ->where('type', 'deposit')
                    ->sum('amount') : 0,
                'expenses' => $bankAccount ? $bankAccount->transactions()
                    ->where('type', 'withdraw')
                    ->sum('amount') : 0
            ]
        ]);
    }
} 