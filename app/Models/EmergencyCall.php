<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergencyCall extends Model
{
    protected $fillable = [
        'caller_id',
        'responded_by',
        'location',
        'message',
        'status',
    ];

    protected $casts = [
        'location' => 'array',
    ];

    public function caller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caller_id');
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

}
