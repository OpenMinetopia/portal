<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Arrest;
use Illuminate\Http\Request;

class ArrestController extends Controller
{
    public function index()
    {
        $arrests = Arrest::with(['arrestedUser', 'officer'])
            ->latest()
            ->paginate(10);

        return view('portal.police.arrests.index', [
            'arrests' => $arrests,
            'stats' => [
                'total' => Arrest::count(),
                'active' => Arrest::whereNull('released_at')
                    ->orWhere('released_at', '>', now())
                    ->count(),
                'recent' => Arrest::where('created_at', '>', now()->subDay())->count()
            ]
        ]);
    }

    public function show(Arrest $arrest)
    {
        return view('portal.police.arrests.show', [
            'arrest' => $arrest->load(['arrestedUser', 'officer'])
        ]);
    }
} 