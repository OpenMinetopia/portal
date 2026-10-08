<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Tenancy\AdminClaim;
use Illuminate\Http\Request;

class AdminClaimController extends Controller
{
    public function __invoke(Request $request, string $token)
    {
        if (! AdminClaim::isValid(tenant(), $token)) {
            return response()->view('auth.admin-claim-invalid', [], 410);
        }

        if ($request->user()) {
            AdminClaim::consume(tenant(), $token, $request->user())
                ? session()->flash('success', 'Je bent nu beheerder van dit portaal.')
                : session()->flash('error', 'De beheerderslink is verlopen of al gebruikt. '.AdminClaim::howToGetANewLink());

            return redirect()->route('dashboard');
        }

        // The claim is finished right after logging in or registering.
        $request->session()->put(AdminClaim::SESSION_KEY, $token);

        return redirect()->route(\App\Models\User::exists() ? 'login' : 'register');
    }
}
