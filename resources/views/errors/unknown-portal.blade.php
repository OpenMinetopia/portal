@extends('errors.layout', ['brand' => 'OpenMinetopia'])

@section('title', 'Portaal niet gevonden')
@section('code', '404')
@section('header', 'Hier staat geen portaal')

@section('content')
    <p>
        Op <span class="font-mono text-gray-900 dark:text-gray-100">{{ request()->getHost() }}</span> draait (nog) geen
        OpenMinetopia-portaal. Controleer het adres, of vraag de beheerders van je server om de juiste link.
    </p>
    <p>
        Net een portaal aangemaakt? Het kan een paar minuten duren voordat het bereikbaar is.
    </p>
@endsection
