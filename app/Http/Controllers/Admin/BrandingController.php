<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BrandingSetting;
use App\Support\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BrandingController extends Controller
{
    public function edit()
    {
        return view('portal.admin.branding.edit', [
            'branding' => BrandingSetting::first() ?? new BrandingSetting(),
            'gallery' => Theme::gallery(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'accent_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'neutrals' => ['required', Rule::in(array_keys(BrandingSetting::NEUTRALS))],
            'font' => ['required', Rule::in(array_keys(BrandingSetting::FONTS))],
            'heading_font' => ['nullable', Rule::in(array_keys(BrandingSetting::FONTS))],
            'radius' => ['required', Rule::in(array_keys(BrandingSetting::RADII))],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,gif', 'max:1024'],
            'remove_logo' => ['nullable', 'boolean'],
        ], [
            'accent_color.required' => 'Kies een accentkleur.',
            'accent_color.regex' => 'De accentkleur moet een hexcode zijn, bijvoorbeeld #3f6b30.',
            'neutrals.*' => 'Kies een van de grijstinten.',
            'font.*' => 'Kies een van de lettertypes.',
            'heading_font.*' => 'Kies een van de lettertypes voor koppen.',
            'radius.*' => 'Kies een van de hoeken.',
            'logo.image' => 'Het logo moet een afbeelding zijn.',
            'logo.mimes' => 'Het logo moet een PNG, JPG, WebP of GIF zijn.',
            'logo.max' => 'Het logo mag maximaal 1 MB zijn.',
        ]);

        $branding = BrandingSetting::first() ?? new BrandingSetting();
        $branding->accent_color = strtolower($validated['accent_color']);
        $branding->neutrals = $validated['neutrals'];
        $branding->font = $validated['font'];
        $branding->heading_font = $validated['heading_font'] ?? null;
        $branding->radius = $validated['radius'];
        $branding->logo_pixelated = $request->boolean('logo_pixelated');

        if ($request->hasFile('logo') || $request->boolean('remove_logo')) {
            if ($branding->logo_path) {
                Storage::disk('public')->delete($branding->logo_path);
            }
            $branding->logo_path = $request->hasFile('logo')
                ? $request->file('logo')->store('branding', 'public')
                : null;
        }

        $branding->save();

        return back()->with('success', 'Het uiterlijk is bijgewerkt.');
    }

    public function reset()
    {
        $branding = BrandingSetting::first();

        if ($branding) {
            if ($branding->logo_path) {
                Storage::disk('public')->delete($branding->logo_path);
            }
            $branding->update([
                'accent_color' => null, 'neutrals' => null, 'font' => null, 'heading_font' => null, 'radius' => null,
                'logo_path' => null, 'logo_pixelated' => false,
            ]);
        }

        return back()->with('success', 'Het standaard uiterlijk is hersteld.');
    }

    public function export()
    {
        $theme = Theme::fromBranding(BrandingSetting::current(), config('app.name'));

        $filename = (Str::slug(config('app.name')) ?: 'portaal').'-thema.json';

        return response()->json($theme->toArray(), 200, [
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /** Loads a theme file into the form, so the admin sees it before saving. */
    public function import(Request $request)
    {
        $request->validate([
            'theme' => ['required', 'file', 'max:16'],
        ], [
            'theme.required' => 'Kies een themabestand.',
            'theme.max' => 'Een themabestand is maximaal 16 KB.',
        ]);

        $data = json_decode($request->file('theme')->get(), true);

        try {
            $theme = Theme::fromArray(is_array($data) ? $data : []);
        } catch (ValidationException $e) {
            return back()->withErrors(['theme' => $e->validator->errors()->first()]);
        }

        return back()
            ->withInput($theme->formInput())
            ->with('success', "Thema \"{$theme->name}\" geladen. Bekijk het voorbeeld en sla op om het te gebruiken.");
    }

    public function logo()
    {
        $path = BrandingSetting::current()->logo_path;

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path, null, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
