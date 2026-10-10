<!DOCTYPE html>
<html lang="nl" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Overzicht') — {{ config('app.name', 'Minetopia Panel') }}</title>

    @unless (\App\Models\BrandingSetting::current()->logo_path)
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    @endunless
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#ffffff">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:wght@400..800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

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
    sidebarOpen: false,
    darkMode: document.documentElement.classList.contains('dark'),
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.theme = this.darkMode ? 'dark' : 'light';
        document.documentElement.classList.toggle('dark', this.darkMode);
    }
}">
    @include('portal.layouts.partials.sidebar')

    <div class="min-h-full bg-gray-50 lg:pl-64 dark:bg-gray-900">
        @include('portal.layouts.partials.header')

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="animate-omt-rise">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
    <x-notification/>
</body>
</html>
