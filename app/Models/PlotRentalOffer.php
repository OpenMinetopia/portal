<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A plot offered for rent at the makelaar. While it is rented the offer is
 * 'rented'; when that rental ends it goes back on the market, unless the
 * landlord gave notice.
 */
class PlotRentalOffer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'plot_name',
        'landlord_id',
        'is_server_listing',
        'payout_bank_account_uuid',
        'price',
        'period',
        'description',
        'image_path',
        'min_x',
        'min_y',
        'min_z',
        'max_x',
        'max_y',
        'max_z',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_server_listing' => 'boolean',
        'min_x' => 'integer',
        'min_y' => 'integer',
        'min_z' => 'integer',
        'max_x' => 'integer',
        'max_y' => 'integer',
        'max_z' => 'integer',
    ];

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(PlotRental::class);
    }

    public function activeRental(): HasOne
    {
        return $this->hasOne(PlotRental::class)->where('status', 'active');
    }

    public function getLandlordNameAttribute(): string
    {
        if ($this->is_server_listing) {
            return config('app.name');
        }

        return $this->landlord->minecraft_username ?: \App\Helpers\MinecraftHelper::getName($this->landlord->minecraft_uuid);
    }

    public function getFormattedPriceAttribute(): string
    {
        return '€ ' . number_format($this->price, 2, ',', '.');
    }

    public function getPeriodTextAttribute(): string
    {
        return PlotRental::periodText($this->period);
    }

    public function getDimensionsAttribute(): array
    {
        return [
            'width' => abs($this->max_x - $this->min_x) + 1,
            'length' => abs($this->max_z - $this->min_z) + 1,
            'height' => abs($this->max_y - $this->min_y) + 1,
        ];
    }

    public function getAreaAttribute(): int
    {
        return $this->dimensions['width'] * $this->dimensions['length'];
    }
}
