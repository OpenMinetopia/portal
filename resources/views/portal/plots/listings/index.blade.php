@extends('portal.layouts.app')

@section('title', 'Plots te koop')
@section('header', 'Plots te koop')

@section('content')
    <div class="space-y-6">
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
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $listing->plot_name }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Verkoper: {{ \App\Helpers\MinecraftHelper::getName($listing->seller->minecraft_uuid) }}
                                </p>
                            </div>
                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20">
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
    </div>
@endsection
