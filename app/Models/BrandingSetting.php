<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * The portal's look as set by an admin: the accent colour and the logo.
 * Empty values fall back to the OpenMinetopia defaults.
 */
class BrandingSetting extends Model
{
    public const DEFAULT_ACCENT = '#3f6b30';


    // How far each shade is mixed towards white (positive) or black (negative); 600 is the chosen colour.
    private const SHADES = [
        50 => 0.92, 100 => 0.84, 200 => 0.68, 300 => 0.5, 400 => 0.3, 500 => 0.14,
        600 => 0, 700 => -0.15, 800 => -0.3, 900 => -0.45, 950 => -0.65,
    ];

    protected $fillable = [
        'accent_color',
        'logo_path',
        'logo_pixelated',
    ];

    protected $casts = [
        'logo_pixelated' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::cacheKey()));
    }

    public static function current(): self
    {
        // Before the migration has run there is nothing to read, and nothing should be cached.
        if (! Cache::has(self::cacheKey()) && ! Schema::hasTable('branding_settings')) {
            return new self();
        }

        return Cache::rememberForever(self::cacheKey(), fn () => self::first() ?? new self());
    }

    // Every tenant of a hosted portal has its own look.
    private static function cacheKey(): string
    {
        return 'branding_settings:'.(tenant()?->getTenantKey() ?? 'central');
    }

    public function hasCustomAccent(): bool
    {
        return $this->accent_color !== null && strcasecmp($this->accent_color, self::DEFAULT_ACCENT) !== 0;
    }

    public function logoUrl(): string
    {
        return $this->logo_path
            ? route('branding.logo', ['v' => $this->updated_at?->timestamp])
            : asset('images/brand/logo.svg');
    }

    public function isLogoPixelated(): bool
    {
        return $this->logo_path ? $this->logo_pixelated : true;
    }

    /**
     * The accent palette as "r g b" channels per shade, for the --accent-* CSS variables.
     *
     * @return array<int, string>
     */
    public function palette(): array
    {
        [$r, $g, $b] = sscanf($this->accent_color ?? self::DEFAULT_ACCENT, '#%02x%02x%02x');

        $palette = [];
        foreach (self::SHADES as $shade => $mix) {
            $target = $mix > 0 ? 255 : 0;
            $amount = abs($mix);
            $palette[$shade] = implode(' ', array_map(
                fn ($channel) => (int) round($channel + ($target - $channel) * $amount),
                [$r, $g, $b],
            ));
        }

        return $palette;
    }
}
