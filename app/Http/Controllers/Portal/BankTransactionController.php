<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\BankTransaction;
use App\Models\User;
use App\Services\Plugin\BankingService;
use App\Notifications\BankTransactionNotification;
use Illuminate\Http\Request;

class BankTransactionController extends Controller
{
    protected BankingService $bankingService;

    public function __construct(BankingService $bankingService)
    {
        $this->bankingService = $bankingService;
    }

    public function create(string $accountUuid)
    {
        // Check if user has access to this account
        $account = collect(auth()->user()->bank_accounts)->firstWhere('uuid', $accountUuid);
        if (!$account) {
            abort(403);
        }

        // Get all users except current user
        $users = User::where('id', '!=', auth()->id())
            ->where('minecraft_verified', true)
            ->get();

        // Get all accounts for the current user
        $ownAccounts = collect(auth()->user()->bank_accounts)
            ->filter(function($acc) use ($accountUuid) {
                return $acc['uuid'] !== $accountUuid;
            })
            ->values()
            ->toArray();

        return view('portal.bank-accounts.transactions.create', [
            'account' => $account,
            'users' => $users,
            'allAccounts' => $ownAccounts
        ]);
    }

    public function getUserAccounts(string $userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            \Log::info('Fetching bank accounts for user', [
                'userId' => $userId,
                'minecraft_uuid' => $user->minecraft_plain_uuid
            ]);
            
            $accounts = $this->bankingService->getPlayerBankAccounts($user->minecraft_plain_uuid);
            
            // Only return PRIVATE accounts without balance
            $privateAccounts = collect($accounts)
                ->filter(function($account) {
                    return $account['type'] === 'PRIVATE';
                })
                ->map(function($account) {
                    return [
                        'uuid' => $account['uuid'],
                        'name' => $account['name'],
                        'type' => $account['type']
                    ];
                })
                ->values()
                ->toArray();

            return response()->json($privateAccounts);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch user accounts', [
                'userId' => $userId,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to fetch accounts'], 500);
        }
    }

    public function store(Request $request, string $accountUuid)
    {
        // Validate request
        $validated = $request->validate([
            'transferType' => 'required|in:player,own',
            'to_user_id' => 'required_if:transferType,player|exists:users,id',
            'to_account_uuid' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255'
        ]);

        // Check if user has access to source account
        $fromAccount = collect(auth()->user()->bank_accounts)->firstWhere('uuid', $accountUuid);
        if (!$fromAccount) {
            return back()->withInput()->withErrors(['error' => 'Je hebt geen toegang tot deze rekening.']);
        }

        // Check if account has sufficient balance
        if ($fromAccount['balance'] < $validated['amount']) {
            return back()->withInput()->withErrors(['error' => 'Je hebt onvoldoende saldo op deze rekening.']);
        }

        // Check if target account exists and is different from source
        if ($validated['to_account_uuid'] === $accountUuid) {
            return back()->withInput()->withErrors(['error' => 'Je kunt geen geld overmaken naar dezelfde rekening.']);
        }

        try {
            // Withdraw from sender's account
            if (!$this->bankingService->withdraw($accountUuid, $validated['amount'])) {
                throw new \Exception('Failed to withdraw money from sender account');
            }

            // Deposit to receiver's account
            if (!$this->bankingService->deposit($validated['to_account_uuid'], $validated['amount'])) {
                // Rollback withdrawal if deposit fails
                $this->bankingService->deposit($accountUuid, $validated['amount']);
                throw new \Exception('Failed to deposit money to receiver account');
            }

            // Create transaction record
            $transaction = BankTransaction::create([
                'from_user_id' => auth()->id(),
                'to_user_id' => $validated['transferType'] === 'player' ? $validated['to_user_id'] : auth()->id(),
                'from_account_uuid' => $accountUuid,
                'to_account_uuid' => $validated['to_account_uuid'],
                'amount' => $validated['amount'],
                'description' => $validated['description']
            ]);

            // Send notifications
            auth()->user()->notify(new BankTransactionNotification($transaction, false));
            if ($transaction->to_user_id !== auth()->id()) {
                $transaction->toUser->notify(new BankTransactionNotification($transaction, true));
            }

            return redirect()
                ->route('portal.bank-accounts.show', $accountUuid)
                ->with('success', sprintf(
                    'Je hebt succesvol €%s overgemaakt naar %s.',
                    number_format($validated['amount'], 2, ',', '.'),
                    $transaction->to_user_id === auth()->id() ? 'je andere rekening' : $transaction->toUser->minecraft_username
                ));

        } catch (\Exception $e) {
            \Log::error('Error processing bank transaction', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => auth()->id(),
                'account' => $accountUuid
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Er is een fout opgetreden bij het uitvoeren van de transactie. Probeer het later opnieuw.']);
        }
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        
        return User::where('minecraft_username', 'LIKE', "%{$query}%")
            ->where('id', '!=', auth()->id())
            ->where('minecraft_verified', true)
            ->select('id', 'minecraft_username', 'minecraft_uuid')
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->minecraft_username,
                    'avatar' => "https://crafatar.com/avatars/{$user->minecraft_uuid}?size=32&overlay=true"
                ];
            });
    }
} 