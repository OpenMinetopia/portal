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

class CompaniesController extends Controller
{
    public function index()
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

        return view('portal.companies.index', compact('companies', 'requests'));
    }

    public function register()
    {
        // Show available company types
        $companyTypes = CompanyType::where('is_active', true)->get();
        return view('portal.companies.register', compact('companyTypes'));
    }

    public function request(CompanyType $companyType)
    {
        if (!$companyType->is_active) {
            return back()->with('error', 'Dit bedrijfstype is momenteel niet beschikbaar.');
        }

        return view('portal.companies.request', compact('companyType'));
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
            // Build validation rules based on company type's form fields
            $rules = [
                'form_data.Bedrijfsnaam' => ['required', 'string', 'max:255'],
            ];
            
            $messages = [
                'form_data.Bedrijfsnaam.required' => 'De bedrijfsnaam is verplicht.',
                'form_data.Bedrijfsnaam.max' => 'De bedrijfsnaam mag maximaal 255 karakters bevatten.',
            ];

            foreach ($companyType->form_fields as $field) {
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
                        if (isset($field['options']) && is_array($field['options'])) {
                            $fieldRules[] = Rule::in($field['options']);
                            $messages[$fieldName.'.in'] = "De geselecteerde optie voor '{$field['label']}' is ongeldig.";
                        }
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

                $rules[$fieldName] = $fieldRules;
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

            // Create the company request
            CompanyRequest::create([
                'company_type_id' => $companyType->id,
                'user_id' => auth()->id(),
                'name' => $validated['form_data']['Bedrijfsnaam'],
                'form_data' => $validated['form_data'],
                'status' => 'pending'
            ]);

            return redirect()
                ->route('portal.companies.index')
                ->with('success', 'Je bedrijfs aanvraag is succesvol ingediend.');

        } catch (\Exception $e) {
            \Log::error('Error creating company request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Er is een fout opgetreden bij het indienen van je aanvraag.']);
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

    public function showRequest(CompanyRequest $companyRequest)
    {
        // Check if user owns the request
        if ($companyRequest->user_id !== auth()->id()) {
            abort(403);
        }

        return view('portal.companies.requests.show', compact('companyRequest'));
    }

    public function show(Company $company)
    {
        // Check if user owns the company
        if ($company->owner_id !== auth()->id()) {
            abort(403);
        }

        // Get dissolution request if exists
        $dissolutionRequest = $company->dissolutionRequest;

        return view('portal.companies.show', compact('company', 'dissolutionRequest'));
    }
} 