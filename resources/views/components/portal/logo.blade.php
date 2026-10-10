{{-- The portal logo: the admin's upload, or the OpenMinetopia pixel logo. --}}
@php($branding = \App\Models\BrandingSetting::current())
<img src="{{ $branding->logoUrl() }}" alt=""
     {{ $attributes->class(['h-7 w-7 object-contain animate-omt-land', 'omt-pixel' => $branding->isLogoPixelated()]) }}>
