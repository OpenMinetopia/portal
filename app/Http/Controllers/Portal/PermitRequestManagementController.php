<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\PermitRequest;
use App\Models\PermitType;
use Illuminate\Http\Request;
use App\Services\Plugin\BankingService;
use App\Models\PermitSetting;
use App\Notifications\PermitRequestHandled;

class PermitRequestManagementController extends Controller
{
    protected BankingService $bankingService;

    public function __construct(BankingService $bankingService)
    {
        $this->bankingService = $bankingService;
    }

    public function index()
    {
        // Only requests for the permit types this user may handle
        $typeIds = PermitType::manageableIdsFor(auth()->user());
        $scoped = fn () => PermitRequest::whereIn('permit_type_id', $typeIds);

        $requests = $scoped()->with(['type', 'user', 'handler'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total' => $requests->total(),
            'pending' => $scoped()->where('status', 'pending')->count(),
            'approved' => $scoped()->where('status', 'approved')->count(),
            'denied' => $scoped()->where('status', 'denied')->count(),
        ];

        return view('portal.permits.manage.index', compact('requests', 'stats'));
    }

    public function show(PermitRequest $permitRequest)
    {
        // Check if user can manage this permit type
        if (!$permitRequest->type->userCanManage(auth()->user())) {
            abort(403, 'Je hebt geen toegang tot deze vergunning aanvraag.');
        }

        return view('portal.permits.manage.show', compact('permitRequest'));
    }

    public function handle(Request $request, PermitRequest $permitRequest)
    {
        // Check if user can manage this permit type
        if (!$permitRequest->type->userCanManage(auth()->user())) {
            abort(403, 'Je hebt geen toegang tot deze vergunning aanvraag.');
        }

        // Validate request
        $validated = $request->validate([
            'status' => 'required|in:approved,denied',
            'admin_notes' => 'required|string|min:10',
            'should_refund' => 'nullable|boolean'
        ], [
            'status.required' => 'Selecteer een status.',
            'status.in' => 'Ongeldige status geselecteerd.',
            'admin_notes.required' => 'Vul een toelichting in.',
            'admin_notes.min' => 'De toelichting moet minimaal 10 karakters bevatten.',
        ]);

        try {
            // Handle refund if denied and refund is requested
            if ($validated['status'] === 'denied' && $request->has('should_refund')) {
                $settings = PermitSetting::first();
                if (!$settings || !$settings->payout_bank_account_uuid) {
                    throw new \Exception('Geen uitbetalingsrekening geconfigureerd voor vergunningen.');
                }

                // Withdraw from government account
                $withdrawSuccess = $this->bankingService->withdraw(
                    $settings->payout_bank_account_uuid,
                    $permitRequest->type->price
                );

                if (!$withdrawSuccess) {
                    throw new \Exception('Failed to withdraw money from government account');
                }

                // Deposit back to user's account
                $depositSuccess = $this->bankingService->deposit(
                    $permitRequest->bank_account_uuid,
                    $permitRequest->type->price
                );

                if (!$depositSuccess) {
                    // Rollback withdrawal if deposit fails
                    $this->bankingService->deposit(
                        $settings->payout_bank_account_uuid,
                        $permitRequest->type->price
                    );
                    throw new \Exception('Failed to deposit money to user account');
                }
            }

            // Update permit request
            $permitRequest->update([
                'status' => $validated['status'],
                'admin_notes' => $validated['admin_notes'],
                'handled_by' => auth()->id(),
                'handled_at' => now(),
                'refunded' => $validated['status'] === 'denied' && $request->has('should_refund')
            ]);

            // Send notification
            $permitRequest->user->notify(new PermitRequestHandled($permitRequest));

            return redirect()
                ->route('portal.permits.manage.show', $permitRequest)
                ->with('success', 'Vergunning aanvraag succesvol verwerkt.' . 
                    ($validated['status'] === 'denied' && $request->has('should_refund') ? ' Het bedrag is teruggestort.' : ''));

        } catch (\Exception $e) {
            \Log::error('Error handling permit request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'permit_request' => $permitRequest->id,
                'user' => auth()->id()
            ]);

            return back()
                ->withInput()
                ->with('error', [
                    'title' => 'Verwerking mislukt',
                    'message' => 'Er is een fout opgetreden bij het verwerken van de aanvraag. ' . 
                        ($validated['status'] === 'denied' && $request->has('should_refund') ? 'De terugbetaling kon niet worden uitgevoerd.' : '')
                ]);
        }
    }
} 