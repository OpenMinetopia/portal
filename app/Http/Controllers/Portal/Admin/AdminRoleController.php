<?php

namespace App\Http\Controllers\Portal\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class AdminRoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')
            ->latest()
            ->paginate(10);

        // Determine layout version
        $layout = request()->get('layout', 'v2'); // Default to v2, fallback to v1

        if ($layout === 'v1') {
            return view('portal.admin.roles.index', [
                'roles' => $roles,
                'stats' => [
                    'total' => Role::count(),
                    'admin_roles' => Role::where('is_admin', true)->count(),
                    'game_roles' => Role::where('is_game_role', true)->count()
                ]
            ]);
        }

        return view('portal.v2.admin.roles.index', [
            'roles' => $roles,
            'stats' => [
                'total' => Role::count(),
                'admin_roles' => Role::where('is_admin', true)->count(),
                'game_roles' => Role::where('is_game_role', true)->count()
            ]
        ]);
    }

    public function create()
    {
        return view('portal.admin.roles.create', [
            'permissions' => Permission::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'is_admin' => 'boolean',
            'is_game_role' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = Role::create($validated);
        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()
            ->route('portal.admin.roles.index')
            ->with('success', 'Rol succesvol aangemaakt');
    }

    public function edit(Role $role)
    {
        return view('portal.admin.roles.edit', [
            'role' => $role->load('permissions'),
            'permissions' => Permission::all()
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_admin' => 'boolean',
            'is_game_role' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->update($validated);
        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()
            ->route('portal.admin.roles.index')
            ->with('success', 'Rol succesvol bijgewerkt');
    }
} 