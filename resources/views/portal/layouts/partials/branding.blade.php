{{-- The admin-chosen theme, overriding the default variables from app.css. --}}
@php($branding = \App\Models\BrandingSetting::current())
<link href="{{ \App\Models\BrandingSetting::fontStylesheetUrl($branding->fontKey(), $branding->headingFontKey()) }}" rel="stylesheet">
@if ($variables = $branding->cssVariables())
    <style>
        :root {
            @foreach ($variables as $name => $value)
                {{ $name }}: {!! $value !!};
            @endforeach
        }
    </style>
@endif
@if ($branding->logo_path)
    <link rel="icon" href="{{ $branding->logoUrl() }}">
@endif
