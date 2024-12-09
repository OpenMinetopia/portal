<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\CompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompanyRequestManagementController extends Controller
{
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

                    return redirect()
                        ->route('portal.companies.requests.index')
                        ->with('success', 'De bedrijfs aanvraag is succesvol goedgekeurd en het bedrijf is aangemaakt.');

                } catch (\Exception $e) {
                    return back()
                        ->withInput()
                        ->withErrors(['error' => 'Er is een fout opgetreden bij het aanmaken van het bedrijf. De aanvraag is niet verwerkt.']);
                }
            } else {
                // If denying, just update the request status
                $companyRequest->update([
                    'status' => $validated['status'],
                    'admin_notes' => $validated['admin_notes'],
                    'handled_by' => auth()->id(),
                    'handled_at' => now(),
                ]);

                return redirect()
                    ->route('portal.companies.requests.index')
                    ->with('success', 'De bedrijfs aanvraag is succesvol afgewezen.');
            }

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Er is een fout opgetreden bij het verwerken van de aanvraag.']);
        }
    }
}
