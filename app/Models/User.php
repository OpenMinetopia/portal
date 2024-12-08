<?php

namespace App\Models;

use App\Services\Plugin\PlayerService;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use  Notifiable;

    protected $fillable = [
        'name',
        'email',
        'minecraft_username',
        'token',
        'password',
        'minecraft_verified',
        'minecraft_uuid',
        'minecraft_plain_uuid',
        'minecraft_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'minecraft_verified_at' => 'datetime',
        'minecraft_verified' => 'boolean',
        'password' => 'hashed',
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

    public function hasRole($role)
    {
        return $this->roles->contains('name', $role);
    }

    /**
     * Get the player's level.
     *
     * @return int
     */
    public function getLevelAttribute(): int
    {
        $service = app(PlayerService::class);
        $data = $service->getPlayerData($this->minecraft_plain_uuid);

        return $data['level'];
    }

    /**
     * Get the player's fitness.
     *
     * @return int
     */
    public function getFitnessAttribute(): int
    {
        $service = app(PlayerService::class);
        $data = $service->getPlayerData($this->minecraft_plain_uuid);

        return $data['fitness'];
    }

    /**
     * Get the player's prefix.
     *
     * @return string
     */
    public function getPrefixAttribute(): string
    {
        $service = app(PlayerService::class);
        $data = $service->getPlayerData($this->minecraft_plain_uuid);

        return $data['prefix'];
    }

    /**
     * Get available prefixes.
     *
     * @return array
     */
    public function getAvailablePrefixesAttribute(): array
    {
        $service = app(PlayerService::class);
        return $service->getPlayerPrefixes($this->minecraft_plain_uuid);
    }

}
