<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\WalkieTalkie;
use Illuminate\Http\Request;

class WalkieTalkieController extends Controller
{
    public function index()
    {
        $walkieTalkies = WalkieTalkie::with(['user'])
            ->latest('last_used')
            ->paginate(10);

        return view('portal.police.walkie-talkies.index', [
            'walkieTalkies' => $walkieTalkies,
            'stats' => [
                'total' => WalkieTalkie::count(),
                'active' => WalkieTalkie::whereNotNull('last_used')
                    ->where('last_used', '>', now()->subMinutes(5))
                    ->count(),
                'emergency_cooldown' => WalkieTalkie::where('emergency_cooldown_until', '>', now())->count()
            ]
        ]);
    }

    public function show(WalkieTalkie $walkieTalkie)
    {
        return view('portal.police.walkie-talkies.show', [
            'walkieTalkie' => $walkieTalkie->load('user')
        ]);
    }
} 