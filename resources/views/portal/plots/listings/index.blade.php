@extends('portal.layouts.app')

@section('title', 'Makelaar')
@section('header', 'Makelaar')

@php
    $tabs = [
        'koop' => ['label' => 'Te koop', 'count' => $listings->count()],
        'huur' => ['label' => 'Te huur', 'count' => $rentalOffers->count()],
    ];
@endphp

@section('content')
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex gap-6" aria-label="Kopen of huren">
                @foreach($tabs as $key => $item)
                    <a href="{{ route('portal.plots.listings.index', $key === 'huur' ? ['soort' => 'huur'] : []) }}"
                       @if($tab === $key) aria-current="page" @endif
                       @class([
                           'inline-flex items-center gap-2 border-b-2 px-1 pb-3 text-sm font-medium',
                           'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' => $tab === $key,
                           'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' => $tab !== $key,
                       ])>
                        {{ $item['label'] }}
                        <span @class([
                            'rounded-full px-2 py-0.5 text-xs font-medium',
                            'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400' => $tab === $key,
                            'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' => $tab !== $key,
                        ])>{{ $item['count'] }}</span>
                    </a>
                @endforeach
            </nav>
            <a href="{{ route('portal.plots.rentals.index') }}" class="mb-3 inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                <x-heroicon-s-key class="w-4 h-4"/>
                Mijn huur
            </a>
        </div>

        @if($tab === 'koop')
        <!-- Listings Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($listings as $listing)
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                    @if($listing->image_path)
                        <img src="{{ Storage::url($listing->image_path) }}"
                             alt="Afbeelding van {{ $listing->plot_name }}"
                             class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                            <x-heroicon-o-photo class="w-12 h-12 text-gray-400 dark:text-gray-500"/>
                        </div>
                    @endif

                    <div class="p-6">
                        <div class="flex flex-wrap justify-between items-start gap-2">
                            <div class="min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white [overflow-wrap:anywhere]">
                                    {{ $listing->plot_name }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Verkoper: {{ $listing->seller_name }}
                                </p>
                            </div>
                            <span class="inline-flex shrink-0 items-center whitespace-nowrap rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20">
                                {{ $listing->formatted_price }}
                            </span>
                        </div>

                        <p class="mt-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ Str::limit($listing->description, 150) }}
                        </p>

                        <div class="mt-4 flex items-center justify-between text-sm">
                            <div class="text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col gap-1">
                                    <span class="flex items-center gap-1">
                                        <x-heroicon-s-map-pin class="w-4 h-4"/>
                                        Min: {{ $listing->min_x }}, {{ $listing->min_y }}, {{ $listing->min_z }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <x-heroicon-s-map-pin class="w-4 h-4"/>
                                        Max: {{ $listing->max_x }}, {{ $listing->max_y }}, {{ $listing->max_z }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-gray-500 dark:text-gray-400">
                                {{ $listing->dimensions['width'] }}x{{ $listing->dimensions['length'] }}x{{ $listing->dimensions['height'] }}
                                ({{ $listing->area }} m²)
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            @if($listing->instant_buy)
                                <a href="{{ route('portal.plots.listings.buy.show', $listing) }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                    <x-heroicon-s-shopping-cart class="w-4 h-4 mr-2"/>
                                    Plot kopen voor {{ $listing->formatted_price }}
                                </a>
                            @else
                                <span class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                                    <x-heroicon-s-chat-bubble-left-right class="w-4 h-4 mr-2"/>
                                    Contact verkoper
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center bg-white dark:bg-gray-800 rounded-lg shadow-sm px-6 py-8">
                        <x-heroicon-o-home class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"/>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen plots te koop</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Er staan momenteel geen plots te koop.
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
        @else
        <!-- Rental Offers Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($rentalOffers as $offer)
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                    @if($offer->image_path)
                        <img src="{{ Storage::url($offer->image_path) }}"
                             alt="Afbeelding van {{ $offer->plot_name }}"
                             class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                            <x-heroicon-o-key class="w-12 h-12 text-gray-400 dark:text-gray-500"/>
                        </div>
                    @endif

                    <div class="p-6">
                        <div class="flex flex-wrap justify-between items-start gap-2">
                            <div class="min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white [overflow-wrap:anywhere]">{{ $offer->plot_name }}</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Verhuurder: {{ $offer->landlord_name }}</p>
                            </div>
                            <span class="inline-flex shrink-0 items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20 whitespace-nowrap">
                                {{ $offer->formatted_price }} {{ $offer->period_text }}
                            </span>
                        </div>

                        <p class="mt-4 text-sm text-gray-600 dark:text-gray-300">{{ Str::limit($offer->description, 150) }}</p>

                        <div class="mt-4 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                            <span class="flex items-center gap-1">
                                <x-heroicon-s-map-pin class="w-4 h-4"/>
                                {{ $offer->min_x }}, {{ $offer->min_y }}, {{ $offer->min_z }}
                            </span>
                            <span>{{ $offer->dimensions['width'] }}x{{ $offer->dimensions['length'] }} ({{ $offer->area }} m²)</span>
                        </div>

                        <div class="mt-6 flex justify-end">
                            @if($offer->landlord_id === auth()->id() && !$offer->is_server_listing)
                                <span class="text-sm text-gray-500 dark:text-gray-400">Jouw plot</span>
                            @else
                                <a href="{{ route('portal.plots.rentals.rent.show', $offer) }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                    <x-heroicon-s-key class="w-4 h-4 mr-2"/>
                                    Huren
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center bg-white dark:bg-gray-800 rounded-lg shadow-sm px-6 py-8">
                        <x-heroicon-o-key class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"/>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen plots te huur</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Er staan momenteel geen plots te huur.</p>
                    </div>
                </div>
            @endforelse
        </div>
        @endif
    </div>
@endsection
