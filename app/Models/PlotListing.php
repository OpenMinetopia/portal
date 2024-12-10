<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlotListing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'plot_name',
        'seller_id',
        'payout_bank_account_uuid',
        'buyer_bank_account_uuid',
        'price',
        'description',
        'image_path',
        'status',
        'min_x',
        'min_y',
        'min_z',
        'max_x',
        'max_y',
        'max_z',
        'instant_buy'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'instant_buy' => 'boolean',
        'min_x' => 'integer',
        'min_y' => 'integer',
        'min_z' => 'integer',
        'max_x' => 'integer',
        'max_y' => 'integer',
        'max_z' => 'integer'
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function getFormattedPriceAttribute(): string
    {
        return '€ ' . number_format($this->price, 2, ',', '.');
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Te koop',
            'sold' => 'Verkocht',
            'cancelled' => 'Ingetrokken',
            default => 'Onbekend'
        };
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
        $dimensions = $this->dimensions;
        return $dimensions['width'] * $dimensions['length'];
    }

    public function getVolumeAttribute(): int
    {
        $dimensions = $this->dimensions;
        return $dimensions['width'] * $dimensions['length'] * $dimensions['height'];
    }

    public function getRouteKeyName()
    {
        return 'id';
    }
}
