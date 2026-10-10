<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CompanyType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'is_active',
        'authorized_roles',
        'form_fields',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'authorized_roles' => 'array',
        'form_fields' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($type) {
            $type->slug = Str::slug($type->name);
        });
    }

    public function companies()
    {
        return $this->hasMany(Company::class, 'type_id');
    }

    public function requests()
    {
        return $this->hasMany(CompanyRequest::class);
    }

    /**
     * Needs manage-companies; a type with roles is limited to those roles (no roles: every manager).
     */
    public function userCanManage(User $user): bool
    {
        if (! $user->can('manage-companies')) {
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