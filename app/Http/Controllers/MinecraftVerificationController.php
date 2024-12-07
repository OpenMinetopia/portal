<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MinecraftVerificationController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        
        return view('minecraft.verify', [
            'token' => $user->token,
            'minecraft_username' => $user->minecraft_username
        ]);
    }

    public function verify(Request $request)
    {
        $user = auth()->user();

        if ($user->minecraft_verified) {
            return redirect()->route('dashboard');
        }

        // Check if user has been verified through the API
        if (!$user->minecraft_verified) {
            return back()->with('error', 'Je account is nog niet geverifieerd. Gebruik /koppel in de game.');
        }

        return redirect()->route('dashboard')
            ->with('success', 'Je account is succesvol geverifieerd!');
    }
}
