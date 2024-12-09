<?php

namespace App\Http\Controllers\Portal\Admin;

use App\Http\Controllers\Controller;
use App\Models\DissolutionRequest;
use Illuminate\Http\Request;

class DissolutionRequestManagementController extends Controller
{

    public function index()
    {
        $requests = DissolutionRequest::with(['company', 'user'])
            ->latest()
            ->paginate(10);

        return view('portal.admin.companies.dissolutions.index', compact('requests'));
    }

    public function show(DissolutionRequest $dissolutionRequest)
    {
        return view('portal.admin.companies.dissolutions.show', compact('dissolutionRequest'));
    }

    public function handle(Request $request, DissolutionRequest $dissolutionRequest)
    {
        // Validate request
        $validated = $request->validate([
            'status' => 'required|in:approved,denied',
            'admin_notes' => 'required|string|max:1000',
        ]);

        try {
            // Update request status
            $dissolutionRequest->update([
                'status' => $validated['status'],
                'admin_notes' => $validated['admin_notes'],
                'handled_by' => auth()->id(),
                'handled_at' => now(),
            ]);

            // If approved, deactivate the company
            if ($validated['status'] === 'approved') {
                $dissolutionRequest->company->update([
                    'is_active' => false
                ]);
            }

            return redirect()
                ->route('portal.companies.dissolutions.index')
                ->with('success', 'De opheffings aanvraag is succesvol ' .
                    ($validated['status'] === 'approved' ? 'goedgekeurd' : 'afgewezen') . '.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Er is een fout opgetreden bij het verwerken van de aanvraag.']);
        }
    }
}
