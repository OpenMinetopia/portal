<?php

namespace App\Http\Controllers\Demo;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Portal;
use Database\Seeders\DemoSeeder;
use Illuminate\Http\Request;

/**
 * Demo mode only: logs in as the demo admin, optionally switches the theme and
 * goes to a page. Used for screenshots: /demo?theme=dark&naar=/portal/plots
 */
class DemoLoginController extends Controller
{
    public function __invoke(Request $request)
    {
        abort_unless(Portal::demo(), 404);

        $user = User::where('email', DemoSeeder::EMAIL)->first();
        abort_if($user === null, 404, 'Laad eerst de demodata: php artisan db:seed --class=DemoSeeder');

        auth()->login($user);
        $request->session()->regenerate();

        $target = (string) $request->query('naar', '/');
        $target = str_starts_with($target, '/') && ! str_starts_with($target, '//') ? $target : '/';
        $theme = $request->query('theme') === 'dark' ? 'dark' : 'light';

        return response()->view('demo.redirect', compact('target', 'theme'));
    }
}
