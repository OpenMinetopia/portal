<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CriminalRecord extends Model
{
    protected $fillable = [
        'player_uuid',
        'officer_uuid',
        'reason',
        'date',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * Get the player that owns the criminal record.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(User::class, 'player_uuid', 'minecraft_plain_uuid');
    }

    /**
     * Get the officer that created the criminal record.
     */
    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_uuid', 'minecraft_plain_uuid');
    }
} 