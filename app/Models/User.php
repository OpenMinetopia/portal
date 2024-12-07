<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use  Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'minecraft_uuid',
        'minecraft_username',
        'is_verified',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_verified' => 'boolean',
        'last_login' => 'datetime',
    ];

    // Relationships
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
    public function isAdmin(): bool
    {
        return $this->roles->contains('is_admin', true);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->roles->contains(function ($role) use ($permission) {
            return $role->hasPermission($permission);
        });
    }

    public function arrests()
    {
        return $this->hasMany(Arrest::class, 'minecraft_uuid', 'minecraft_uuid');
    }

    public function fines()
    {
        return $this->hasMany(Fine::class, 'minecraft_uuid', 'minecraft_uuid');
    }

    public function plots()
    {
        return $this->hasMany(Plot::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function bankAccount()
    {
        return $this->hasOne(BankAccount::class);
    }

    public function fitness()
    {
        return $this->hasOne(Fitness::class);
    }

    public function plotMemberships()
    {
        return $this->belongsToMany(Plot::class, 'plot_members')
            ->withTimestamps()
            ->withPivot(['role']);
    }

    // Helper methods
    public function hasRole($role)
    {
        return $this->roles->contains('name', $role);
    }

    public function isPoliceOfficer()
    {
        return $this->hasRole('police_officer');
    }

    public function getCurrentBalance()
    {
        return $this->bankAccount?->balance ?? 0;
    }

    public function getCurrentFitness()
    {
        return $this->fitness?->total_fitness ?? 100;
    }

    public function getMaxFitness()
    {
        return $this->fitness?->max_fitness ?? 100;
    }

    public function getFitnessPercentage()
    {
        $max = $this->getMaxFitness();
        if ($max === 0) return 0;
        return round(($this->getCurrentFitness() / $max) * 100);
    }

    public function getActiveArrests()
    {
        return $this->arrests()->where('release_time', '>', now())->get();
    }

    public function getUnpaidFines()
    {
        return $this->fines()->where('paid', false)->get();
    }
}
