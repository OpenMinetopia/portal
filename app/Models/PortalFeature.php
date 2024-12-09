<?php

namespace App\Models;

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

    public static function isEnabled(string $key): bool
    {
        return static::where('key', $key)->value('is_enabled') ?? false;
    }
} 