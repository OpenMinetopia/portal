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

        return view('portal.admin.roles.index', [
            'roles' => $roles,
            'stats' => [
                'total' => Role::count(),
                'admin_roles' => Role::where('is_admin', true)->count()
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
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = Role::create(['is_admin' => $request->boolean('is_admin')] + $validated);
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
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        // An unchecked checkbox is not sent, so read it as a boolean; the admin role always stays admin.
        $role->update(['is_admin' => $role->slug === 'admin' || $request->boolean('is_admin')] + $validated);
        $role->permissions()->sync($validated['permissions'] ?? []);

        return redirect()
            ->route('portal.admin.roles.index')
            ->with('success', 'Rol succesvol bijgewerkt');
    }

    public function destroy(Role $role)
    {
        if ($role->slug === 'admin') {
            return back()->with('error', [
                'title' => 'Actie niet mogelijk',
                'message' => 'De admin rol kan niet worden verwijderd.'
            ]);
        }

        $role->users()->detach();
        $role->permissions()->detach();
        $role->delete();

        return redirect()
            ->route('portal.admin.roles.index')
            ->with('success', 'Rol succesvol verwijderd');
    }
} 