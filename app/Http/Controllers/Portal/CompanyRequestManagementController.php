<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\CompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\Plugin\BankingService;
use App\Models\CompanySetting;
use App\Notifications\CompanyRequestHandled;

class CompanyRequestManagementController extends Controller
{
    protected BankingService $bankingService;

    public function __construct(BankingService $bankingService)
    {
        $this->bankingService = $bankingService;
    }

    public function index()
    {
        $requests = CompanyRequest::with(['type', 'user'])
            ->latest()
            ->paginate(10);

        return view('portal.companies.manage.index', compact('requests'));
    }

    public function show(CompanyRequest $companyRequest)
    {
        return view('portal.companies.manage.show', compact('companyRequest'));
    }

    public function handle(Request $request, CompanyRequest $companyRequest)
    {
        // Validate request
        $validated = $request->validate([
            'status' => 'required|in:approved,denied',
            'admin_notes' => 'required|string|max:1000',
            'should_refund' => 'nullable|boolean'
        ]);

        try {
            // If approving, try to create company first
            if ($validated['status'] === 'approved') {
                try {
                    // Generate KvK number
                    $typeId = str_pad($companyRequest->company_type_id, 2, '0', STR_PAD_LEFT);
                    $random = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    $kvkNumber = "KVK-{$typeId}{$random}";

                    // Get description from form data
                    $description = $companyRequest->form_data['Beschrijving'] ??
                                 $companyRequest->form_data['Bedrijfsactiviteiten'] ??
                                 null;

                    // Try to create the company
                    $company = Company::create([
                        'name' => $companyRequest->name,
                        'type_id' => $companyRequest->company_type_id,
                        'owner_id' => $companyRequest->user_id,
                        'kvk_number' => $kvkNumber,
                        'description' => $description,
                        'data' => json_encode($companyRequest->form_data),
                        'is_active' => true,
                        'slug' => Str::slug($companyRequest->name),
                        'company_request_id' => $companyRequest->id,
                    ]);

                    // Only if company creation succeeds, update the request status
                    $companyRequest->update([
                        'status' => $validated['status'],
                        'admin_notes' => $validated['admin_notes'],
                        'handled_by' => auth()->id(),
                        'handled_at' => now(),
                    ]);

                    // Send notification
                    $companyRequest->user->notify(new CompanyRequestHandled($companyRequest));

                    return redirect()
                        ->route('portal.companies.manage.show', $companyRequest)
                        ->with('success', 'De bedrijfs aanvraag is succesvol goedgekeurd en het bedrijf is aangemaakt.');

                } catch (\Exception $e) {
                    return back()
                        ->withInput()
                        ->withErrors(['error' => 'Er is een fout opgetreden bij het aanmaken van het bedrijf. De aanvraag is niet verwerkt.']);
                }
            } else {
                // Handle refund if requested
                if ($request->has('should_refund')) {
                    $settings = CompanySetting::first();
                    if (!$settings || !$settings->payout_bank_account_uuid) {
                        throw new \Exception('Geen uitbetalingsrekening geconfigureerd voor bedrijven.');
                    }

                    // Withdraw from government account
                    $withdrawSuccess = $this->bankingService->withdraw(
                        $settings->payout_bank_account_uuid,
                        $companyRequest->price
                    );

                    if (!$withdrawSuccess) {
                        throw new \Exception('Failed to withdraw money from government account');
                    }

                    // Deposit back to user's account
                    $depositSuccess = $this->bankingService->deposit(
                        $companyRequest->bank_account_uuid,
                        $companyRequest->price
                    );

                    if (!$depositSuccess) {
                        // Rollback withdrawal if deposit fails
                        $this->bankingService->deposit(
                            $settings->payout_bank_account_uuid,
                            $companyRequest->price
                        );
                        throw new \Exception('Failed to deposit money to user account');
                    }
                }

                // Update the request status
                $companyRequest->update([
                    'status' => $validated['status'],
                    'admin_notes' => $validated['admin_notes'],
                    'handled_by' => auth()->id(),
                    'handled_at' => now(),
                    'refunded' => $request->has('should_refund')
                ]);

                // Send notification
                $companyRequest->user->notify(new CompanyRequestHandled($companyRequest));

                return redirect()
                    ->route('portal.companies.manage.index')
                    ->with('success', 'De bedrijfs aanvraag is succesvol afgewezen.' . 
                        ($request->has('should_refund') ? ' Het bedrag is teruggestort.' : ''));
            }

        } catch (\Exception $e) {
            \Log::error('Error handling company request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'company_request' => $companyRequest->id,
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
