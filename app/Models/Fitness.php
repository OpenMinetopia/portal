<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fitness extends Model
{
    protected $table = 'fitness';

    protected $fillable = [
        'user_id',
        'total_fitness',
        'max_fitness',
        'current_status',
        'statistics'
    ];

    protected $casts = [
        'statistics' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatistic(string $name, int $default = 0): int
    {
        return $this->statistics[$name] ?? $default;
    }
}
