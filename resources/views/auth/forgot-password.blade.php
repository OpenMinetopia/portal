@extends('portal.layouts.guest')

@section('title', 'Wachtwoord vergeten')
@section('header', 'Wachtwoord vergeten')
@section('subheader', 'Vul je e-mailadres in. Je krijgt een link om een nieuw wachtwoord in te stellen.')

@section('content')
    <x-portal.status />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <x-portal.input name="email" type="email" label="E-mailadres" required autofocus autocomplete="email" />

        <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2.5 font-semibold text-white transition hover:bg-indigo-500 active:translate-y-px dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:hover:text-gray-900">
            Link versturen
        </button>
    </form>

    <p class="mt-8 border-t border-gray-200 pt-6 text-sm text-gray-600 dark:border-gray-800 dark:text-gray-400">
        Weet je het weer? <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:underline dark:text-indigo-400">Inloggen</a>
    </p>
@endsection
