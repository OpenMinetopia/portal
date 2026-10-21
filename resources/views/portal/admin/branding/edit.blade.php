@extends('portal.layouts.app')

@section('title', 'Uiterlijk & logo')
@section('header', 'Uiterlijk & logo')

@use('App\Models\BrandingSetting')

@php
    $saved = [
        'accent_color' => $branding->accent_color ?? BrandingSetting::DEFAULT_ACCENT,
        'neutrals' => $branding->neutralsKey(),
        'font' => $branding->fontKey(),
        'heading_font' => $branding->headingFontKey() ?? '',
        'radius' => $branding->radiusKey(),
    ];

    $editor = [
        'initial' => collect($saved)->map(fn ($value, $key) => old($key, $value) ?? '')->all(),
        'saved' => $saved,
        'shades' => BrandingSetting::SHADES,
        'neutrals' => BrandingSetting::NEUTRALS,
        'fonts' => collect(BrandingSetting::FONTS)->map(fn ($font, $key) => ['name' => $font['name'], 'url' => BrandingSetting::fontStylesheetUrl($key)]),
        'radii' => BrandingSetting::RADII,
        'logo' => $branding->logo_path ? $branding->logoUrl() : null,
    ];
@endphp

@section('content')
    <div class="space-y-6">
        <p class="text-sm text-gray-500 dark:text-gray-400">Geef het portaal het uiterlijk van je eigen server. Wat je kiest zie je meteen op deze pagina; pas na opslaan ziet iedereen het.</p>

        <form action="{{ route('portal.admin.branding.update') }}" method="POST" enctype="multipart/form-data"
              x-data="brandingEditor(@js($editor))"
              class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            @csrf
            @method('PUT')

            <div class="space-y-6 lg:col-span-2">
                <!-- Gallery -->
                <div class="rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-4 py-5 sm:px-6 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Thema's</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Begin met een kant-en-klaar thema en pas het daarna verder aan.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-3 px-4 py-5 sm:grid-cols-2 sm:p-6">
                        @foreach ($gallery as $key => $theme)
                            @php($shades = BrandingSetting::NEUTRALS[$theme->neutrals]['shades'])
                            <button type="button" @click="use(@js($theme->formInput()))"
                                    :class="accent_color === @js($theme->accent) && neutrals === @js($theme->neutrals) && font === @js($theme->font) && heading_font === @js($theme->headingFont ?? '') && radius === @js($theme->radius)
                                        ? 'ring-2 ring-indigo-600 dark:ring-indigo-400' : 'ring-1 ring-gray-200 hover:ring-gray-300 dark:ring-gray-700 dark:hover:ring-gray-600'"
                                    class="flex items-start gap-3 rounded-lg p-3 text-left ring-inset">
                                <span class="flex h-12 w-12 shrink-0 flex-col justify-end gap-1 p-1.5 ring-1 ring-inset ring-black/5"
                                      style="background: {{ $shades[1] }}; border-radius: {{ BrandingSetting::RADII[$theme->radius]['sizes'][1] }}px">
                                    <span class="block h-2" style="background: {{ $shades[3] }}; border-radius: {{ min(BrandingSetting::RADII[$theme->radius]['sizes'][0], 4) }}px"></span>
                                    <span class="block h-3" style="background: {{ $theme->accent }}; border-radius: {{ min(BrandingSetting::RADII[$theme->radius]['sizes'][0], 4) }}px"></span>
                                </span>
                                <span class="min-w-0">
                                    <span class="block text-sm font-semibold text-gray-900 dark:text-white" style="font-family: '{{ BrandingSetting::FONTS[$theme->headingFont ?? $theme->font]['name'] }}'">{{ $theme->name }}</span>
                                    @if ($theme->description)
                                        <span class="mt-0.5 block text-sm text-gray-500 dark:text-gray-400">{{ $theme->description }}</span>
                                    @endif
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Colours -->
                <div class="rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-4 py-5 sm:px-6 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Kleuren</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">De accentkleur kleurt knoppen, links en markeringen; lichtere en donkerdere tinten worden automatisch berekend.</p>
                    </div>
                    <div class="space-y-6 px-4 py-5 sm:p-6">
                        <div>
                            <label for="accent_color" class="block text-sm font-semibold text-gray-900 dark:text-white">Accentkleur</label>
                            <div class="mt-1.5 flex items-center gap-3">
                                <input type="color" x-model="accent_color" aria-label="Kies een kleur"
                                       class="h-10 w-14 cursor-pointer rounded-md border-0 bg-transparent p-0 ring-1 ring-inset ring-gray-300 dark:ring-gray-700">
                                <input type="text" id="accent_color" name="accent_color" x-model="accent_color" maxlength="7" required
                                       class="block w-32 rounded-md border-0 bg-white px-3 py-2 font-mono text-sm text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:bg-gray-800 dark:text-white dark:ring-gray-700 dark:focus:ring-indigo-400">
                                <button type="button" @click="accent_color = @js(BrandingSetting::DEFAULT_ACCENT)"
                                        class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                                    Standaardkleur
                                </button>
                            </div>
                            @error('accent_color')
                                <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">Tip: kies een kleur die donker genoeg is voor witte tekst.</p>
                        </div>

                        <fieldset>
                            <legend class="text-sm font-semibold text-gray-900 dark:text-white">Grijstinten</legend>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Achtergronden, randen en tekst, in licht en donker.</p>
                            <div class="mt-2 grid grid-cols-2 gap-3 sm:grid-cols-4">
                                @foreach (BrandingSetting::NEUTRALS as $key => $set)
                                    <label :class="neutrals === @js($key) ? 'ring-2 ring-indigo-600 dark:ring-indigo-400' : 'ring-1 ring-gray-200 hover:ring-gray-300 dark:ring-gray-700 dark:hover:ring-gray-600'"
                                           class="cursor-pointer rounded-lg p-2 ring-inset">
                                        <input type="radio" name="neutrals" value="{{ $key }}" x-model="neutrals" class="sr-only">
                                        <span class="flex h-6 overflow-hidden rounded-md">
                                            @foreach ([1, 3, 5, 8, 9] as $i)
                                                <span class="flex-1" style="background: {{ $set['shades'][$i] }}"></span>
                                            @endforeach
                                        </span>
                                        <span class="mt-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $set['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('neutrals')
                                <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </fieldset>
                    </div>
                </div>

                <!-- Font and corners -->
                <div class="rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-4 py-5 sm:px-6 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Letters & hoeken</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kies een aparte letter voor koppen, bijvoorbeeld een Minecraft-letter, en houd de tekst goed leesbaar.</p>
                    </div>
                    <div class="space-y-6 px-4 py-5 sm:p-6">
                        @foreach (['heading_font' => ['Koppen', 'Titels van pagina\'s en blokken, en de naam van je server.'], 'font' => ['Tekst', 'Al het andere: menu, tabellen, knoppen en formulieren.']] as $field => [$legend, $hint])
                            <fieldset>
                                <legend class="text-sm font-semibold text-gray-900 dark:text-white">{{ $legend }}</legend>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $hint }}</p>
                                <div class="mt-2 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                    @if ($field === 'heading_font')
                                        <label :class="heading_font === '' ? 'ring-2 ring-indigo-600 dark:ring-indigo-400' : 'ring-1 ring-gray-200 hover:ring-gray-300 dark:ring-gray-700 dark:hover:ring-gray-600'"
                                               class="cursor-pointer rounded-lg px-3 py-2.5 ring-inset">
                                            <input type="radio" name="heading_font" value="" x-model="heading_font" class="sr-only">
                                            <span class="block text-base text-gray-900 dark:text-white">Zelfde als tekst</span>
                                            <span class="block text-sm text-gray-500 dark:text-gray-400">Eén letter voor alles</span>
                                        </label>
                                    @endif
                                    @foreach (BrandingSetting::FONTS as $key => $fontOption)
                                        <label :class="{{ $field }} === @js($key) ? 'ring-2 ring-indigo-600 dark:ring-indigo-400' : 'ring-1 ring-gray-200 hover:ring-gray-300 dark:ring-gray-700 dark:hover:ring-gray-600'"
                                               class="cursor-pointer rounded-lg px-3 py-2.5 ring-inset">
                                            <input type="radio" name="{{ $field }}" value="{{ $key }}" x-model="{{ $field }}" class="sr-only">
                                            <span class="block text-base text-gray-900 dark:text-white" style="font-family: '{{ $fontOption['name'] }}'">{{ $fontOption['name'] }}</span>
                                            <span class="block text-sm text-gray-500 dark:text-gray-400" style="font-family: '{{ $fontOption['name'] }}'">Saldo € 1.250,00</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error($field)
                                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </fieldset>
                        @endforeach

                        <fieldset>
                            <legend class="text-sm font-semibold text-gray-900 dark:text-white">Hoeken</legend>
                            <div class="mt-2 grid grid-cols-2 gap-3 sm:grid-cols-4">
                                @foreach (BrandingSetting::RADII as $key => $radiusOption)
                                    <label :class="radius === @js($key) ? 'ring-2 ring-indigo-600 dark:ring-indigo-400' : 'ring-1 ring-gray-200 hover:ring-gray-300 dark:ring-gray-700 dark:hover:ring-gray-600'"
                                           class="flex cursor-pointer items-center gap-2.5 rounded-lg px-3 py-2.5 ring-inset">
                                        <input type="radio" name="radius" value="{{ $key }}" x-model="radius" class="sr-only">
                                        <span class="h-5 w-5 shrink-0 border-l-2 border-t-2 border-gray-400 dark:border-gray-500" style="border-top-left-radius: {{ $radiusOption['sizes'][2] }}px"></span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $radiusOption['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('radius')
                                <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </fieldset>
                    </div>
                </div>

                <!-- Logo -->
                <div class="rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-4 py-5 sm:px-6 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Logo</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Wordt getoond in de zijbalk, op de inlogpagina en als icoon in het browsertabblad. Het logo hoort niet bij het thema en wordt niet meegeëxporteerd.</p>
                    </div>
                    <div class="space-y-5 px-4 py-5 sm:p-6">
                        <div class="flex items-center gap-4">
                            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-lg bg-gray-50 ring-1 ring-inset ring-gray-200 dark:bg-gray-900 dark:ring-gray-700">
                                <img x-show="logoPreview && !removeLogo" :src="logoPreview" alt="Huidig logo" class="h-12 w-12 object-contain">
                                <img x-show="!logoPreview || removeLogo" src="{{ asset('images/brand/logo.svg') }}" alt="Standaard logo" class="omt-pixel h-10 w-10">
                            </div>
                            <div class="min-w-0">
                                <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/webp,image/gif" @change="pickLogo"
                                       class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-gray-900 hover:file:bg-gray-200 dark:text-gray-400 dark:file:bg-gray-700 dark:file:text-white dark:hover:file:bg-gray-600">
                                <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">PNG, JPG, WebP of GIF, maximaal 1 MB. Een vierkant logo werkt het best.</p>
                            </div>
                        </div>
                        @error('logo')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        <label class="flex items-start gap-3">
                            <input type="checkbox" name="logo_pixelated" value="1" @checked(old('logo_pixelated', $branding->logo_pixelated))
                                   class="mt-0.5 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-700">
                            <span class="text-sm">
                                <span class="font-medium text-gray-700 dark:text-gray-300">Pixel-art logo</span>
                                <span class="block text-gray-500 dark:text-gray-400">Houdt de pixels scherp in plaats van ze te vervagen, handig voor een klein Minecraft-logo.</span>
                            </span>
                        </label>

                        @if ($branding->logo_path)
                            <label class="flex items-start gap-3">
                                <input type="checkbox" name="remove_logo" value="1" x-model="removeLogo"
                                       class="mt-0.5 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-700">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Eigen logo verwijderen en het standaard logo gebruiken</span>
                            </label>
                        @endif
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-lg bg-white shadow-sm lg:sticky lg:top-6 dark:bg-gray-800">
                    <div class="space-y-3 px-4 py-5 sm:p-6">
                        <p x-show="dirty" x-cloak class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <span class="h-2 w-2 rounded-full bg-indigo-600 dark:bg-indigo-400"></span>
                            Voorbeeld, nog niet opgeslagen
                        </p>
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                            <x-heroicon-s-check class="mr-2 h-4 w-4" />
                            Opslaan
                        </button>
                        @if ($branding->exists && ($branding->hasCustomTheme() || $branding->logo_path))
                            <button type="submit" form="branding-reset"
                                    class="inline-flex w-full items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-600">
                                <x-heroicon-s-arrow-uturn-left class="mr-2 h-4 w-4" />
                                Standaard herstellen
                            </button>
                        @endif
                    </div>

                    <div class="space-y-3 border-t border-gray-200 px-4 py-5 sm:p-6 dark:border-gray-700">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Thema delen</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Download het opgeslagen thema als bestand, of laad een thema van een andere server.</p>
                        </div>
                        <a href="{{ route('portal.admin.branding.export') }}"
                           class="inline-flex w-full items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-600">
                            <x-heroicon-s-arrow-down-tray class="mr-2 h-4 w-4" />
                            Exporteren
                        </a>
                        <label class="inline-flex w-full cursor-pointer items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-600">
                            <x-heroicon-s-arrow-up-tray class="mr-2 h-4 w-4" />
                            Importeren
                            <input type="file" name="theme" form="branding-import" accept="application/json,.json" class="sr-only"
                                   @change="$el.form.submit()">
                        </label>
                        @error('theme')
                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </form>

        <form id="branding-reset" action="{{ route('portal.admin.branding.reset') }}" method="POST"
              onsubmit="return confirm('Thema en logo terugzetten naar de standaard?')">
            @csrf
            @method('DELETE')
        </form>

        <form id="branding-import" action="{{ route('portal.admin.branding.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
        </form>
    </div>
@endsection
