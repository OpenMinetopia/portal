<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Services\Plugin\BankingService;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index(Request $request)
    {
        $accounts = auth()->user()->bank_accounts;
        
        // Check if user wants V2 layout
        $layout = $request->get('layout', 'v2'); // Default to V2
        
        if ($layout === 'v1') {
            return view('portal.bank-accounts.index', [
                'accounts' => $accounts
            ]);
        }
        
        return view('portal.v2.bank-accounts.index', [
            'accounts' => $accounts
        ]);
    }

    public function show(Request $request, string $uuid)
    {
        $bankingService = app(BankingService::class);
        $account = $bankingService->getBankAccount($uuid);
        $users = $bankingService->getBankAccountUsers($uuid);

        // Check if user wants V2 layout
        $layout = $request->get('layout', 'v2'); // Default to V2
        
        if ($layout === 'v1') {
            return view('portal.bank-accounts.show', [
                'account' => $account,
                'users' => $users
            ]);
        }
        
        return view('portal.v2.bank-accounts.show', [
            'account' => $account,
            'users' => $users
        ]);
    }
}
