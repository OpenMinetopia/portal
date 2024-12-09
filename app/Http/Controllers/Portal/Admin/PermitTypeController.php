<?php

namespace App\Http\Controllers\Portal\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermitType;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermitTypeController extends Controller
{
    public function index()
    {
        $permitTypes = PermitType::latest()->paginate(10);
        
        return view('portal.admin.permits.types.index', compact('permitTypes'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('portal.admin.permits.types.create', compact('roles'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:permit_types',
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
            ], [
                'name.required' => 'De naam is verplicht.',
                'name.unique' => 'Deze naam is al in gebruik.',
                'price.required' => 'De prijs is verplicht.',
                'price.min' => 'De prijs moet minimaal 0 zijn.',
                'authorized_roles.required' => 'Selecteer minimaal één rol.',
                'authorized_roles.min' => 'Selecteer minimaal één rol.',
                'form_fields.required' => 'Voeg minimaal één formulierveld toe.',
                'form_fields.min' => 'Voeg minimaal één formulierveld toe.',
                'form_fields.*.label.required' => 'Elk veld moet een label hebben.',
                'form_fields.*.type.required' => 'Elk veld moet een type hebben.',
                'form_fields.*.type.in' => 'Ongeldig veldtype geselecteerd.',
                'form_fields.*.options.required_if' => 'Voeg minimaal één optie toe voor keuzelijsten.',
                'form_fields.*.options.min' => 'Voeg minimaal één optie toe voor keuzelijsten.'
            ]);

            $validated['slug'] = Str::slug($validated['name']);
            
            PermitType::create($validated);

            return redirect()
                ->route('portal.admin.permits.types.index')
                ->with('success', 'Vergunning type succesvol aangemaakt.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Er is een fout opgetreden bij het aanmaken van het vergunning type.'])
                ->with('error', 'Er is een fout opgetreden bij het aanmaken van het vergunning type.');
        }
    }

    public function edit(PermitType $permitType)
    {
        $roles = Role::all();
        return view('portal.admin.permits.types.edit', compact('permitType', 'roles'));
    }

    public function update(Request $request, PermitType $permitType)
    {
        \Log::info('Update method called', [
            'request_method' => $request->method(),
            'request_path' => $request->path(),
            'permit_type_id' => $permitType->id,
            'form_data' => $request->all()
        ]);

        try {
            $data = $request->all();
            $data['is_active'] = filter_var($data['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN);

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:permit_types,name,' . $permitType->id,
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

            $permitType->update($validated);

            return redirect()
                ->route('portal.admin.permits.types.index')
                ->with('success', 'Vergunning type succesvol bijgewerkt.');

        } catch (\Exception $e) {
            \Log::error('Error updating permit type', [
                'id' => $permitType->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Er is een fout opgetreden bij het bijwerken van het vergunning type: ' . $e->getMessage()]);
        }
    }

    public function destroy(Request $request, PermitType $permitType)
    {
        \Log::info('Destroy method called', [
            'request_method' => $request->method(),
            'permit_type_id' => $permitType->id
        ]);

        try {
            if ($permitType->requests()->exists()) {
                return back()->with('error', 'Dit vergunning type kan niet worden verwijderd omdat er aanvragen aan gekoppeld zijn.');
            }

            $permitType->delete();

            return redirect()
                ->route('portal.admin.permits.types.index')
                ->with('success', 'Vergunning type succesvol verwijderd.');

        } catch (\Exception $e) {
            \Log::error('Destroy error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
} 