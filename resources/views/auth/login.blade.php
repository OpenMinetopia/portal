@extends('portal.layouts.guest')

@section('title', 'Inloggen')
@section('header', 'Inloggen')
@section('subheader', 'Log in met je portaal-account.')

@section('content')
    @if (\App\Support\Portal::demo())
        <div class="mb-6 rounded-md border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-900 dark:border-indigo-500/30 dark:bg-indigo-500/10 dark:text-indigo-200">
            Demo: log in met <span class="font-mono">{{ \Database\Seeders\DemoSeeder::EMAIL }}</span> en wachtwoord <span class="font-mono">{{ \Database\Seeders\DemoSeeder::PASSWORD }}</span>.
        </div>
    @endif
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <x-portal.input name="email" type="email" label="E-mailadres" required autofocus autocomplete="email" />
        <x-portal.input name="password" type="password" label="Wachtwoord" required autocomplete="current-password" />

        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
            <input type="checkbox" name="remember" id="remember" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-800">
            Ingelogd blijven
        </label>

        <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2.5 font-semibold text-white transition hover:bg-indigo-500 active:translate-y-px dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:hover:text-gray-900">
            Inloggen
        </button>
    </form>

    <p class="mt-8 border-t border-gray-200 pt-6 text-sm text-gray-600 dark:border-gray-800 dark:text-gray-400">
        Nog geen account? <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:underline dark:text-indigo-400">Account aanmaken</a>
    </p>
@endsection
