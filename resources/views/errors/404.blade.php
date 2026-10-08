@extends('errors.layout')

@section('title', 'Pagina niet gevonden')
@section('code', '404')
@section('header', 'Deze pagina bestaat niet')

@section('content')
    <p>De link klopt niet of de pagina is verplaatst.</p>
    <p><a href="/" class="font-semibold text-indigo-600 hover:underline dark:text-indigo-400">Terug naar het portaal</a></p>
@endsection
