<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\PermitType;
use App\Models\PermitRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\Plugin\BankingService;
use App\Models\PermitSetting;

class PermitsController extends Controller
{
    protected BankingService $bankingService;

    public function __construct(BankingService $bankingService)
    {
        $this->bankingService = $bankingService;
    }

    public function index()
    {
        // Get all active permit types
        $permitTypes = PermitType::where('is_active', true)->get();
        
        // Get user's permit requests
        $requests = PermitRequest::where('user_id', auth()->id())
            ->with(['type', 'handler'])
            ->latest()
            ->get();

        // Determine layout version
        $layout = request()->get('layout', 'v2'); // Default to v2, fallback to v1

        if ($layout === 'v1') {
            return view('portal.permits.index', compact('permitTypes', 'requests'));
        }

        return view('portal.v2.permits.index', compact('permitTypes', 'requests'));
    }

    public function request(PermitType $permitType)
    {
        if (!$permitType->is_active) {
            return back()->with('error', 'Deze vergunning is momenteel niet beschikbaar.');
        }

        // Determine layout version
        $layout = request()->get('layout', 'v2'); // Default to v2, fallback to v1

        if ($layout === 'v1') {
            return view('portal.permits.request', compact('permitType'));
        }

        return view('portal.v2.permits.request', compact('permitType'));
    }

    public function store(Request $request, PermitType $permitType)
    {
        if (!$permitType->is_active) {
            return back()->with('error', 'Deze vergunning is momenteel niet beschikbaar.');
        }

        try {
            // Build validation rules based on permit type's form fields
            $rules = [
                'bank_account_uuid' => 'required|string'
            ];
            $messages = [
                'bank_account_uuid.required' => 'Selecteer een bankrekening om de vergunning mee te betalen.'
            ];

            foreach ($permitType->form_fields as $field) {
                $fieldName = 'form_data.' . $field['label'];
                $fieldRules = [];

                if ($field['required']) {
                    $fieldRules[] = 'required';
                    $messages[$fieldName.'.required'] = "Het veld '{$field['label']}' is verplicht.";
                } else {
                    $fieldRules[] = 'nullable';
                }

                switch ($field['type']) {
                    case 'number':
                        $fieldRules[] = 'numeric';
                        $messages[$fieldName.'.numeric'] = "Het veld '{$field['label']}' moet een nummer zijn.";
                        break;
                    case 'select':
                        $fieldRules[] = 'in:' . implode(',', $field['options']);
                        $messages[$fieldName.'.in'] = "De geselecteerde optie voor '{$field['label']}' is ongeldig.";
                        break;
                    case 'checkbox':
                        $fieldRules[] = 'boolean';
                        $messages[$fieldName.'.boolean'] = "Het veld '{$field['label']}' moet ja of nee zijn.";
                        break;
                    default:
                        $fieldRules[] = 'string';
                        $messages[$fieldName.'.string'] = "Het veld '{$field['label']}' moet tekst zijn.";
                        break;
                }

                $rules[$fieldName] = implode('|', $fieldRules);
            }

            // Validate the request
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $validated = $validator->validated();

            // Get settings for payout account
            $settings = PermitSetting::first();
            if (!$settings || !$settings->payout_bank_account_uuid) {
                return back()->with('error', [
                    'title' => 'Configuratie fout',
                    'message' => 'Er is geen uitbetalingsrekening geconfigureerd voor vergunningen.'
                ]);
            }

            // Withdraw from user's account
            $withdrawSuccess = $this->bankingService->withdraw(
                $validated['bank_account_uuid'],
                $permitType->price
            );

            if (!$withdrawSuccess) {
                throw new \Exception('Failed to withdraw money from user account');
            }

            // Deposit to government account
            $depositSuccess = $this->bankingService->deposit(
                $settings->payout_bank_account_uuid,
                $permitType->price
            );

            if (!$depositSuccess) {
                // Rollback withdrawal if deposit fails
                $this->bankingService->deposit(
                    $validated['bank_account_uuid'],
                    $permitType->price
                );
                throw new \Exception('Failed to deposit money to government account');
            }

            // Create the permit request
            $permitRequest = PermitRequest::create([
                'permit_type_id' => $permitType->id,
                'user_id' => auth()->id(),
                'form_data' => $validated['form_data'],
                'bank_account_uuid' => $validated['bank_account_uuid'],
                'price' => $permitType->price,
                'status' => 'pending'
            ]);

            return redirect()
                ->route('portal.permits.show', $permitRequest)
                ->with('success', 'Je vergunning aanvraag is succesvol ingediend.');

        } catch (\Exception $e) {
            \Log::error('Error creating permit request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => auth()->id(),
                'permit_type' => $permitType->id
            ]);

            return back()
                ->withInput()
                ->with('error', [
                    'title' => 'Betaling mislukt',
                    'message' => 'Er is een fout opgetreden bij de betaling. Probeer het later opnieuw.'
                ]);
        }
    }

    public function show(PermitRequest $permitRequest)
    {
        if ($permitRequest->user_id !== auth()->id()) {
            abort(403);
        }

        // Determine layout version
        $layout = request()->get('layout', 'v2'); // Default to v2, fallback to v1

        if ($layout === 'v1') {
            return view('portal.permits.show', compact('permitRequest'));
        }

        return view('portal.v2.permits.show', compact('permitRequest'));
    }
} 