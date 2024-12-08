<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\EmergencyCall;
use Illuminate\Http\Request;

class EmergencyCallController extends Controller
{
    public function index()
    {
        $calls = EmergencyCall::with(['caller', 'responder'])
            ->latest()
            ->paginate(10);

        return view('portal.police.emergency-calls.index', [
            'calls' => $calls,
            'stats' => [
                'total' => EmergencyCall::count(),
                'pending' => EmergencyCall::where('status', 'pending')->count(),
                'responded' => EmergencyCall::where('status', 'responded')->count()
            ]
        ]);
    }

    public function show(EmergencyCall $emergencyCall)
    {
        return view('portal.police.emergency-calls.show', [
            'call' => $emergencyCall->load(['caller', 'responder'])
        ]);
    }
} 