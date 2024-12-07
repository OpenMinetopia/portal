<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plot extends Model
{
    protected $fillable = [
        'user_id',
        'name',
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
        return $this->belongsToMany(User::class, 'plot_members')
            ->withTimestamps()
            ->withPivot(['role']);
    }

    // Helper methods
    public function getArea(): int
    {
        if (!isset($this->coordinates['corner1'], $this->coordinates['corner2'])) {
            return 0;
        }

        $length = abs($this->coordinates['corner2']['x'] - $this->coordinates['corner1']['x']) + 1;
        $width = abs($this->coordinates['corner2']['z'] - $this->coordinates['corner1']['z']) + 1;
        return $length * $width;
    }

    public function isOwner(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function isMember(User $user): bool
    {
        return $this->members()->where('users.id', $user->id)->exists();
    }
}
