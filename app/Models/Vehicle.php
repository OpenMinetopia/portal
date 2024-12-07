<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'model',
        'custom_data',
        'last_used'
    ];

    protected $casts = [
        'custom_data' => 'array',
        'last_used' => 'datetime'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Helper methods
    public function getLicensePlate(): ?string
    {
        return $this->custom_data['license_plate'] ?? null;
    }

    public function getColor(): ?string
    {
        return $this->custom_data['color'] ?? null;
    }

    public function getFuelLevel(): int
    {
        return $this->custom_data['fuel_level'] ?? 100;
    }
}
