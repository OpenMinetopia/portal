<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function create(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                // Log out everywhere else: whoever knew the old password is out too.
                if (config('session.driver') === 'database') {
                    DB::connection(config('session.connection'))
                        ->table(config('session.table'))
                        ->where('user_id', $user->id)
                        ->delete();
                }

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors([
                'email' => 'Deze link is ongeldig of verlopen. Vraag een nieuwe aan.',
            ])->onlyInput('email');
        }

        return redirect()->route('login')->with('status', 'Je wachtwoord is gewijzigd. Log in met je nieuwe wachtwoord.');
    }
}
