<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_admin',
        'is_default',
        'is_game_role'
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'is_default' => 'boolean',
        'is_game_role' => 'boolean'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasPermission(string $permission): bool
    {
        return $this->is_admin || $this->permissions->contains('slug', $permission);
    }

    /**
     * Whether the user may give this role to someone, or change it: admins always,
     * others only a non-admin role with no permission they lack themselves.
     */
    public function canBeGrantedBy(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return ! $this->is_admin
            && $this->permissions->pluck('slug')->diff($user->permissionSlugs())->isEmpty();
    }
}
