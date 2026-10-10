<?php

namespace App\Models;

use App\Support\Features;
use Illuminate\Database\Eloquent\Model;

class PortalFeature extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'is_enabled'
    ];

    protected $casts = [
        'is_enabled' => 'boolean'
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Features::forget());
        static::deleted(fn () => Features::forget());
    }

    public static function isEnabled(string $key): bool
    {
        return Features::enabled($key);
    }
} 