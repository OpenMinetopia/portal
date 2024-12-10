<?php

namespace App\Http\Controllers\Portal\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Services\Plugin\PlayerService;

class AdminUserController extends Controller
{
    protected PlayerService $playerService;

    public function __construct(PlayerService $playerService)
    {
        $this->playerService = $playerService;
    }

    public function index()
    {
        $users = User::with(['roles'])
            ->latest()
            ->paginate(10);

        return view('portal.admin.users.index', [
            'users' => $users,
            'stats' => [
                'total' => User::count(),
                'verified' => User::where('minecraft_verified', true)->count()]
        ]);
    }

    public function show(User $user)
    {
        return view('portal.admin.users.show', [
            'user' => $user->load(['roles']),
            'roles' => Role::all()
        ]);
    }

    public function edit(User $user)
    {
        return view('portal.admin.users.edit', [
            'user' => $user->load(['roles']),
            'roles' => Role::all()
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'minecraft_username' => 'required|string|max:255',
            'level' => 'nullable|integer',
            'default_prefix' => 'nullable|string|max:255',
            'prefix_color' => 'nullable|string|max:255',
            'level_color' => 'nullable|string|max:255',
            'name_color' => 'nullable|string|max:255',
            'chat_color' => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        return redirect()
            ->route('portal.admin.users.show', $user)
            ->with('success', 'Gebruiker succesvol bijgewerkt');
    }

    /**
     * Update the user's roles.
     */
    public function updateRoles(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,id'
        ]);

        $user->roles()->sync($validated['roles'] ?? []);

        return back()->with('success', 'Rollen succesvol bijgewerkt');
    }
}
