<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $transactions = $user->bankAccount?->transactions()
            ->latest()
            ->paginate(15);

        return view('dashboard.transactions.index', [
            'transactions' => $transactions ?? collect(),
            'stats' => [
                'total_income' => $user->bankAccount?->transactions()
                    ->where('type', 'deposit')
                    ->sum('amount') ?? 0,
                'total_expenses' => $user->bankAccount?->transactions()
                    ->where('type', 'withdraw')
                    ->sum('amount') ?? 0
            ]
        ]);
    }

    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        
        return view('dashboard.transactions.show', [
            'transaction' => $transaction->load(['bankAccount.user'])
        ]);
    }
} 