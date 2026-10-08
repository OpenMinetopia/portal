@extends('portal.layouts.guest')

@section('title', 'Link verlopen')
@section('header', 'Deze link werkt niet meer')
@section('subheader', 'Een beheerderslink is maar één keer en kort geldig.')

@section('content')
    <p class="text-gray-600 dark:text-gray-400">
        Vraag op de OpenMinetopia-website bij je portaal een nieuwe beheerderslink aan en open die meteen.
    </p>

    <a href="{{ route('login') }}" class="mt-8 inline-flex w-full justify-center rounded-md bg-indigo-600 px-4 py-2.5 font-semibold text-white transition hover:bg-indigo-500 active:translate-y-px dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:hover:text-gray-900">
        Naar inloggen
    </a>
@endsection
