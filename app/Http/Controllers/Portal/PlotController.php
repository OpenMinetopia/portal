<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Plot;
use Illuminate\Http\Request;

class PlotController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $plots = $user->plots()
            ->with(['members'])
            ->latest()
            ->paginate(10);

        return view('portal.plots.index', [
            'plots' => $plots,
            'stats' => [
                'total' => $user->plots()->count(),
                'residential' => $user->plots()->where('type', 'residential')->count(),
                'commercial' => $user->plots()->where('type', 'commercial')->count()
            ]
        ]);
    }

    public function show(Plot $plot)
    {
        $this->authorize('view', $plot);
        
        return view('portal.plots.show', [
            'plot' => $plot->load(['members', 'owner']),
            'recentActivity' => $plot->activities()->latest()->take(5)->get()
        ]);
    }
} 