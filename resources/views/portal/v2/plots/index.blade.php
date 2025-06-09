@extends('portal.layouts.v2.app')

@section('title', 'Mijn plots')
@section('header', 'Mijn plots')

@section('content')
    <div class="space-y-8">
        <!-- Welcome Hero -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500/10 via-purple-500/10 to-emerald-500/10 backdrop-blur-sm border border-white/20 p-8">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-600/5"></div>
            <div class="relative">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <span class="text-2xl">🗺️</span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 dark:text-white">Mijn Plots</h1>
                        <p class="text-lg text-gray-600 dark:text-gray-300">Beheer en bekijk al je plots op één plek</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Plots -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-6 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-600/10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-heroicon-s-map class="h-6 w-6 text-white" />
                        </div>
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Totale Plots</h3>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ count($plots) }}</p>
                </div>
            </div>

            <!-- Owner Plots -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-6 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-blue-600/10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-heroicon-s-key class="h-6 w-6 text-white" />
                        </div>
                        <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Eigenaar</h3>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ collect($plots)->where('permission', 'OWNER')->count() }}</p>
                </div>
            </div>

            <!-- Member Plots -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-6 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 to-red-600/10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-heroicon-s-users class="h-6 w-6 text-white" />
                        </div>
                        <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Lid</h3>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ collect($plots)->where('permission', 'MEMBER')->count() }}</p>
                </div>
            </div>

            <!-- Total Area -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-6 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-pink-600/10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-heroicon-s-calculator class="h-6 w-6 text-white" />
                        </div>
                        <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Totaal gebied</h3>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format(collect($plots)->sum(function($plot) { return abs(($plot['location']['max']['x'] - $plot['location']['min']['x']) * ($plot['location']['max']['z'] - $plot['location']['min']['z'])); })) }}m²</p>
                </div>
            </div>
        </div>

        <!-- Plots Grid -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-600/5"></div>
            <div class="relative">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-black text-gray-900 dark:text-white">Plots Overzicht</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Bekijk en beheer al je plots</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></div>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ count($plots) }} plots</span>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    @forelse($plots as $plot)
                        <div class="card-hover relative overflow-hidden rounded-xl bg-white/70 dark:bg-gray-700/70 backdrop-blur-sm border border-gray-200/30 dark:border-gray-600/30 p-6 mb-4 transition-all duration-300">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 to-purple-600/5"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                            <span class="text-white font-bold text-sm">{{ substr($plot['name'], 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $plot['name'] }}</h3>
                                            <div class="flex items-center gap-3 mt-1">
                                                <span @class([
                                                    'inline-flex items-center gap-1 rounded-lg px-3 py-1 text-xs font-semibold',
                                                    'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400' => $plot['permission'] === 'OWNER',
                                                    'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-400' => $plot['permission'] === 'MEMBER',
                                                ])>
                                                    @if($plot['permission'] === 'OWNER')
                                                        <x-heroicon-s-key class="w-3 h-3" />
                                                    @else
                                                        <x-heroicon-s-users class="w-3 h-3" />
                                                    @endif
                                                    {{ $plot['permission'] }}
                                                </span>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $plot['location']['min']['x'] }}, {{ $plot['location']['min']['z'] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ number_format(abs(($plot['location']['max']['x'] - $plot['location']['min']['x']) * ($plot['location']['max']['z'] - $plot['location']['min']['z']))) }}m²
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Oppervlakte</p>
                                        </div>
                                        <a href="{{ route('portal.plots.show', $plot['name']) }}"
                                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl hover:shadow-lg transition-all duration-200">
                                            <x-heroicon-s-eye class="w-4 h-4"/>
                                            Bekijken
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-24 h-24 bg-gradient-to-r from-gray-400 to-gray-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <x-heroicon-o-map class="h-12 w-12 text-white"/>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Geen plots</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">Je hebt nog geen plots. Ga naar de server om plots te claimen!</p>
                            <div class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 rounded-xl">
                                <span class="text-lg">🎮</span>
                                Ga naar de server
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection 