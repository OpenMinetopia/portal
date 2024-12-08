<?php

namespace App\Http\Controllers\Portal\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teleporter;
use Illuminate\Http\Request;

class AdminTeleporterController extends Controller
{
    public function index()
    {
        $teleporters = Teleporter::with(['creator'])
            ->latest()
            ->paginate(10);

        return view('portal.admin.teleporters.index', [
            'teleporters' => $teleporters,
            'stats' => [
                'total' => Teleporter::count(),
                'active' => Teleporter::where('is_active', true)->count()
            ]
        ]);
    }

    public function create()
    {
        return view('portal.admin.teleporters.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|array',
            'location.world' => 'required|string',
            'location.x' => 'required|numeric',
            'location.y' => 'required|numeric',
            'location.z' => 'required|numeric',
            'display_lines' => 'array',
            'display_lines.*' => 'string',
            'is_active' => 'boolean'
        ]);

        $validated['created_by'] = auth()->id();

        Teleporter::create($validated);

        return redirect()
            ->route('portal.admin.teleporters.index')
            ->with('success', 'Teleporter succesvol aangemaakt');
    }

    public function edit(Teleporter $teleporter)
    {
        return view('portal.admin.teleporters.edit', [
            'teleporter' => $teleporter
        ]);
    }

    public function update(Request $request, Teleporter $teleporter)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|array',
            'location.world' => 'required|string',
            'location.x' => 'required|numeric',
            'location.y' => 'required|numeric',
            'location.z' => 'required|numeric',
            'display_lines' => 'array',
            'display_lines.*' => 'string',
            'is_active' => 'boolean'
        ]);

        $teleporter->update($validated);

        return redirect()
            ->route('portal.admin.teleporters.index')
            ->with('success', 'Teleporter succesvol bijgewerkt');
    }
} 