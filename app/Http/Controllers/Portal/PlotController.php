<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Services\Plugin\PlotService;

class PlotController extends Controller
{
    public function index()
    {
        $plots = auth()->user()->plots;

        return view('portal.plots.index', [
            'plots' => $plots
        ]);
    }

    public function show(string $name)
    {
        $plots = auth()->user()->plots;
        $plot = collect($plots)->firstWhere('name', $name);

        if (!$plot) {
            abort(404);
        }

        return view('portal.plots.show', [
            'plot' => $plot
        ]);
    }
} 