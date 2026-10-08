@extends('errors.layout')

@section('title', 'Portaal gepauzeerd')
@section('code', 'Gepauzeerd')
@section('header', 'Dit portaal is even gepauzeerd')

@section('content')
    <p>
        {{ config('app.name') }} is op dit moment niet beschikbaar. Je gegevens blijven bewaard
        en alles staat weer klaar zodra het portaal verlengd is.
    </p>
    <p>
        Ben jij de eigenaar? Log in op de OpenMinetopia-website en verleng je portaal met één klik.
        Speler? Laat het de beheerders van je server weten.
    </p>
@endsection
