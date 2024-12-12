<?php

namespace App\Http\Controllers\Portal\Admin;

use App\Http\Controllers\Controller;
use App\Services\Plugin\BankingService;
use App\Services\MojangApiService;

class AdminBankAccountController extends Controller
{
    protected BankingService $bankingService;
    protected MojangApiService $mojangApi;

    public function __construct(BankingService $bankingService, MojangApiService $mojangApi)
    {
        $this->bankingService = $bankingService;
        $this->mojangApi = $mojangApi;
    }

    public function index()
    {
        $accounts = $this->bankingService->getAllBankAccounts();

        return view('portal.admin.bank-accounts.index', [
            'accounts' => $accounts
        ]);
    }

    public function show(string $uuid)
    {
        $account = $this->bankingService->getBankAccount($uuid);
        
        if (!$account) {
            abort(404);
        }

        $users = $this->bankingService->getBankAccountUsers($uuid);

        return view('portal.admin.bank-accounts.show', [
            'account' => $account,
            'users' => $users
        ]);
    }
} 