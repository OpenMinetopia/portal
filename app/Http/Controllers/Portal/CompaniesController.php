<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyType;
use App\Models\CompanyRequest;
use App\Models\DissolutionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\Plugin\BankingService;
use App\Models\CompanySetting;

class CompaniesController extends Controller
{
    protected BankingService $bankingService;

    public function __construct(BankingService $bankingService)
    {
        $this->bankingService = $bankingService;
    }

    public function index(Request $request)
    {
        // Get user's companies and requests
        $companies = Company::where('owner_id', auth()->id())
            ->with('type')
            ->latest()
            ->get();
            
        $requests = CompanyRequest::where('user_id', auth()->id())
            ->with(['type', 'handler'])
            ->latest()
            ->get();

        // Check if user wants V2 layout
        $layout = $request->get('layout', 'v2'); // Default to V2
        
        if ($layout === 'v1') {
            return view('portal.companies.index', compact('companies', 'requests'));
        }
        
        return view('portal.v2.companies.index', compact('companies', 'requests'));
    }

    public function register(Request $request)
    {
        // Show available company types
        $companyTypes = CompanyType::where('is_active', true)->get();
        
        // Check if user wants V2 layout
        $layout = $request->get('layout', 'v2'); // Default to V2
        
        if ($layout === 'v1') {
            return view('portal.companies.register', compact('companyTypes'));
        }
        
        return view('portal.v2.companies.register', compact('companyTypes'));
    }

    public function request(Request $request, CompanyType $companyType)
    {
        if (!$companyType->is_active) {
            return back()->with('error', 'Dit bedrijfstype is momenteel niet beschikbaar.');
        }

        // Check if user wants V2 layout
        $layout = $request->get('layout', 'v2'); // Default to V2
        
        if ($layout === 'v1') {
            return view('portal.companies.request', [
                'companyType' => $companyType,
                'bank_accounts' => auth()->user()->bank_accounts
            ]);
        }

        return view('portal.v2.companies.request', [
            'companyType' => $companyType,
            'bank_accounts' => auth()->user()->bank_accounts
        ]);
    }

    public function lookup(Request $request)
    {
        $name = $request->input('name');
        
        // Check both existing companies and pending requests
        $existingCompany = Company::where('name', 'LIKE', $name)->first();
        $pendingRequest = CompanyRequest::where('name', 'LIKE', $name)
            ->where('status', 'pending')
            ->first();

        if ($existingCompany || $pendingRequest) {
            return response()->json([
                'exists' => true,
                'message' => 'Deze bedrijfsnaam is al in gebruik. Je aanvraag kan mogelijk worden afgewezen indien het bedrijf in dezelfde branche actief is.'
            ]);
        }

        return response()->json([
            'exists' => false,
            'message' => 'Deze bedrijfsnaam is beschikbaar!'
        ]);
    }

    public function store(Request $request, CompanyType $companyType)
    {
        try {
            // Build validation rules
            $rules = [
                'form_data.Bedrijfsnaam' => [],
                'bank_account_uuid' => []
            ];
            
            $messages = [
                'form_data.Bedrijfsnaam.required' => 'De bedrijfsnaam is verplicht.',
                'form_data.Bedrijfsnaam.max' => 'De bedrijfsnaam mag maximaal 255 karakters bevatten.',
                'bank_account_uuid.required' => 'Selecteer een bankrekening om het bedrijf mee te betalen.'
            ];

            // Add base rules
            $rules['form_data.Bedrijfsnaam'][] = 'required';
            $rules['form_data.Bedrijfsnaam'][] = 'string';
            $rules['form_data.Bedrijfsnaam'][] = 'max:255';
            $rules['bank_account_uuid'][] = 'required';
            $rules['bank_account_uuid'][] = 'string';

            foreach ($companyType->form_fields as $field) {
                $fieldName = 'form_data.' . $field['label'];
                if ($fieldName !== 'form_data.Bedrijfsnaam') {  // Skip if already handled
                    $rules[$fieldName] = [];

                    if ($field['required']) {
                        $rules[$fieldName][] = 'required';
                        $messages[$fieldName.'.required'] = "Het veld '{$field['label']}' is verplicht.";
                    } else {
                        $rules[$fieldName][] = 'nullable';
                    }

                    switch ($field['type']) {
                        case 'number':
                            $rules[$fieldName][] = 'numeric';
                            $messages[$fieldName.'.numeric'] = "Het veld '{$field['label']}' moet een nummer zijn.";
                            break;
                        case 'select':
                            if (isset($field['options']) && is_array($field['options'])) {
                                $rules[$fieldName][] = Rule::in($field['options']);
                                $messages[$fieldName.'.in'] = "De geselecteerde optie voor '{$field['label']}' is ongeldig.";
                            }
                            break;
                        case 'checkbox':
                            $rules[$fieldName][] = 'boolean';
                            $messages[$fieldName.'.boolean'] = "Het veld '{$field['label']}' moet ja of nee zijn.";
                            break;
                        default:
                            $rules[$fieldName][] = 'string';
                            $messages[$fieldName.'.string'] = "Het veld '{$field['label']}' moet tekst zijn.";
                            break;
                    }
                }
            }

            // Convert rules arrays to strings
            foreach ($rules as $field => $fieldRules) {
                $rules[$field] = implode('|', $fieldRules);
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
            $settings = CompanySetting::first();
            if (!$settings || !$settings->payout_bank_account_uuid) {
                return back()->with('error', [
                    'title' => 'Configuratie fout',
                    'message' => 'Er is geen uitbetalingsrekening geconfigureerd voor bedrijven.'
                ]);
            }

            // Withdraw from user's account
            $withdrawSuccess = $this->bankingService->withdraw(
                $validated['bank_account_uuid'],
                $companyType->price
            );

            if (!$withdrawSuccess) {
                throw new \Exception('Failed to withdraw money from user account');
            }

            // Deposit to government account
            $depositSuccess = $this->bankingService->deposit(
                $settings->payout_bank_account_uuid,
                $companyType->price
            );

            if (!$depositSuccess) {
                // Rollback withdrawal if deposit fails
                $this->bankingService->deposit(
                    $validated['bank_account_uuid'],
                    $companyType->price
                );
                throw new \Exception('Failed to deposit money to government account');
            }

            // Create the company request
            CompanyRequest::create([
                'company_type_id' => $companyType->id,
                'user_id' => auth()->id(),
                'name' => $validated['form_data']['Bedrijfsnaam'],
                'form_data' => $validated['form_data'],
                'bank_account_uuid' => $validated['bank_account_uuid'],
                'price' => $companyType->price,
                'status' => 'pending'
            ]);

            return redirect()
                ->route('portal.companies.index')
                ->with('success', 'Je bedrijfs aanvraag is succesvol ingediend.');

        } catch (\Exception $e) {
            \Log::error('Error creating company request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => auth()->id(),
                'company_type' => $companyType->id
            ]);

            return back()
                ->withInput()
                ->with('error', [
                    'title' => 'Betaling mislukt',
                    'message' => 'Er is een fout opgetreden bij de betaling. Probeer het later opnieuw.'
                ]);
        }
    }

    public function dissolve(Company $company)
    {
        // Check if user owns the company
        if ($company->owner_id !== auth()->id()) {
            abort(403);
        }

        // Check if there's already a pending dissolution request
        if ($company->hasPendingDissolution()) {
            return back()->withErrors(['error' => 'Er is al een opheffingsverzoek in behandeling voor dit bedrijf.']);
        }

        try {
            // Create a dissolution request
            DissolutionRequest::create([
                'company_id' => $company->id,
                'user_id' => auth()->id(),
                'status' => 'pending',
                'reason' => 'Opheffing aangevraagd door eigenaar'
            ]);

            return back()->with('success', 'Je opheffingsverzoek is succesvol ingediend.');

        } catch (\Exception $e) {
            \Log::error('Error creating dissolution request', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Er is een fout opgetreden bij het indienen van je opheffingsverzoek.']);
        }
    }

    public function showRequest(Request $request, CompanyRequest $companyRequest)
    {
        // Check if user owns the request
        if ($companyRequest->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if user wants V2 layout
        $layout = $request->get('layout', 'v2'); // Default to V2
        
        if ($layout === 'v1') {
            return view('portal.companies.request-details', compact('companyRequest'));
        }
        
        return view('portal.v2.companies.request-details', compact('companyRequest'));
    }

    public function show(Request $request, Company $company)
    {
        // Check if user owns the company
        if ($company->owner_id !== auth()->id()) {
            abort(403);
        }

        // Get dissolution request if exists
        $dissolutionRequest = $company->dissolutionRequest;

        // Check if user wants V2 layout
        $layout = $request->get('layout', 'v2'); // Default to V2
        
        if ($layout === 'v1') {
            return view('portal.companies.show', compact('company', 'dissolutionRequest'));
        }
        
        return view('portal.v2.companies.show', compact('company', 'dissolutionRequest'));
    }
} 