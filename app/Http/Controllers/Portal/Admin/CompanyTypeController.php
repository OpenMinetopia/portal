<?php

namespace App\Http\Controllers\Portal\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyType;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompanyTypeController extends Controller
{
    public function index()
    {
        $companyTypes = CompanyType::latest()->paginate(10);
        return view('portal.admin.companies.types.index', compact('companyTypes'));
    }

    public function create()
    {
        $roles = Role::all();
        $suggestedFields = [
            [
                'label' => 'Plotnummer',
                'type' => 'text',
                'required' => false,
            ],
            [
                'label' => 'Bedrijfsactiviteiten',
                'type' => 'textarea',
                'required' => false,
            ],
            [
                'label' => 'BTW Nummer',
                'type' => 'text',
                'required' => false,
            ]
        ];
        
        return view('portal.admin.companies.types.create', compact('roles', 'suggestedFields'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:company_types',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'is_active' => 'boolean',
                'authorized_roles' => 'required|array|min:1',
                'authorized_roles.*' => 'exists:roles,id',
                'form_fields' => 'required|array|min:1',
                'form_fields.*.type' => 'required|in:text,textarea,number,select,checkbox',
                'form_fields.*.label' => 'required|string|max:255',
                'form_fields.*.required' => 'boolean',
                'form_fields.*.options' => 'array|required_if:form_fields.*.type,select|min:1'
            ]);

            $validated['slug'] = Str::slug($validated['name']);

            CompanyType::create($validated);

            return redirect()
                ->route('portal.admin.companies.types.index')
                ->with('success', 'Bedrijfstype succesvol aangemaakt.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Er is een fout opgetreden bij het aanmaken van het bedrijfstype.']);
        }
    }

    public function edit(CompanyType $companyType)
    {
        $roles = Role::all();
        return view('portal.admin.companies.types.edit', compact('companyType', 'roles'));
    }

    public function update(Request $request, CompanyType $companyType)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:company_types,name,' . $companyType->id,
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'is_active' => 'boolean',
                'authorized_roles' => 'required|array|min:1',
                'authorized_roles.*' => 'exists:roles,id',
                'form_fields' => 'nullable|array',
                'form_fields.*.type' => 'required_with:form_fields|in:text,textarea,number,select,checkbox',
                'form_fields.*.label' => 'required_with:form_fields|string|max:255',
                'form_fields.*.required' => 'nullable|boolean',
                'form_fields.*.options' => 'array|required_if:form_fields.*.type,select'
            ]);

            $validated['slug'] = Str::slug($validated['name']);

            if (empty($validated['form_fields'])) {
                $validated['form_fields'] = $companyType->form_fields;
            }

            $companyType->update($validated);

            return redirect()
                ->route('portal.admin.companies.types.index')
                ->with('success', 'Bedrijfstype succesvol bijgewerkt.');

        } catch (\Exception $e) {
            \Log::error('Error updating company type', [
                'id' => $companyType->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Er is een fout opgetreden bij het bijwerken van het bedrijfstype.']);
        }
    }

    public function destroy(CompanyType $companyType)
    {
        try {
            if ($companyType->companies()->exists()) {
                return back()->with('error', 'Dit bedrijfstype kan niet worden verwijderd omdat er bedrijven aan gekoppeld zijn.');
            }

            if ($companyType->requests()->exists()) {
                return back()->with('error', 'Dit bedrijfstype kan niet worden verwijderd omdat er aanvragen aan gekoppeld zijn.');
            }

            $companyType->delete();

            return redirect()
                ->route('portal.admin.companies.types.index')
                ->with('success', 'Bedrijfstype succesvol verwijderd.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
} 