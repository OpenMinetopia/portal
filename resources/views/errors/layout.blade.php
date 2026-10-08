{{-- Rendered before the session starts (unknown domain, paused portal), so nothing here may use the session. --}}
<!DOCTYPE html>
<html lang="nl" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('title') — {{ $brand ?? config('app.name', 'OpenMinetopia') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:wght@400..800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        }
    </script>
</head>
<body class="h-full bg-white text-gray-900 dark:bg-gray-900 dark:text-gray-100">
    <div class="flex min-h-full flex-col justify-center px-4 py-12">
        <div class="mx-auto w-full max-w-md animate-omt-rise">
            <span class="omt-hop flex items-center gap-3 text-base font-bold tracking-tight">
                <img src="{{ asset('images/brand/logo.svg') }}" alt="" class="omt-pixel h-7 w-7 animate-omt-land">
                {{ $brand ?? config('app.name', 'OpenMinetopia') }}
            </span>

            <p class="mt-10 font-mono text-sm font-medium text-indigo-600 dark:text-indigo-400">@yield('code')</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight">@yield('header')</h1>
            <div class="mt-4 space-y-4 text-gray-600 dark:text-gray-400">
                @yield('content')
            </div>

            <p class="mt-10 border-t border-gray-200 pt-6 text-sm text-gray-500 dark:border-gray-800 dark:text-gray-500">
                @if (\App\Support\Portal::hosted())
                    Een portaal van <a href="https://openminetopia.nl" class="font-semibold text-indigo-600 hover:underline dark:text-indigo-400">OpenMinetopia</a>.
                @else
                    Gemaakt met <a href="https://github.com/OpenMinetopia/portal" class="font-semibold text-indigo-600 hover:underline dark:text-indigo-400">OpenMinetopia Portal</a>.
                @endif
            </p>
        </div>
    </div>
</body>
</html>
