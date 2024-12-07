<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MinecraftVerificationController extends Controller
{
    public function show()
    {
        return view('minecraft.verify', [
            'token' => auth()->user()->token,
            'minecraft_username' => auth()->user()->minecraft_username
        ]);
    }

    public function verify(): \Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();

        if ($user->minecraft_verified) {
            return redirect()->route('dashboard');
        }

        return back()->with('error', 'Minecraft account verification failed. Please try again.');
    }
}
