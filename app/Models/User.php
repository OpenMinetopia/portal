<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'minecraft_username',
        'token',
        'password',
        'minecraft_verified',
        'minecraft_uuid',
        'minecraft_verified_at',
        'level',
        'calculated_level',
        'playtime',
        'default_prefix',
        'prefix_color',
        'level_color',
        'name_color',
        'chat_color',
        'last_login',
        'last_logout',
        'is_online',
        'health_statistic',
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
        'last_login' => 'datetime',
        'last_logout' => 'datetime',
        'is_online' => 'boolean',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->roles->contains('slug', $role);
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

    public function hasGameRole(string $role): bool
    {
        return $this->roles->where('is_game_role', true)->contains('slug', $role);
    }

    public function fitness(): HasOne
    {
        return $this->hasOne(Fitness::class);
    }

    public function bankAccounts():HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function ownedPlots(): HasMany
    {
        return $this->hasMany(Plot::class);
    }

    public function memberPlots(): BelongsToMany
    {
        return $this->belongsToMany(Plot::class, 'member_plots');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function securityItems(): HasMany
    {
        return $this->hasMany(SecurityItem::class);
    }

    public function detectionLogs(): HasMany
    {
        return $this->hasMany(DetectionLog::class);
    }

    public function createdDetectionGates(): HasMany
    {
        return $this->hasMany(DetectionGate::class, 'created_by');
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function prefixes(): HasMany
    {
        return $this->hasMany(Prefix::class);
    }

    public function levelProgress(): HasOne
    {
        return $this->hasOne(LevelProgress::class);
    }

    public function createdTeleporters(): HasMany
    {
        return $this->hasMany(Teleporter::class, 'created_by');
    }

    public function emergencyCalls(): HasMany
    {
        return $this->hasMany(EmergencyCall::class, 'caller_id');
    }

    public function emergencyResponses(): HasMany
    {
        return $this->hasMany(EmergencyCall::class, 'responded_by');
    }

    public function walkieTalkie(): HasOne
    {
        return $this->hasOne(WalkieTalkie::class);
    }

    public function arrests(): HasMany
    {
        return $this->hasMany(Arrest::class, 'arrested_user_id');
    }

    public function issuedArrests(): HasMany
    {
        return $this->hasMany(Arrest::class, 'officer_id');
    }

    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }

    public function issuedFines(): HasMany
    {
        return $this->hasMany(Fine::class, 'officer_id');
    }

    public function isArrested(): bool
    {
        return $this->arrests()
            ->whereNull('released_at')
            ->orWhere('released_at', '>', now())
            ->exists();
    }

    public function getActiveArrest()
    {
        return $this->arrests()
            ->whereNull('released_at')
            ->orWhere('released_at', '>', now())
            ->first();
    }

    public function getTotalUnpaidFines(): float
    {
        return $this->fines()
            ->where('status', 'pending')
            ->sum('amount');
    }

    public function isPoliceOfficer(): bool
    {
        return $this->hasGameRole('police-officer');
    }

    public function calculateLevel(): int
    {
        if (!$this->levelProgress) {
            return $this->level;
        }

        return (int) floor($this->levelProgress->getTotalPoints() / config('minetopia.points_needed_for_level_up'));
    }

    public function updateCalculatedLevel(): void
    {
        $this->calculated_level = $this->calculateLevel();
        $this->save();
    }

    public function getCurrentFitness(): int
    {
        return $this->fitness?->total_fitness ?? 0;
    }

    public function getMaxFitness(): int
    {
        return $this->fitness?->max_fitness ?? 100;
    }

    public function getFitnessPercentage(): float
    {
        if (!$this->fitness || $this->fitness->max_fitness === 0) {
            return 0;
        }

        return ($this->fitness->total_fitness / $this->fitness->max_fitness) * 100;
    }

    public function getBalance(): float
    {
        return $this->bankAccount?->balance ?? 0;
    }

    public function canAfford(float $amount): bool
    {
        return $this->getBalance() >= $amount;
    }

    public function getPlotCount(): int
    {
        return $this->ownedPlots()->count();
    }

    public function hasAccessToPlot(Plot $plot): bool
    {
        return $this->ownedPlots()->where('id', $plot->id)->exists() ||
            $this->memberPlots()->where('id', $plot->id)->exists();
    }

    public function hasSecurityItem(string $type): bool
    {
        return $this->securityItems()->where('type', $type)->exists();
    }

    public function isHandcuffed(): bool
    {
        return $this->securityItems()
            ->where('type', 'handcuffs')
            ->whereNotNull('last_used')
            ->where('cooldown_until', '>', now())
            ->exists();
    }

}
