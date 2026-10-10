<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A renter's contract for a plot. Paid up to paid_until; renewed automatically
 * by plots:process-rentals. See App\Services\PlotRentalService.
 */
class PlotRental extends Model
{
    public const GRACE_DAYS = 7;

    protected $fillable = [
        'plot_rental_offer_id',
        'plot_name',
        'landlord_id',
        'renter_id',
        'is_server_listing',
        'payout_bank_account_uuid',
        'renter_bank_account_uuid',
        'price',
        'period',
        'status',
        'started_at',
        'paid_until',
        'ends_at',
        'cancelled_by',
        'overdue_since',
        'grace_until',
        'ended_at',
        'end_reason',
        'access_revoked_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_server_listing' => 'boolean',
        'started_at' => 'datetime',
        'paid_until' => 'datetime',
        'ends_at' => 'datetime',
        'overdue_since' => 'datetime',
        'grace_until' => 'datetime',
        'ended_at' => 'datetime',
        'access_revoked_at' => 'datetime',
    ];

    public static function periodText(string $period): string
    {
        return $period === 'week' ? 'per week' : 'per maand';
    }

    /** The end of a period that starts at $start. */
    public static function periodEnd(string $period, CarbonInterface $start): CarbonInterface
    {
        return $period === 'week' ? $start->copy()->addWeek() : $start->copy()->addMonthNoOverflow();
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(PlotRentalOffer::class, 'plot_rental_offer_id')->withTrashed();
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function renter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'renter_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PlotRentalPayment::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Who looks after the server's rentals: admins and roles with manage-broker. A
     * running rental outlives the makelaar feature, so this ignores whether it is on.
     */
    public static function managesServerRentals(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('manage-broker');
    }

    /** Rentals this user may manage as landlord: their own, and every server rental for a manager. */
    public function scopeLandlordView(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $query) use ($user) {
            $query->where('landlord_id', $user->id);

            if (self::managesServerRentals($user)) {
                $query->orWhere('is_server_listing', true);
            }
        });
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isOverdue(): bool
    {
        return $this->isActive() && $this->overdue_since !== null;
    }

    public function isCancelled(): bool
    {
        return $this->isActive() && $this->ends_at !== null;
    }

    public function isRenter(User $user): bool
    {
        return $this->renter_id === $user->id;
    }

    public function isLandlord(User $user): bool
    {
        return $this->landlord_id === $user->id || ($this->is_server_listing && self::managesServerRentals($user));
    }

    public function getLandlordNameAttribute(): string
    {
        if ($this->is_server_listing) {
            return config('app.name');
        }

        return $this->landlord->minecraft_username ?: \App\Helpers\MinecraftHelper::getName($this->landlord->minecraft_uuid);
    }

    public function getRenterNameAttribute(): string
    {
        return $this->renter->minecraft_username ?: \App\Helpers\MinecraftHelper::getName($this->renter->minecraft_uuid);
    }

    public function getFormattedPriceAttribute(): string
    {
        return '€ ' . number_format($this->price, 2, ',', '.');
    }

    public function getPeriodTextAttribute(): string
    {
        return self::periodText($this->period);
    }

    public function getStatusTextAttribute(): string
    {
        return match (true) {
            ! $this->isActive() => match ($this->end_reason) {
                'unpaid' => 'Beëindigd wegens niet betalen',
                'renter' => 'Opgezegd door huurder',
                'landlord' => 'Opgezegd door verhuurder',
                default => 'Beëindigd',
            },
            $this->isOverdue() => 'Betaling achterstallig',
            $this->isCancelled() => 'Opgezegd',
            default => 'Actief',
        };
    }
}
