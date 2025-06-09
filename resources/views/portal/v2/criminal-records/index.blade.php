@extends('portal.layouts.v2.app')

@section('title', 'Mijn strafblad')
@section('header', 'Mijn strafblad')

@section('content')
    <div class="space-y-8">
        <!-- Welcome Hero -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-gradient-to-br from-red-500/10 via-orange-500/10 to-yellow-500/10 backdrop-blur-sm border border-white/20 p-8">
            <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 to-orange-600/5"></div>
            <div class="relative">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-red-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <span class="text-2xl">📋</span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 dark:text-white">Mijn Strafblad</h1>
                        <p class="text-lg text-gray-600 dark:text-gray-300">Overzicht van je overtredingen en status</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Total Records -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-6 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-red-500/10 to-orange-600/10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-heroicon-s-document-text class="h-6 w-6 text-white" />
                        </div>
                        <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Totale Overtredingen</h3>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ count($records) }}</p>
                </div>
            </div>

            <!-- Status -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-6 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br {{ empty($records) ? 'from-emerald-500/10 to-green-600/10' : 'from-red-500/10 to-orange-600/10' }}"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r {{ empty($records) ? 'from-emerald-500 to-green-600' : 'from-red-500 to-orange-600' }} rounded-xl flex items-center justify-center shadow-lg">
                            @if(empty($records))
                                <x-heroicon-s-shield-check class="h-6 w-6 text-white" />
                            @else
                                <x-heroicon-s-shield-exclamation class="h-6 w-6 text-white" />
                            @endif
                        </div>
                        <div class="w-2 h-2 {{ empty($records) ? 'bg-emerald-500' : 'bg-red-500' }} rounded-full {{ empty($records) ? '' : 'animate-pulse' }}"></div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Status</h3>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">
                        {{ empty($records) ? 'Schoon' : 'Overtredingen' }}
                    </p>
                </div>
            </div>

            <!-- Latest Record -->
            @if(!empty($records))
                <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-6 shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-pink-600/10"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                                <x-heroicon-s-clock class="h-6 w-6 text-white" />
                            </div>
                            <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Laatste Overtreding</h3>
                        <p class="text-lg font-black text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::createFromTimestampMs(collect($records)->max('date'))->format('d-m-Y') }}
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Records List -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 to-orange-600/5"></div>
            <div class="relative">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-black text-gray-900 dark:text-white">Overtredingen</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Overzicht van al je overtredingen</p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if(empty($records))
                                <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                                <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">Schoon strafblad</span>
                            @else
                                <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                                <span class="text-sm font-medium text-red-600 dark:text-red-400">{{ count($records) }} overtredingen</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    @forelse($records as $record)
                        <div class="card-hover relative overflow-hidden rounded-xl bg-white/70 dark:bg-gray-700/70 backdrop-blur-sm border border-gray-200/30 dark:border-gray-600/30 p-6 mb-4 transition-all duration-300">
                            <div class="absolute inset-0 bg-gradient-to-r from-red-500/5 to-orange-600/5"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                                            <x-heroicon-s-exclamation-triangle class="h-6 w-6 text-white" />
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $record['reason'] }}</h3>
                                            <div class="flex items-center gap-4 mt-1">
                                                <div class="flex items-center gap-2">
                                                    <img src="{{ $record['officer_skin_url'] }}"
                                                         alt="{{ $record['officer_name'] }}"
                                                         class="h-6 w-6 rounded-lg shadow-sm"
                                                         onerror="this.src='https://crafatar.com/avatars/steve'">
                                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $record['officer_name'] }}</span>
                                                </div>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">•</span>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ \Carbon\Carbon::createFromTimestampMs($record['date'])->format('d-m-Y H:i') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center gap-1 rounded-lg px-3 py-1 text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400">
                                            <x-heroicon-s-exclamation-triangle class="w-3 h-3" />
                                            Overtreding
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-24 h-24 bg-gradient-to-r from-emerald-400 to-green-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <x-heroicon-o-shield-check class="h-12 w-12 text-white"/>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Schoon strafblad! 🎉</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">Je hebt geen overtredingen. Blijf zo doorgaan!</p>
                            <div class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/20 rounded-xl">
                                <x-heroicon-s-shield-check class="w-4 h-4" />
                                Uitstekend gedrag
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        @if(!empty($records))
            <!-- Additional Info -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-gradient-to-br from-yellow-500/10 via-orange-500/10 to-red-500/10 backdrop-blur-sm border border-yellow-200/50 dark:border-yellow-700/50 p-6 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/5 to-orange-600/5"></div>
                <div class="relative">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                            <x-heroicon-s-information-circle class="h-6 w-6 text-white" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Belangrijke informatie</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                Dit strafblad toont al je geregistreerde overtredingen binnen Minetopia. 
                                Als je vragen hebt over een specifieke overtreding, neem dan contact op met de politie of stuur een ticket in de Discord.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection 