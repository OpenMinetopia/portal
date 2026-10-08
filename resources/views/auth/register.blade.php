@extends('portal.layouts.guest')

@section('title', 'Account aanmaken')
@section('header', 'Account aanmaken')
@section('subheader', 'Daarna koppel je je Minecraft-account aan het portaal.')

@section('content')
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf
        <x-portal.input name="name" label="Naam" required autofocus autocomplete="name" />
        <x-portal.input name="email" type="email" label="E-mailadres" required autocomplete="email" />
        <x-portal.input name="minecraft_username" label="Minecraft-gebruikersnaam" required autocomplete="off" class="font-mono">
            <div id="minecraft-preview" class="mt-2"></div>
        </x-portal.input>
        <x-portal.input name="password" type="password" label="Wachtwoord" required autocomplete="new-password" />
        <x-portal.input name="password_confirmation" type="password" label="Wachtwoord herhalen" required autocomplete="new-password" />

        <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2.5 font-semibold text-white transition hover:bg-indigo-500 active:translate-y-px dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:hover:text-gray-900">
            Account aanmaken
        </button>
    </form>

    <p class="mt-8 border-t border-gray-200 pt-6 text-sm text-gray-600 dark:border-gray-800 dark:text-gray-400">
        Heb je al een account? <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:underline dark:text-indigo-400">Inloggen</a>
    </p>
@endsection
