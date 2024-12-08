<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\DetectionGate;
use App\Models\DetectionLog;
use Illuminate\Http\Request;

class DetectionGateController extends Controller
{
    public function index()
    {
        $gates = DetectionGate::with(['creator'])
            ->latest()
            ->paginate(10);

        return view('portal.police.detection-gates.index', [
            'gates' => $gates,
            'stats' => [
                'total' => DetectionGate::count(),
                'active' => DetectionGate::where('is_active', true)->count(),
                'detections' => DetectionLog::count()
            ]
        ]);
    }

    public function show(DetectionGate $detectionGate)
    {
        return view('portal.police.detection-gates.show', [
            'gate' => $detectionGate->load(['creator', 'detectionLogs' => function($query) {
                $query->latest()->take(10);
            }])
        ]);
    }
} 