<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BrandingSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrandingController extends Controller
{
    public function edit()
    {
        return view('portal.admin.branding.edit', [
            'branding' => BrandingSetting::first() ?? new BrandingSetting(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'accent_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,gif', 'max:1024'],
            'remove_logo' => ['nullable', 'boolean'],
        ], [
            'accent_color.required' => 'Kies een accentkleur.',
            'accent_color.regex' => 'De accentkleur moet een hexcode zijn, bijvoorbeeld #3f6b30.',
            'logo.image' => 'Het logo moet een afbeelding zijn.',
            'logo.mimes' => 'Het logo moet een PNG, JPG, WebP of GIF zijn.',
            'logo.max' => 'Het logo mag maximaal 1 MB zijn.',
        ]);

        $branding = BrandingSetting::first() ?? new BrandingSetting();
        $branding->accent_color = strtolower($validated['accent_color']);
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
            $branding->update(['accent_color' => null, 'logo_path' => null, 'logo_pixelated' => false]);
        }

        return back()->with('success', 'Het standaard uiterlijk is hersteld.');
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
