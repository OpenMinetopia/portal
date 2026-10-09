<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        Password::sendResetLink($request->only('email'));

        // The same answer whether or not the address has an account, so this form
        // cannot be used to find out who is registered.
        return back()->onlyInput('email')->with('status', 'Hoort dit e-mailadres bij een account? Dan staat er een link in je inbox om een nieuw wachtwoord in te stellen.');
    }
}
