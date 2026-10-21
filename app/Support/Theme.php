<?php

namespace App\Support;

use App\Models\BrandingSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * A portal theme as a file: the accent, neutrals, fonts and corners, without the logo.
 * Only keys from the lists on BrandingSetting are accepted, so a theme can't carry
 * CSS, URLs or code. The gallery is every file in resources/themes.
 */
final class Theme
{
    public const FORMAT = 'openminetopia-theme';

    public function __construct(
        public readonly string $name,
        public readonly ?string $author,
        public readonly ?string $description,
        public readonly string $accent,
        public readonly string $neutrals,
        public readonly string $font,
        public readonly ?string $headingFont,
        public readonly string $radius,
    ) {}

    /** @throws \Illuminate\Validation\ValidationException */
    public static function fromArray(array $data): self
    {
        $valid = Validator::make($data, [
            'format' => ['required', Rule::in([self::FORMAT])],
            'version' => ['required', Rule::in([1])],
            'name' => ['required', 'string', 'max:50'],
            'author' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:200'],
            'accent' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'neutrals' => ['nullable', Rule::in(array_keys(BrandingSetting::NEUTRALS))],
            'font' => ['nullable', Rule::in(array_keys(BrandingSetting::FONTS))],
            'heading_font' => ['nullable', Rule::in(array_keys(BrandingSetting::FONTS))],
            'radius' => ['nullable', Rule::in(array_keys(BrandingSetting::RADII))],
        ], [
            'format.*' => 'Dit is geen themabestand van het portaal.',
            'version.*' => 'Dit thema is gemaakt voor een nieuwere versie van het portaal.',
            'name.*' => 'Het thema heeft geen geldige naam.',
            'accent.*' => 'Het thema heeft geen geldige accentkleur.',
            'neutrals.*' => 'Het thema gebruikt grijstinten die dit portaal niet kent.',
            'font.*' => 'Het thema gebruikt een lettertype dat dit portaal niet kent.',
            'heading_font.*' => 'Het thema gebruikt een lettertype dat dit portaal niet kent.',
            'radius.*' => 'Het thema gebruikt hoeken die dit portaal niet kent.',
        ])->validate();

        return new self(
            $valid['name'],
            $valid['author'] ?? null,
            $valid['description'] ?? null,
            strtolower($valid['accent']),
            $valid['neutrals'] ?? BrandingSetting::DEFAULT_NEUTRALS,
            $valid['font'] ?? BrandingSetting::DEFAULT_FONT,
            $valid['heading_font'] ?? null,
            $valid['radius'] ?? BrandingSetting::DEFAULT_RADIUS,
        );
    }

    public static function fromBranding(BrandingSetting $branding, string $name): self
    {
        return new self(
            $name,
            null,
            null,
            $branding->accent_color ?? BrandingSetting::DEFAULT_ACCENT,
            $branding->neutralsKey(),
            $branding->fontKey(),
            $branding->headingFontKey(),
            $branding->radiusKey(),
        );
    }

    /** @return Collection<string, self> keyed by file name, the default first */
    public static function gallery(): Collection
    {
        return collect(glob(resource_path('themes/*.json')))
            ->sortBy(fn (string $path) => [basename($path) !== 'openminetopia.json', $path])
            ->mapWithKeys(fn (string $path) => [
                basename($path, '.json') => self::fromArray(json_decode(file_get_contents($path), true) ?? []),
            ]);
    }

    /** The values for the appearance form. */
    public function formInput(): array
    {
        return [
            'accent_color' => $this->accent,
            'neutrals' => $this->neutrals,
            'font' => $this->font,
            'heading_font' => $this->headingFont ?? '',
            'radius' => $this->radius,
        ];
    }

    public function toArray(): array
    {
        return array_filter([
            'format' => self::FORMAT,
            'version' => 1,
            'name' => $this->name,
            'author' => $this->author,
            'description' => $this->description,
            'accent' => $this->accent,
            'neutrals' => $this->neutrals,
            'font' => $this->font,
            'heading_font' => $this->headingFont,
            'radius' => $this->radius,
        ], fn ($value) => $value !== null);
    }
}
