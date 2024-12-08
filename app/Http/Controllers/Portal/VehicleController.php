<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $vehicles = $user->vehicles()
            ->latest('last_used')
            ->paginate(10);

        return view('portal.vehicles.index', [
            'vehicles' => $vehicles,
            'stats' => [
                'total' => $user->vehicles()->count(),
                'types' => $user->vehicles()
                    ->select('type')
                    ->distinct()
                    ->count(),
                'lastUsed' => $user->vehicles()
                    ->latest('last_used')
                    ->first()?->last_used
            ]
        ]);
    }

    public function show(Vehicle $vehicle)
    {
        $this->authorize('view', $vehicle);

        return view('portal.vehicles.show', [
            'vehicle' => $vehicle
        ]);
    }
} 