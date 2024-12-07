<?php

namespace App\Http\Controllers\Dashboard;

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

        return view('dashboard.plots.index', [
            'plots' => $plots,
            'plotsCount' => [
                'total' => $user->plots()->count(),
                'residential' => $user->plots()->where('type', 'residential')->count(),
                'commercial' => $user->plots()->where('type', 'commercial')->count()
            ]
        ]);
    }

    public function show(Plot $plot)
    {
        $this->authorize('view', $plot);
        
        return view('dashboard.plots.show', [
            'plot' => $plot->load(['members', 'owner'])
        ]);
    }

    public function edit(Plot $plot)
    {
        $this->authorize('update', $plot);

        return view('dashboard.plots.edit', [
            'plot' => $plot->load(['members'])
        ]);
    }

    public function update(Request $request, Plot $plot)
    {
        $this->authorize('update', $plot);

        $validated = $request->validate([
            'description' => 'nullable|string|max:500',
            'flags' => 'array',
            'flags.*' => 'string'
        ]);

        $plot->update($validated);

        return redirect()
            ->route('dashboard.plots.show', $plot)
            ->with('success', 'Plot updated successfully');
    }
} 