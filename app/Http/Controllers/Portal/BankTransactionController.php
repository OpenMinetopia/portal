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
        $users = User::where('id', '!=', auth()->id())->get();

        return view('portal.bank-accounts.transactions.create', [
            'account' => $account,
            'users' => $users,
            'allAccounts' => auth()->user()->bank_accounts
        ]);
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
        ], [
            'transferType.required' => 'Selecteer een type overboeking.',
            'to_user_id.required_if' => 'Selecteer een ontvanger.',
            'to_user_id.exists' => 'Deze gebruiker bestaat niet.',
            'to_account_uuid.required' => 'Selecteer een rekening van de ontvanger.',
            'amount.required' => 'Vul een bedrag in.',
            'amount.numeric' => 'Het bedrag moet een getal zijn.',
            'amount.min' => 'Het bedrag moet minimaal €0,01 zijn.',
            'description.required' => 'Vul een omschrijving in.',
            'description.max' => 'De omschrijving mag maximaal 255 karakters bevatten.'
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