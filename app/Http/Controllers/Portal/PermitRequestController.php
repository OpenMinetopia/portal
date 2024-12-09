<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\PermitRequest;
use App\Models\PermitType;
use Illuminate\Http\Request;

class PermitRequestController extends Controller
{
    public function index()
    {
        $requests = PermitRequest::with(['type', 'user'])
            ->when(!auth()->user()->isAdmin(), function ($query) {
                $query->whereHas('type', function ($q) {
                    $q->whereJsonContains('authorized_roles', auth()->user()->roles->pluck('id'));
                });
            })
            ->latest()
            ->paginate(10);

        return view('portal.permits.requests.index', compact('requests'));
    }

    public function create(PermitType $type)
    {
        return view('portal.permits.requests.create', compact('type'));
    }

    public function store(Request $request, PermitType $type)
    {
        $validated = $request->validate([
            'form_data' => 'required|array'
        ]);

        $permitRequest = PermitRequest::create([
            'permit_type_id' => $type->id,
            'user_id' => auth()->id(),
            'form_data' => $validated['form_data']
        ]);

        return redirect()
            ->route('portal.permits.requests.show', $permitRequest)
            ->with('success', 'Vergunning aanvraag succesvol ingediend.');
    }

    public function handle(Request $request, PermitRequest $permitRequest)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,denied',
            'admin_notes' => 'required|string'
        ]);

        $permitRequest->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'],
            'handled_by' => auth()->id(),
            'handled_at' => now()
        ]);

        return redirect()
            ->route('portal.permits.requests.show', $permitRequest)
            ->with('success', 'Vergunning aanvraag succesvol verwerkt.');
    }
} 