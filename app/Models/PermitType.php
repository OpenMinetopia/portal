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

    public function userCanManage(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->roles->whereIn('id', $this->authorized_roles)->isNotEmpty();
    }
} 