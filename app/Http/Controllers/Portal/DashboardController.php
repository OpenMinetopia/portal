<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        return view('portal.dashboard', [
            'stats' => [
                'balance' => $user->getCurrentBalance(),
                'plots' => [
                    'total' => $user->plots()->count(),
                    'area' => $user->plots()->get()->sum(function($plot) {
                        return $plot->getArea();
                    })
                ],
                'vehicles' => $user->vehicles()->count() ?? 0,
                'level' => [
                    'current' => $user->level ?? 1,
                    'progress' => $user->level_progress ?? 0,
                    'next' => ($user->level ?? 1) + 1
                ],
                'fitness' => [
                    'current' => $user->getCurrentFitness(),
                    'max' => $user->getMaxFitness(),
                    'percentage' => $user->getFitnessPercentage()
                ]
            ],
            'recentActivity' => $this->getRecentActivity($user)
        ]);
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
                            'title' => $transaction->isDeposit() ? 'Payment Received' : 'Payment Sent',
                            'description' => $transaction->description,
                            'amount' => $transaction->getFormattedAmount(),
                            'time' => $transaction->created_at
                        ];
                    })
            );
        }

        // Add recent plot activities
        $activity = $activity->merge(
            $user->plots()
                ->latest()
                ->take(3)
                ->get()
                ->map(function ($plot) {
                    return [
                        'type' => 'plot',
                        'title' => 'Plot Updated',
                        'description' => "Plot {$plot->name} was updated",
                        'time' => $plot->updated_at
                    ];
                })
        );

        return $activity->sortByDesc('time')->take(5);
    }
} 