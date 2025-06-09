@extends('portal.layouts.v2.app')

@section('title', 'Makelaar - Plots te koop')
@section('header', 'Makelaar - Plots te koop')

@section('content')
    <div class="space-y-8">
        <!-- Welcome Header -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500/10 via-blue-500/10 to-purple-500/10 backdrop-blur-sm border border-white/20 p-8">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
            <div class="relative">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <span class="text-2xl">🏪</span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 dark:text-white">Makelaar</h1>
                        <p class="text-lg text-gray-600 dark:text-gray-300">Ontdek en koop de perfecte plot</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $listings->count() }} {{ $listings->count() === 1 ? 'plot' : 'plots' }} beschikbaar</span>
                </div>
            </div>
        </div>

        @if($listings->count() > 0)
            <!-- Listings Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($listings as $listing)
                    <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg transition-all duration-300">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
                        <div class="relative">
                            <!-- Image -->
                            <div class="relative overflow-hidden">
                                @if($listing->image_path)
                                    <img src="{{ Storage::url($listing->image_path) }}"
                                         alt="Afbeelding van {{ $listing->plot_name }}"
                                         class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105">
                                @else
                                    <div class="w-full h-48 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center">
                                        <div class="text-center">
                                            <x-heroicon-o-photo class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-2"/>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Geen afbeelding</span>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Price Badge -->
                                <div class="absolute top-4 right-4">
                                    <div class="px-3 py-1 rounded-xl bg-emerald-500/90 backdrop-blur-sm border border-emerald-400/30 text-white shadow-lg">
                                        <span class="text-lg font-black">{{ $listing->formatted_price }}</span>
                                    </div>
                                </div>

                                <!-- Instant Buy Badge -->
                                @if($listing->instant_buy)
                                    <div class="absolute top-4 left-4">
                                        <div class="px-2 py-1 rounded-lg bg-blue-500/90 backdrop-blur-sm border border-blue-400/30 text-white text-xs font-semibold">
                                            <span class="flex items-center gap-1">
                                                <x-heroicon-s-bolt class="w-3 h-3"/>
                                                Direct kopen
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="p-6">
                                <!-- Header -->
                                <div class="mb-4">
                                    <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">{{ $listing->plot_name }}</h3>
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-lg flex items-center justify-center">
                                            <x-heroicon-s-user class="h-3 w-3 text-white" />
                                        </div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ \App\Helpers\MinecraftHelper::getName($listing->seller->minecraft_uuid) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Description -->
                                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                                    {{ Str::limit($listing->description, 150) }}
                                </p>

                                <!-- Plot Info -->
                                <div class="space-y-3 mb-6">
                                    <!-- Dimensions -->
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Afmetingen</span>
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">
                                            {{ $listing->dimensions['width'] }}×{{ $listing->dimensions['length'] }}×{{ $listing->dimensions['height'] }}
                                        </span>
                                    </div>

                                    <!-- Area -->
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Oppervlakte</span>
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $listing->area }} m²</span>
                                    </div>

                                    <!-- Coordinates -->
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        <div class="flex items-center gap-1 mb-1">
                                            <x-heroicon-s-map-pin class="w-3 h-3"/>
                                            <span>Van: {{ $listing->min_x }}, {{ $listing->min_y }}, {{ $listing->min_z }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <x-heroicon-s-map-pin class="w-3 h-3"/>
                                            <span>Tot: {{ $listing->max_x }}, {{ $listing->max_y }}, {{ $listing->max_z }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <div class="border-t border-gray-200/50 dark:border-gray-700/50 pt-4">
                                    @if($listing->instant_buy)
                                        <a href="{{ route('portal.plots.listings.buy.show', $listing) }}"
                                           class="w-full inline-flex justify-center items-center gap-2 px-6 py-3 text-sm font-bold text-white bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl hover:shadow-lg transition-all duration-200">
                                            <x-heroicon-s-shopping-cart class="h-5 w-5"/>
                                            Direct Kopen
                                        </a>
                                    @else
                                        <div class="w-full inline-flex justify-center items-center gap-2 px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 rounded-xl">
                                            <x-heroicon-s-chat-bubble-left-right class="h-5 w-5"/>
                                            Contact Verkoper
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Info Section -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500/10 via-purple-500/10 to-pink-500/10 backdrop-blur-sm border border-blue-200/50 dark:border-blue-700/50 p-6 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-600/5"></div>
                <div class="relative">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                            <x-heroicon-s-information-circle class="h-6 w-6 text-white" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Tips voor het kopen van plots</h3>
                            <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                <p>• <strong>Direct kopen:</strong> Plots met dit label kunnen direct worden gekocht</p>
                                <p>• <strong>Contact verkoper:</strong> Voor andere plots moet je eerst contact opnemen</p>
                                <p>• <strong>Locatie:</strong> Bekijk de coördinaten om de locatie te controleren</p>
                                <p>• <strong>Afmetingen:</strong> Let op de grootte en vorm van het plot</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-12 shadow-lg text-center">
                <div class="absolute inset-0 bg-gradient-to-br from-gray-500/5 to-gray-600/5"></div>
                <div class="relative">
                    <div class="w-24 h-24 bg-gradient-to-r from-gray-400 to-gray-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <x-heroicon-o-home class="h-12 w-12 text-white"/>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Geen plots te koop</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Er staan momenteel geen plots te koop. Check later nog eens of overweeg je eigen plot te verkopen.
                    </p>
                    <a href="{{ route('portal.plots.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 rounded-xl hover:bg-white dark:hover:bg-gray-800 transition-all duration-200">
                        <x-heroicon-s-arrow-left class="w-4 h-4"/>
                        Bekijk mijn plots
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection 