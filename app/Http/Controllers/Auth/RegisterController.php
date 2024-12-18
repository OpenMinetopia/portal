<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Services\MojangApiService;
use App\Models\Role;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'minecraft_username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $mojangApi = app(MojangApiService::class);
        $playerData = $mojangApi->getPlayerData($validated['minecraft_username']);

        if (!$playerData) {
            return back()->withErrors([
                'minecraft_username' => 'Could not verify Minecraft account.'
            ])->withInput();
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'minecraft_username' => $playerData['name'],
            'minecraft_uuid' => $playerData['uuid'],
            'minecraft_plain_uuid' => $mojangApi->formatUuid($playerData['uuid']),
            'password' => Hash::make($validated['password']),
            'token' => Str::random(32),
        ]);

        // Check if this is the first user
        if (User::count() === 1) {
            // Assign admin role (assuming role with ID 1 is admin)
            $adminRole = Role::where('is_admin', true)->first();
            if ($adminRole) {
                $user->roles()->attach($adminRole->id);
            }
        } else {
            // Assign default player role for subsequent users
            $playerRole = Role::where('slug', 'player')->first();
            if ($playerRole) {
                $user->roles()->attach($playerRole);
            }
        }

        auth()->login($user);

        return redirect()->route('dashboard');
    }
}
