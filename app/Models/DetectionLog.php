<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetectionLog extends Model
{
    protected $fillable = [
        'detection_gate_id',
        'user_id',
        'detected_items',
    ];

    protected $casts = [
        'detected_items' => 'array',
    ];

    public function detectionGate(): BelongsTo
    {
        return $this->belongsTo(DetectionGate::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
