<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * The portal's look as set by an admin: the theme (accent, neutrals, fonts, corners) and the logo.
 * Empty values fall back to the OpenMinetopia defaults.
 */
class BrandingSetting extends Model
{
    public const DEFAULT_ACCENT = '#3f6b30';

    public const DEFAULT_NEUTRALS = 'warm';

    public const DEFAULT_FONT = 'schibsted';

    public const DEFAULT_RADIUS = 'default';

    // Hand-picked grays from 50 to 950. They carry dark mode too, so every set has to stay readable on 800 and 900.
    public const NEUTRALS = [
        'warm' => ['name' => 'Warm', 'shades' => ['#f4f4f1', '#f0f0ec', '#e3e3de', '#cfcfc8', '#9a9a93', '#6b6b65', '#55554f', '#2d2d2b', '#1c1c1b', '#131313', '#0b0b0a']],
        'cool' => ['name' => 'Koel', 'shades' => ['#f8fafc', '#f1f5f9', '#e2e8f0', '#cbd5e1', '#94a3b8', '#64748b', '#475569', '#334155', '#1e293b', '#0f172a', '#020617']],
        'neutral' => ['name' => 'Neutraal', 'shades' => ['#fafafa', '#f4f4f5', '#e4e4e7', '#d4d4d8', '#a1a1aa', '#71717a', '#52525b', '#3f3f46', '#27272a', '#18181b', '#09090b']],
        'moss' => ['name' => 'Mos', 'shades' => ['#f3f5f1', '#eef1eb', '#e0e5db', '#cbd2c4', '#969f8e', '#687160', '#525a4b', '#2b3128', '#1b1f19', '#121511', '#0a0c09']],
    ];

    // Only fonts from this list, so a theme can never load a stylesheet from anywhere else.
    // A font without a query is served from public/fonts (see app.css), the rest from Google Fonts.
    public const FONTS = [
        'schibsted' => ['name' => 'Schibsted Grotesk', 'query' => 'Schibsted+Grotesk:wght@400..800'],
        'inter' => ['name' => 'Inter', 'query' => 'Inter:wght@400..800'],
        'nunito' => ['name' => 'Nunito', 'query' => 'Nunito:wght@400..800'],
        'space-grotesk' => ['name' => 'Space Grotesk', 'query' => 'Space+Grotesk:wght@400..700'],
        'outfit' => ['name' => 'Outfit', 'query' => 'Outfit:wght@400..800'],
        'monocraft' => ['name' => 'Monocraft', 'query' => null],
    ];

    // The md, lg and xl corner radii in pixels.
    public const RADII = [
        'none' => ['name' => 'Recht', 'sizes' => [0, 0, 0]],
        'small' => ['name' => 'Klein', 'sizes' => [3, 4, 6]],
        'default' => ['name' => 'Standaard', 'sizes' => [6, 8, 10]],
        'large' => ['name' => 'Rond', 'sizes' => [10, 14, 18]],
    ];

    private const SHADE_KEYS = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950];

    // How far each shade is mixed towards white (positive) or black (negative); 600 is the chosen colour.
    public const SHADES = [
        50 => 0.92, 100 => 0.84, 200 => 0.68, 300 => 0.5, 400 => 0.3, 500 => 0.14,
        600 => 0, 700 => -0.15, 800 => -0.3, 900 => -0.45, 950 => -0.65,
    ];

    protected $fillable = [
        'accent_color',
        'neutrals',
        'font',
        'heading_font',
        'radius',
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

    public function hasCustomTheme(): bool
    {
        return $this->accent_color !== null || $this->neutrals !== null || $this->font !== null
            || $this->heading_font !== null || $this->radius !== null;
    }

    public function neutralsKey(): string
    {
        return isset(self::NEUTRALS[$this->neutrals]) ? $this->neutrals : self::DEFAULT_NEUTRALS;
    }

    public function fontKey(): string
    {
        return isset(self::FONTS[$this->font]) ? $this->font : self::DEFAULT_FONT;
    }

    // Null when headings use the body font.
    public function headingFontKey(): ?string
    {
        return isset(self::FONTS[$this->heading_font]) ? $this->heading_font : null;
    }

    public function radiusKey(): string
    {
        return isset(self::RADII[$this->radius]) ? $this->radius : self::DEFAULT_RADIUS;
    }

    public static function fontStylesheetUrl(?string ...$fonts): string
    {
        $families = collect($fonts)->filter()->unique()
            ->map(fn (string $font) => self::FONTS[$font]['query'])->filter()
            ->push('JetBrains+Mono:wght@500')
            ->map(fn (string $query) => "family={$query}");

        return 'https://fonts.googleapis.com/css2?'.$families->implode('&').'&display=swap';
    }

    /**
     * The theme variables that differ from the defaults in app.css.
     *
     * @return array<string, string>
     */
    public function cssVariables(): array
    {
        $variables = [];

        if ($this->hasCustomAccent()) {
            foreach ($this->palette() as $shade => $channels) {
                $variables["--accent-{$shade}"] = $channels;
            }
        }

        if ($this->neutralsKey() !== self::DEFAULT_NEUTRALS) {
            foreach (self::NEUTRALS[$this->neutralsKey()]['shades'] as $i => $hex) {
                $variables['--gray-'.self::SHADE_KEYS[$i]] = implode(' ', sscanf($hex, '#%02x%02x%02x'));
            }
        }

        if ($this->fontKey() !== self::DEFAULT_FONT) {
            $variables['--font-sans'] = '"'.self::FONTS[$this->fontKey()]['name'].'"';
        }

        if ($this->headingFontKey()) {
            $variables['--font-heading'] = '"'.self::FONTS[$this->headingFontKey()]['name'].'"';
        }

        if ($this->radiusKey() !== self::DEFAULT_RADIUS) {
            foreach (array_combine(['md', 'lg', 'xl'], self::RADII[$this->radiusKey()]['sizes']) as $size => $px) {
                $variables["--radius-{$size}"] = "{$px}px";
            }
        }

        return $variables;
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
