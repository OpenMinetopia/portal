@extends('portal.layouts.guest')

@section('title', 'Nieuw wachtwoord')
@section('header', 'Nieuw wachtwoord')
@section('subheader', 'Kies een nieuw wachtwoord voor je account.')

@section('content')
    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-portal.input name="email" type="email" label="E-mailadres" required autocomplete="email" :value="$email" />
        <x-portal.input name="password" type="password" label="Nieuw wachtwoord" required autofocus autocomplete="new-password" hint="Minimaal 8 tekens." />
        <x-portal.input name="password_confirmation" type="password" label="Nieuw wachtwoord herhalen" required autocomplete="new-password" />

        <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2.5 font-semibold text-white transition hover:bg-indigo-500 active:translate-y-px dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:hover:text-gray-900">
            Wachtwoord opslaan
        </button>
    </form>

    <p class="mt-8 border-t border-gray-200 pt-6 text-sm text-gray-600 dark:border-gray-800 dark:text-gray-400">
        Werkt de link niet meer? <a href="{{ route('password.request') }}" class="font-semibold text-indigo-600 hover:underline dark:text-indigo-400">Nieuwe link aanvragen</a>
    </p>
@endsection
