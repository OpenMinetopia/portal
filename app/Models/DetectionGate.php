<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetectionGate extends Model
{
    protected $fillable = [
        'created_by',
        'location',
        'flagged_materials',
        'is_active',
        'last_triggered'
    ];

    protected $casts = [
        'location' => 'array',
        'flagged_materials' => 'array',
        'is_active' => 'boolean',
        'last_triggered' => 'datetime'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function detectionLogs(): HasMany
    {
        return $this->hasMany(DetectionLog::class);
    }
}
