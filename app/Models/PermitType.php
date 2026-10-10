<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermitType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'is_active',
        'form_fields',
        'authorized_roles'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'form_fields' => 'array',
        'authorized_roles' => 'array'
    ];

    public function requests(): HasMany
    {
        return $this->hasMany(PermitRequest::class);
    }

    /**
     * Needs manage-permits; a type with roles is limited to those roles (no roles: every manager).
     */
    public function userCanManage(User $user): bool
    {
        if (! $user->can('manage-permits')) {
            return false;
        }

        $roles = array_map('intval', $this->authorized_roles ?? []);

        return $user->isAdmin() || $roles === [] || $user->roles->pluck('id')->intersect($roles)->isNotEmpty();
    }

    /** Ids of the types the user may handle requests for. */
    public static function manageableIdsFor(User $user): array
    {
        return static::all()->filter->userCanManage($user)->pluck('id')->all();
    }
} 