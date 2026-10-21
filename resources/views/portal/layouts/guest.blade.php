<!DOCTYPE html>
<html lang="nl" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — {{ config('app.name', 'Minetopia Panel') }}</title>
    @unless (\App\Models\BrandingSetting::current()->logo_path)
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endunless
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('portal.layouts.partials.branding')
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>
<body class="h-full bg-white text-gray-900 dark:bg-gray-900 dark:text-gray-100" x-data="{
    darkMode: document.documentElement.classList.contains('dark'),
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.theme = this.darkMode ? 'dark' : 'light';
        document.documentElement.classList.toggle('dark', this.darkMode);
    }
}">
    <div class="flex min-h-full flex-col justify-center px-4 py-12">
        <div class="mx-auto w-full max-w-sm animate-omt-rise">
            <div class="flex items-center justify-between">
                <span class="omt-hop omt-heading flex items-center gap-3 text-base font-bold tracking-tight">
                    <x-portal.logo />
                    {{ config('app.name') }}
                </span>
                <button @click="toggleDarkMode" type="button" class="rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                        :aria-label="darkMode ? 'Licht thema' : 'Donker thema'">
                    <x-heroicon-o-moon x-show="!darkMode" class="h-5 w-5" />
                    <x-heroicon-o-sun x-show="darkMode" x-cloak class="h-5 w-5" />
                </button>
            </div>

            <h1 class="mt-10 text-3xl font-bold tracking-tight">@yield('header')</h1>
            @hasSection('subheader')
                <p class="mt-2 text-gray-600 dark:text-gray-400">@yield('subheader')</p>
            @endif

            @if (session()->has(\App\Services\Tenancy\AdminClaim::SESSION_KEY))
                <div class="mt-6 rounded-md border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-900 dark:border-indigo-500/30 dark:bg-indigo-500/10 dark:text-indigo-200">
                    Log in of maak een account aan. Daarna ben je beheerder van dit portaal.
                </div>
            @endif

            <div class="mt-8">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
