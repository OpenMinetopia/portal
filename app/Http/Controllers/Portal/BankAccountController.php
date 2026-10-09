<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Services\Plugin\BankingService;

class BankAccountController extends Controller
{
    public function index()
    {
        $accounts = auth()->user()->bank_accounts;

        return view('portal.bank-accounts.index', [
            'accounts' => $accounts
        ]);
    }

    public function show(string $uuid)
    {
        if (!collect(auth()->user()->bank_accounts)->contains('uuid', $uuid)) {
            abort(403);
        }

        $bankingService = app(BankingService::class);
        $account = $bankingService->getBankAccount($uuid);
        $users = $bankingService->getBankAccountUsers($uuid);

        return view('portal.bank-accounts.show', [
            'account' => $account,
            'users' => $users
        ]);
    }
}
