<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityItem extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'uses_remaining',
        'effects',
        'last_used',
        'cooldown_until'
    ];

    protected $casts = [
        'effects' => 'array',
        'last_used' => 'datetime',
        'cooldown_until' => 'datetime'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isOnCooldown(): bool
    {
        return $this->cooldown_until && $this->cooldown_until->isFuture();
    }

    public function hasUsesRemaining(): bool
    {
        return !$this->uses_remaining || $this->uses_remaining > 0;
    }

}
