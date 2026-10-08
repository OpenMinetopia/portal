@extends('portal.layouts.app')

@section('title', 'Overzicht')
@section('header', 'Overzicht')

@section('content')
    @php
        $user = auth()->user();
        $stats = [
            ['label' => 'Saldo', 'value' => $user->formatted_balance_with_currency, 'href' => route('portal.bank-accounts.index')],
            ['label' => 'Plots', 'value' => count($user->getPlotsAttribute()), 'href' => route('portal.plots.index')],
            ['label' => 'Baan', 'value' => $user->getPrefixAttribute(), 'href' => null],
            ['label' => 'Level', 'value' => $user->level, 'href' => null],
            ['label' => 'Fitheid', 'value' => $user->getFitnessAttribute(), 'href' => null],
        ];
    @endphp

    <div class="flex items-center gap-4">
        <img src="https://mc-heads.net/avatar/{{ $user->minecraft_username }}/64" alt=""
             class="omt-pixel h-14 w-14 rounded-md bg-gray-100 dark:bg-gray-800">
        <div>
            <p class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Hoi {{ $user->name }}</p>
            <p class="text-gray-600 dark:text-gray-400">Zo staat het ervoor met <span class="font-mono text-sm">{{ $user->minecraft_username }}</span>.</p>
        </div>
    </div>

    <dl class="mt-8 flex flex-wrap gap-px overflow-hidden rounded-lg border border-gray-200 bg-gray-200 dark:border-gray-800 dark:bg-gray-800">
        @foreach ($stats as $stat)
            @php($tag = $stat['href'] ? 'a' : 'div')
            <{{ $tag }} @if($stat['href']) href="{{ $stat['href'] }}" @endif
                class="group min-w-0 flex-1 basis-40 bg-white p-5 transition-colors dark:bg-gray-900 {{ $stat['href'] ? 'hover:bg-gray-50 dark:hover:bg-gray-800' : '' }}">
                <dt class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                    {{ $stat['label'] }}
                    @if ($stat['href'])
                        <span class="text-gray-400 transition-transform group-hover:translate-x-0.5" aria-hidden="true">→</span>
                    @endif
                </dt>
                <dd class="mt-1 truncate text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $stat['value'] }}</dd>
            </{{ $tag }}>
        @endforeach
    </dl>
@endsection
