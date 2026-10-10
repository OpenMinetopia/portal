<?php

namespace App\Http\Controllers\Portal\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Support\Permissions;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Anyone with manage-roles may manage roles; only admins touch admin roles or
 * hand out permissions they do not have themselves.
 */
class AdminRoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')
            ->with('permissions')
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
        return view('portal.admin.roles.create', $this->formData());
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request, [
            'slug' => 'required|string|max:255|unique:roles',
        ]);

        $role = Role::create($validated);
        $role->permissions()->sync($request->input('permissions', []));

        return redirect()
            ->route('portal.admin.roles.index')
            ->with('success', 'Rol succesvol aangemaakt');
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        $this->ensureCanChange($role);

        return view('portal.admin.roles.edit', ['role' => $role] + $this->formData());
    }

    public function update(Request $request, Role $role)
    {
        $role->load('permissions');
        $this->ensureCanChange($role);

        $validated = $this->validated($request, [], $role);

        // The admin role always stays admin, so nobody locks everyone out.
        if ($role->slug === 'admin') {
            $validated['is_admin'] = true;
        }

        $role->update($validated);

        // Permissions the user may not hand out keep whatever they were.
        $grantable = Permission::whereIn('slug', $this->grantableSlugs())->pluck('id');
        $kept = $role->permissions->pluck('id')->diff($grantable);
        $role->permissions()->sync($kept->merge(collect($request->input('permissions', []))->map(fn ($id) => (int) $id)->intersect($grantable))->unique()->values());

        return redirect()
            ->route('portal.admin.roles.index')
            ->with('success', 'Rol succesvol bijgewerkt');
    }

    public function destroy(Role $role)
    {
        $role->load('permissions');

        if ($role->slug === 'admin') {
            return back()->with('error', [
                'title' => 'Actie niet mogelijk',
                'message' => 'De admin rol kan niet worden verwijderd.'
            ]);
        }

        $this->ensureCanChange($role);

        $role->users()->detach();
        $role->permissions()->detach();
        $role->delete();

        return redirect()
            ->route('portal.admin.roles.index')
            ->with('success', 'Rol succesvol verwijderd');
    }

    private function formData(): array
    {
        return [
            'permissions' => Permission::whereIn('slug', array_keys(Permissions::ALL))->get()
                ->sortBy(fn (Permission $permission) => array_search($permission->slug, array_keys(Permissions::ALL)))
                ->groupBy('group'),
            'grantable' => $this->grantableSlugs(),
        ];
    }

    private function validated(Request $request, array $rules, ?Role $role = null): array
    {
        $validated = $request->validate($rules + [
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role)],
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => [Rule::in(Permission::whereIn('slug', $this->grantableSlugs())->pluck('id')->all())],
        ], [
            'permissions.*.in' => 'Je kunt alleen permissies geven die je zelf hebt.',
        ]);

        unset($validated['permissions']);

        // An unchecked checkbox is not sent, so read them as booleans.
        $validated['is_default'] = $request->boolean('is_default');
        $validated['is_admin'] = auth()->user()->isAdmin()
            ? $request->boolean('is_admin')
            : ($role?->is_admin ?? false);

        return $validated;
    }

    private function grantableSlugs(): array
    {
        return auth()->user()->permissionSlugs();
    }

    private function ensureCanChange(Role $role): void
    {
        abort_unless($role->canBeGrantedBy(auth()->user()), 403, 'Je kunt alleen rollen beheren met permissies die je zelf hebt.');
    }
}
