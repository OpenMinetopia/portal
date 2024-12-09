<?php

namespace App\Http\Controllers\Portal\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermitRequest;
use App\Models\PermitType;
use Illuminate\Http\Request;

class PermitRequestManagementController extends Controller
{
    public function index()
    {
        // Get permit requests based on user's role
        $requests = PermitRequest::with(['type', 'user', 'handler'])
            ->when(!auth()->user()->isAdmin(), function ($query) {
                // If not admin, only show requests for permit types they can manage
                $query->whereHas('type', function ($q) {
                    $q->whereJsonContains('authorized_roles', auth()->user()->roles->pluck('id'));
                });
            })
            ->latest()
            ->paginate(10);

        // Get statistics
        $stats = [
            'total' => $requests->total(),
            'pending' => PermitRequest::where('status', 'pending')
                ->when(!auth()->user()->isAdmin(), function ($query) {
                    $query->whereHas('type', function ($q) {
                        $q->whereJsonContains('authorized_roles', auth()->user()->roles->pluck('id'));
                    });
                })->count(),
            'approved' => PermitRequest::where('status', 'approved')
                ->when(!auth()->user()->isAdmin(), function ($query) {
                    $query->whereHas('type', function ($q) {
                        $q->whereJsonContains('authorized_roles', auth()->user()->roles->pluck('id'));
                    });
                })->count(),
            'denied' => PermitRequest::where('status', 'denied')
                ->when(!auth()->user()->isAdmin(), function ($query) {
                    $query->whereHas('type', function ($q) {
                        $q->whereJsonContains('authorized_roles', auth()->user()->roles->pluck('id'));
                    });
                })->count(),
        ];

        return view('portal.admin.permits.requests.index', compact('requests', 'stats'));
    }

    public function show(PermitRequest $permitRequest)
    {
        // Check if user can manage this permit type
        if (!auth()->user()->isAdmin() && !$permitRequest->type->userCanManage(auth()->user())) {
            abort(403, 'Je hebt geen toegang tot deze vergunning aanvraag.');
        }

        return view('portal.admin.permits.requests.show', compact('permitRequest'));
    }

    public function handle(Request $request, PermitRequest $permitRequest)
    {
        // Check if user can manage this permit type
        if (!auth()->user()->isAdmin() && !$permitRequest->type->userCanManage(auth()->user())) {
            abort(403, 'Je hebt geen toegang tot deze vergunning aanvraag.');
        }

        // Validate request
        $validated = $request->validate([
            'status' => 'required|in:approved,denied',
            'admin_notes' => 'required|string|min:10',
        ], [
            'status.required' => 'Selecteer een status.',
            'status.in' => 'Ongeldige status geselecteerd.',
            'admin_notes.required' => 'Vul een toelichting in.',
            'admin_notes.min' => 'De toelichting moet minimaal 10 karakters bevatten.',
        ]);

        try {
            // Update permit request
            $permitRequest->update([
                'status' => $validated['status'],
                'admin_notes' => $validated['admin_notes'],
                'handled_by' => auth()->id(),
                'handled_at' => now(),
            ]);

            // TODO: Send notification to user

            return redirect()
                ->route('portal.admin.permits.requests.show', $permitRequest)
                ->with('success', 'Vergunning aanvraag succesvol verwerkt.');

        } catch (\Exception $e) {
            \Log::error('Error handling permit request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Er is een fout opgetreden bij het verwerken van de aanvraag.']);
        }
    }
} 