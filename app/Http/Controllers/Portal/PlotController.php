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
                'area' => $user->plots()->get()->sum(function($plot) {
                    return $plot->getArea();
                })
            ]
        ]);
    }

    public function show(Plot $plot)
    {
        $this->authorize('view', $plot);

        return view('portal.plots.show', [
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
