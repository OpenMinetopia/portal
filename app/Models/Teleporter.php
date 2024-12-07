<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teleporter extends Model
{
    protected $fillable = [
        'name',
        'location',
        'display_lines',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'location' => 'array',
        'display_lines' => 'array',
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
