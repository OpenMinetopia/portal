<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Arrest extends Model
{
    protected $fillable = [
        'arrested_user_id',
        'officer_id',
        'reason',
        'duration',
        'released_at',
    ];

    protected $casts = [
        'released_at' => 'datetime',
    ];

    public function arrestedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'arrested_user_id');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function isActive(): bool
    {
        return !$this->released_at || $this->released_at->isFuture();
    }
}
