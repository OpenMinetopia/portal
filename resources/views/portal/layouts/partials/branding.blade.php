{{-- The admin-chosen accent colour, overriding the default --accent-* variables from app.css. --}}
@php($branding = \App\Models\BrandingSetting::current())
@if ($branding->hasCustomAccent())
    <style>
        :root {
            @foreach ($branding->palette() as $shade => $channels)
                --accent-{{ $shade }}: {{ $channels }};
            @endforeach
        }
    </style>
@endif
@if ($branding->logo_path)
    <link rel="icon" href="{{ $branding->logoUrl() }}">
@endif
