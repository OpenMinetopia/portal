<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $vehicles = $user->vehicles()
            ->latest()
            ->paginate(10);

        return view('dashboard.vehicles.index', [
            'vehicles' => $vehicles,
            'vehicleStats' => [
                'total' => $user->vehicles()->count(),
                'cars' => $user->vehicles()->where('type', 'car')->count(),
                'motorcycles' => $user->vehicles()->where('type', 'motorcycle')->count()
            ]
        ]);
    }

    public function show(Vehicle $vehicle)
    {
        $this->authorize('view', $vehicle);
        
        return view('dashboard.vehicles.show', [
            'vehicle' => $vehicle->load(['owner'])
        ]);
    }

    public function edit(Vehicle $vehicle)
    {
        $this->authorize('update', $vehicle);

        return view('dashboard.vehicles.edit', [
            'vehicle' => $vehicle
        ]);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $this->authorize('update', $vehicle);

        $validated = $request->validate([
            'properties' => 'array',
            'properties.color' => 'nullable|string',
            'properties.license_plate' => 'nullable|string'
        ]);

        $vehicle->update($validated);

        return redirect()
            ->route('dashboard.vehicles.show', $vehicle)
            ->with('success', 'Vehicle updated successfully');
    }
} 