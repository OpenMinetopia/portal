<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Check if user wants V2 layout
        $layout = $request->get('layout', 'v2'); // Default to V2
        
        if ($layout === 'v1') {
            return view('portal.dashboard');
        }
        
        // Get recent activity for V2
        $recentActivity = $this->getRecentActivity($user);
        
        return view('portal.v2.dashboard', compact('recentActivity'));
    }

    private function getRecentActivity($user)
    {
        $activity = collect();

        // Add portal login activity
        $activity->push([
            'type' => 'login',
            'title' => 'Portal Login',
            'description' => 'Je bent ingelogd in het portaal',
            'time' => now()->subMinutes(5),
            'icon' => '🔐'
        ]);

        // Add bank account view activity
        if (count($user->bank_accounts) > 0) {
            $activity->push([
                'type' => 'bank',
                'title' => 'Bankrekeningen Bekeken',
                'description' => 'Je hebt je bankrekeningen bekeken',
                'time' => now()->subMinutes(15),
                'icon' => '🏦'
            ]);
        }

        // Add dashboard activity
        $activity->push([
            'type' => 'dashboard',
            'title' => 'Dashboard Bekeken',
            'description' => 'Je hebt het dashboard bekeken',
            'time' => now()->subMinutes(30),
            'icon' => '📊'
        ]);

        return $activity->sortByDesc('time')->take(5);
    }
}
