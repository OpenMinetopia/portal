@extends('portal.layouts.app')

@php
    $accounts = collect(auth()->user()->bank_accounts);
    $canPay = fn ($account) => $account['balance'] >= $offer->price && !($account['frozen'] ?? false);
    $default = $accounts->first(fn ($account) => $account['type'] === 'PRIVATE' && $canPay($account)) ?? $accounts->first($canPay);
    $periodWord = $offer->period === 'week' ? 'week' : 'maand';
@endphp

@section('title', 'Plot huren')
@section('header')
    <div class="flex items-center gap-4">
        <a href="{{ route('portal.plots.listings.index') }}"
           class="group flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
            <x-heroicon-s-arrow-left class="w-5 h-5"/>
            Terug naar de makelaar
        </a>
    </div>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="bg-yellow-50 dark:bg-yellow-500/10 rounded-lg p-4">
            <div class="flex">
                <x-heroicon-s-exclamation-triangle class="h-5 w-5 flex-shrink-0 text-yellow-400 dark:text-yellow-300"/>
                <div class="ml-3 text-sm">
                    <h3 class="font-medium text-yellow-800 dark:text-yellow-300">Automatische verlenging</h3>
                    <p class="mt-2 text-yellow-700 dark:text-yellow-200">
                        Je betaalt nu de eerste {{ $periodWord }}. Daarna wordt elke {{ $periodWord }} automatisch
                        {{ $offer->formatted_price }} van dezelfde rekening afgeschreven. Staat er te weinig op, dan heb je
                        {{ \App\Models\PlotRental::GRACE_DAYS }} dagen om te betalen. Daarna eindigt de huur en verlies je de
                        toegang tot het plot. Opzeggen kan altijd via Mijn huur.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                    @if($offer->image_path)
                        <img src="{{ Storage::url($offer->image_path) }}" alt="Afbeelding van {{ $offer->plot_name }}"
                             class="w-full h-64 object-cover">
                    @endif
                    <div class="p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $offer->plot_name }}</h1>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Verhuurder: {{ $offer->landlord_name }}</p>
                            </div>
                            <span class="inline-flex items-center rounded-md px-2.5 py-1.5 text-sm font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20 whitespace-nowrap">
                                {{ $offer->formatted_price }} {{ $offer->period_text }}
                            </span>
                        </div>
                        <div class="mt-6">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Beschrijving</h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $offer->description }}</p>
                        </div>
                        <dl class="mt-6 grid grid-cols-2 gap-6 pt-6 border-t border-gray-200 dark:border-gray-700 text-sm">
                            <div>
                                <dt class="font-medium text-gray-700 dark:text-gray-300">Afmetingen</dt>
                                <dd class="mt-1 text-gray-600 dark:text-gray-300">
                                    {{ $offer->dimensions['width'] }}x{{ $offer->dimensions['length'] }}x{{ $offer->dimensions['height'] }} ({{ $offer->area }} m²)
                                </dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-700 dark:text-gray-300">Locatie</dt>
                                <dd class="mt-1 font-mono text-gray-600 dark:text-gray-300">{{ $offer->min_x }}, {{ $offer->min_y }}, {{ $offer->min_z }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <form action="{{ route('portal.plots.rentals.rent', $offer) }}" method="POST" class="space-y-6"
                  x-data="{ account: @js($default['uuid'] ?? null), terms: false }">
                @csrf
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Betaalrekening</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Hier wordt de huur elke {{ $periodWord }} van afgeschreven</p>
                    </div>
                    <div class="px-6 py-5 space-y-3">
                        @forelse($accounts as $account)
                            <label @class([
                                'relative flex items-start gap-3 p-4 rounded-lg border transition-colors',
                                'border-gray-200 dark:border-gray-700 hover:border-indigo-500 cursor-pointer' => $canPay($account),
                                'border-red-200 dark:border-red-800 cursor-not-allowed opacity-60' => !$canPay($account),
                            ])>
                                <input type="radio" name="renter_bank_account_uuid" value="{{ $account['uuid'] }}" x-model="account"
                                       @disabled(!$canPay($account)) class="mt-0.5 h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <span class="min-w-0">
                                    <span class="block text-sm font-medium text-gray-900 dark:text-white">{{ $account['name'] }}</span>
                                    <span class="block text-sm text-gray-600 dark:text-gray-300">Saldo: € {{ number_format($account['balance'], 2, ',', '.') }}</span>
                                    @unless($canPay($account))
                                        <span class="mt-1 inline-flex text-xs font-medium text-red-700 dark:text-red-400">
                                            {{ ($account['frozen'] ?? false) ? 'Rekening bevroren' : 'Onvoldoende saldo' }}
                                        </span>
                                    @endunless
                                </span>
                            </label>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">Je hebt nog geen bankrekening.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6 space-y-4">
                    @if($errors->any())
                        <div class="bg-red-50 dark:bg-red-500/10 rounded-lg p-4 text-sm text-red-700 dark:text-red-200">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Nu te betalen</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{ $offer->formatted_price }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Daarna</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{ $offer->formatted_price }} {{ $offer->period_text }}</dd>
                        </div>
                    </dl>

                    <label class="flex items-start gap-3 p-4 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer">
                        <input id="terms" name="terms" type="checkbox" value="1" x-model="terms" required
                               class="mt-0.5 h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                        <span class="text-sm text-gray-900 dark:text-white">
                            Ik ga akkoord met de huur en de automatische verlenging elke {{ $periodWord }}
                        </span>
                    </label>

                    <button type="submit" :disabled="!account || !terms"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        <x-heroicon-s-key class="w-4 h-4 mr-2"/>
                        Plot huren voor {{ $offer->formatted_price }} {{ $offer->period_text }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
