<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use Illuminate\Http\Request;

class FineController extends Controller
{
    public function index()
    {
        $fines = Fine::with(['user', 'officer'])
            ->latest()
            ->paginate(10);

        return view('portal.police.fines.index', [
            'fines' => $fines,
            'stats' => [
                'total' => Fine::count(),
                'unpaid' => Fine::where('status', '!=', 'paid')->count(),
                'totalAmount' => Fine::sum('amount')
            ]
        ]);
    }

    public function show(Fine $fine)
    {
        return view('portal.police.fines.show', [
            'fine' => $fine->load(['user', 'officer'])
        ]);
    }
} 