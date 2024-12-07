<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalkieTalkie extends Model
{
    protected $fillable = [
        'user_id',
        'channel',
        'last_used',
        'emergency_cooldown_until',
    ];

    protected $casts = [
        'last_used' => 'datetime',
        'emergency_cooldown_until' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOnEmergencyCooldown(): bool
    {
        return $this->emergency_cooldown_until && $this->emergency_cooldown_until->isFuture();
    }

}
