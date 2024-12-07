<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LevelProgress extends Model
{
    protected $fillable = [
        'user_id',
        'points_from_plots',
        'points_from_balance',
        'points_from_vehicles',
        'points_from_prefix',
        'points_from_playtime',
        'points_from_fitness',
        'last_calculated',
    ];

    protected $casts = [
        'last_calculated' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalPoints(): int
    {
        return $this->points_from_plots +
            $this->points_from_balance +
            $this->points_from_vehicles +
            $this->points_from_prefix +
            $this->points_from_playtime +
            $this->points_from_fitness;
    }

}
