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

    public function userCanManage(User $user): bool
    {
        return $user->isAdmin() || $user->roles->pluck('id')->intersect($this->authorized_roles)->isNotEmpty();
    }
} 