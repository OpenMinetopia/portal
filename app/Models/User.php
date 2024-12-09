<?php

namespace App\Models;

use App\Services\Plugin\PlayerService;
use App\Services\Plugin\BankingService;
use App\Services\Plugin\CriminalRecordService;
use App\Services\Plugin\PlotService;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Collection;

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

        return $data['active_prefix'];
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

    /**
     * Get the user's bank account details.
     *
     * @return array
     */
    public function getBankAccountAttribute(): array
    {
        $service = app(BankingService::class);
        return $service->getBankAccount($this->minecraft_plain_uuid);
    }

    /**
     * Get the user's bank balance.
     *
     * @return float
     */
    public function getBalanceAttribute(): float
    {
        return $this->bank_account['balance'] ?? 0;
    }

    /**
     * Get the user's formatted bank balance.
     *
     * @return string
     */
    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->balance, 2, ',', '.');
    }

    /**
     * Get the user's formatted bank balance with currency.
     *
     * @return string
     */
    public function getFormattedBalanceWithCurrencyAttribute(): string
    {
        return '€ ' . $this->formatted_balance;
    }

    /**
     * Get the user's criminal records.
     *
     * @return array
     */
    public function getCriminalRecordsAttribute(): array
    {
        $service = app(CriminalRecordService::class);
        return $service->getPlayerRecords($this->minecraft_plain_uuid);
    }

    /**
     * Get the user's criminal records count.
     */
    public function getCriminalRecordsCountAttribute(): int
    {
        return count($this->criminal_records);
    }

    /**
     * Get the user's colors.
     *
     * @return array
     */
    public function getColorsAttribute(): array
    {
        $service = app(PlayerService::class);
        return $service->getPlayerColors($this->minecraft_plain_uuid);
    }

    /**
     * Get formatted playtime.
     *
     * @return string
     */
    public function getPlaytimeAttribute(): string
    {
        $service = app(PlayerService::class);
        $data = $service->getPlayerData($this->minecraft_plain_uuid);
        return $service->formatPlaytime($data['playtime_seconds'] ?? 0);
    }

    /**
     * Check if user is online.
     *
     * @return bool
     */
    public function getIsOnlineAttribute(): bool
    {
        $service = app(PlayerService::class);
        $data = $service->getPlayerData($this->minecraft_plain_uuid);
        return $data['is_online'] ?? false;
    }

    /**
     * Get the user's bank accounts.
     *
     * @return array
     */
    public function getBankAccountsAttribute(): array
    {
        $service = app(BankingService::class);
        return $service->getPlayerBankAccounts($this->minecraft_plain_uuid);
    }

    /**
     * Get the user's plots.
     *
     * @return array
     */
    public function getPlotsAttribute(): array
    {
        $service = app(PlotService::class);
        return $service->getPlayerPlots($this->minecraft_plain_uuid);
    }

}
