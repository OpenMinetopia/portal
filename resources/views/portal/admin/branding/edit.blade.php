@extends('portal.layouts.app')

@section('title', 'Uiterlijk & logo')
@section('header', 'Uiterlijk & logo')

@section('content')
    <div class="space-y-6">
        <p class="text-sm text-gray-500 dark:text-gray-400">Geef het portaal de kleur en het logo van je eigen server. Wijzigingen zijn direct zichtbaar voor iedereen.</p>

        <form action="{{ route('portal.admin.branding.update') }}" method="POST" enctype="multipart/form-data"
              x-data="{
                  color: @js(old('accent_color', $branding->accent_color ?? \App\Models\BrandingSetting::DEFAULT_ACCENT)),
                  logoPreview: @js($branding->logo_path ? $branding->logoUrl() : null),
                  removeLogo: false,
                  pickLogo(event) {
                      const file = event.target.files[0];
                      this.removeLogo = false;
                      this.logoPreview = file ? URL.createObjectURL(file) : @js($branding->logo_path ? $branding->logoUrl() : null);
                  },
              }"
              class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            @csrf
            @method('PUT')

            <div class="space-y-6 lg:col-span-2">
                <!-- Accent colour -->
                <div class="rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-4 py-5 sm:px-6 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Accentkleur</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">De kleur van knoppen, links en markeringen. Lichtere en donkerdere tinten worden automatisch berekend.</p>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <label for="accent_color" class="block text-sm font-semibold text-gray-900 dark:text-white">Kleur</label>
                        <div class="mt-1.5 flex items-center gap-3">
                            <input type="color" x-model="color" aria-label="Kies een kleur"
                                   class="h-10 w-14 cursor-pointer rounded-md border-0 bg-transparent p-0 ring-1 ring-inset ring-gray-300 dark:ring-gray-700">
                            <input type="text" id="accent_color" name="accent_color" x-model="color" maxlength="7" required
                                   class="block w-32 rounded-md border-0 bg-white px-3 py-2 font-mono text-sm text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:bg-gray-800 dark:text-white dark:ring-gray-700 dark:focus:ring-indigo-400">
                            <button type="button" @click="color = @js(\App\Models\BrandingSetting::DEFAULT_ACCENT)"
                                    class="text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                                Standaardkleur
                            </button>
                        </div>
                        @error('accent_color')
                            <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        <div class="mt-6">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Voorbeeld</p>
                            <div class="mt-2 flex flex-wrap items-center gap-3">
                                <span class="inline-flex items-center rounded-md px-3 py-2 text-sm font-semibold text-white" :style="{ backgroundColor: color }">Knop</span>
                                <span class="text-sm font-semibold underline" :style="{ color: color }">Link</span>
                                <span class="h-3 w-3 rounded-sm" :style="{ backgroundColor: color }"></span>
                            </div>
                            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Tip: kies een kleur die donker genoeg is voor witte tekst.</p>
                        </div>
                    </div>
                </div>

                <!-- Logo -->
                <div class="rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-4 py-5 sm:px-6 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Logo</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Wordt getoond in de zijbalk, op de inlogpagina en als icoon in het browsertabblad.</p>
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
                <div class="rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <div class="space-y-3 px-4 py-5 sm:p-6">
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                            <x-heroicon-s-check class="mr-2 h-4 w-4" />
                            Opslaan
                        </button>
                        @if ($branding->exists && ($branding->accent_color || $branding->logo_path))
                            <button type="submit" form="branding-reset"
                                    class="inline-flex w-full items-center justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-600">
                                <x-heroicon-s-arrow-uturn-left class="mr-2 h-4 w-4" />
                                Standaard herstellen
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        <form id="branding-reset" action="{{ route('portal.admin.branding.reset') }}" method="POST"
              onsubmit="return confirm('Accentkleur en logo terugzetten naar de standaard?')">
            @csrf
            @method('DELETE')
        </form>
    </div>
@endsection
