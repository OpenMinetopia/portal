<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'minecraft_username' => $validated['minecraft_username'],
            'password' => Hash::make($validated['password']),
            'token' => Str::random(32),
        ]);

        auth()->login($user);

        return redirect()->route('dashboard')->with('minecraft-connect', [
            'token' => $user->token,
            'command' => "/koppel {$user->token}"
        ]);
    }
} 