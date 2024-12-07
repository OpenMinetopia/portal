<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Arrest;
use App\Models\Fine;
use App\Models\Plot;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $data = [
            'balance' => $user->getCurrentBalance(),
            'fitness' => [
                'current' => $user->getCurrentFitness(),
                'max' => $user->getMaxFitness(),
                'percentage' => $user->getFitnessPercentage(),
            ],
            'plots' => [
                'count' => $user->plots()->count(),
                'residential' => $user->plots()->where('type', 'residential')->count(),
                'commercial' => $user->plots()->where('type', 'commercial')->count(),
            ],
            'vehicles' => $user->vehicles()->count(),
            'active_arrests' => $user->getActiveArrests(),
            'unpaid_fines' => $user->getUnpaidFines(),
            'recent_activity' => $this->getRecentActivity($user),
        ];

        // For police officers, add additional data
        if ($user->isPoliceOfficer()) {
            $data['police'] = [
                'arrests_today' => Arrest::where('officer_uuid', $user->minecraft_uuid)
                    ->whereDate('created_at', today())
                    ->count(),
                'fines_today' => Fine::where('officer_uuid', $user->minecraft_uuid)
                    ->whereDate('created_at', today())
                    ->count(),
                'active_arrests' => Arrest::where('release_time', '>', now())->count(),
                'recent_incidents' => $this->getRecentIncidents(),
            ];
        }

        return view('dashboard', $data);
    }

    private function getRecentActivity($user)
    {
        $activity = collect();

        // Add recent transactions
        if ($user->bankAccount) {
            $activity = $activity->merge(
                $user->bankAccount->transactions()
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(function ($transaction) {
                        return [
                            'type' => 'transaction',
                            'data' => $transaction,
                            'time' => $transaction->created_at,
                        ];
                    })
            );
        }

        // Add recent arrests
        $activity = $activity->merge(
            $user->arrests()
                ->latest()
                ->take(3)
                ->get()
                ->map(function ($arrest) {
                    return [
                        'type' => 'arrest',
                        'data' => $arrest,
                        'time' => $arrest->created_at,
                    ];
                })
        );

        // Add recent fines
        $activity = $activity->merge(
            $user->fines()
                ->latest()
                ->take(3)
                ->get()
                ->map(function ($fine) {
                    return [
                        'type' => 'fine',
                        'data' => $fine,
                        'time' => $fine->created_at,
                    ];
                })
        );

        return $activity->sortByDesc('time')->take(5);
    }

    private function getRecentIncidents()
    {
        return Arrest::with('officer', 'arrestee')
            ->latest()
            ->take(5)
            ->get();
    }
} 