<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plot extends Model
{
    protected $fillable = [
        'user_id',
        'description',
        'world',
        'coordinates',
        'flags'
    ];

    protected $casts = [
        'coordinates' => 'array',
        'flags' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'plot_members');
    }

}
