@extends('portal.layouts.v2.app')

@section('title', 'Mijn bedrijven')
@section('header', 'Mijn bedrijven')

@section('content')
    <div class="space-y-8">
        <!-- Welcome Hero -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500/10 via-purple-500/10 to-emerald-500/10 backdrop-blur-sm border border-white/20 p-8">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-600/5"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <span class="text-2xl">🏢</span>
                        </div>
                        <div>
                            <h1 class="text-3xl font-black text-gray-900 dark:text-white">Mijn Bedrijven</h1>
                            <p class="text-lg text-gray-600 dark:text-gray-300">Beheer je bedrijven en aanvragen</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('portal.companies.register') }}"
                           class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl hover:shadow-lg transition-all duration-200">
                            <x-heroicon-m-plus class="h-5 w-5"/>
                            Nieuw Bedrijf
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Active Companies -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-6 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-green-600/10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-heroicon-s-building-office class="h-6 w-6 text-white" />
                        </div>
                        <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Actieve Bedrijven</h3>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $companies->where('is_active', true)->count() }}</p>
                </div>
            </div>

            <!-- Pending Requests -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-6 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/10 to-orange-600/10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-heroicon-s-clock class="h-6 w-6 text-white" />
                        </div>
                        <div class="w-2 h-2 bg-yellow-500 rounded-full {{ $requests->where('status', 'pending')->count() > 0 ? 'animate-pulse' : '' }}"></div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">In Behandeling</h3>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $requests->where('status', 'pending')->count() }}</p>
                </div>
            </div>

            <!-- Total Requests -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-6 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-600/10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-heroicon-s-document-text class="h-6 w-6 text-white" />
                        </div>
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Totaal Aanvragen</h3>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $requests->count() }}</p>
                </div>
            </div>

            <!-- Approved Companies -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-6 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-pink-600/10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-heroicon-s-check-circle class="h-6 w-6 text-white" />
                        </div>
                        <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Goedgekeurd</h3>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $requests->where('status', 'approved')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Companies Section -->
        @if($companies->count() > 0)
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-green-600/5"></div>
                <div class="relative">
                    <!-- Header -->
                    <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-black text-gray-900 dark:text-white">Mijn Bedrijven</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Overzicht van al je bedrijven</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $companies->count() }} bedrijven</span>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($companies as $company)
                                <div class="card-hover relative overflow-hidden rounded-xl bg-white/70 dark:bg-gray-700/70 backdrop-blur-sm border border-gray-200/30 dark:border-gray-600/30 p-6 transition-all duration-300">
                                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/5 to-green-600/5"></div>
                                    <div class="relative">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                                                    <span class="text-white font-bold text-sm">{{ substr($company->name, 0, 2) }}</span>
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $company->name }}</h3>
                                                    <div class="flex items-center gap-3 mt-1">
                                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $company->type->name }}</span>
                                                        <span class="text-sm text-gray-500 dark:text-gray-400">•</span>
                                                        <span @class([
                                                            'inline-flex items-center gap-1 rounded-lg px-3 py-1 text-xs font-semibold',
                                                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400' => $company->is_active,
                                                            'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400' => !$company->is_active,
                                                        ])>
                                                            @if($company->is_active)
                                                                <x-heroicon-s-check-circle class="w-3 h-3" />
                                                                Actief
                                                            @else
                                                                <x-heroicon-s-x-circle class="w-3 h-3" />
                                                                Inactief
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="text-right">
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">KvK: {{ $company->kvk_number }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $company->created_at->format('d-m-Y') }}</p>
                                                </div>
                                                <a href="{{ route('portal.companies.show', $company) }}"
                                                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-green-600 rounded-xl hover:shadow-lg transition-all duration-200">
                                                    <x-heroicon-s-eye class="w-4 h-4"/>
                                                    Bekijken
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Requests Section -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-600/5"></div>
            <div class="relative">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-black text-gray-900 dark:text-white">Aanvragen</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Status van je bedrijfsaanvragen</p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($requests->where('status', 'pending')->count() > 0)
                                <div class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></div>
                                <span class="text-sm font-medium text-yellow-600 dark:text-yellow-400">{{ $requests->where('status', 'pending')->count() }} in behandeling</span>
                            @else
                                <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $requests->count() }} aanvragen</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    @forelse($requests as $request)
                        <div class="card-hover relative overflow-hidden rounded-xl bg-white/70 dark:bg-gray-700/70 backdrop-blur-sm border border-gray-200/30 dark:border-gray-600/30 p-6 mb-4 transition-all duration-300">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 to-purple-600/5"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                            <span class="text-white font-bold text-sm">{{ substr($request->name, 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $request->name }}</h3>
                                            <div class="flex items-center gap-3 mt-1">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $request->type->name }}</span>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">•</span>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $request->created_at->format('d-m-Y H:i') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span @class([
                                            'inline-flex items-center gap-1 rounded-lg px-3 py-1 text-xs font-semibold',
                                            'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400' => $request->isPending(),
                                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400' => $request->isApproved(),
                                            'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400' => $request->isDenied(),
                                        ])>
                                            @if($request->isPending())
                                                <x-heroicon-s-clock class="w-3 h-3" />
                                            @elseif($request->isApproved())
                                                <x-heroicon-s-check-circle class="w-3 h-3" />
                                            @else
                                                <x-heroicon-s-x-circle class="w-3 h-3" />
                                            @endif
                                            {{ $request->getStatusText() }}
                                        </span>
                                        <a href="{{ route('portal.companies.request-details', $request) }}"
                                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl hover:shadow-lg transition-all duration-200">
                                            <x-heroicon-s-eye class="w-4 h-4"/>
                                            Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-24 h-24 bg-gradient-to-r from-gray-400 to-gray-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <x-heroicon-o-document-text class="h-12 w-12 text-white"/>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Geen aanvragen</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">Je hebt nog geen bedrijfsaanvragen ingediend.</p>
                            <a href="{{ route('portal.companies.register') }}"
                               class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl hover:shadow-lg transition-all duration-200">
                                <x-heroicon-m-plus class="h-5 w-5"/>
                                Eerste bedrijf aanvragen
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        @if($companies->count() === 0 && $requests->count() === 0)
            <!-- Getting Started -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500/10 via-blue-500/10 to-purple-500/10 backdrop-blur-sm border border-emerald-200/50 dark:border-emerald-700/50 p-8 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
                <div class="relative text-center">
                    <div class="w-24 h-24 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <x-heroicon-o-building-office class="h-12 w-12 text-white"/>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Start je eerste bedrijf! 🚀</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-2xl mx-auto">
                        Welkom bij het bedrijvenportaal! Hier kun je je eigen bedrijf registreren en beheren. 
                        Begin vandaag nog met ondernemen in Minetopia.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('portal.companies.register') }}"
                           class="inline-flex items-center gap-2 px-8 py-4 text-lg font-semibold text-white bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl hover:shadow-lg transition-all duration-200">
                            <x-heroicon-m-plus class="h-6 w-6"/>
                            Bedrijf Registreren
                        </a>
                        <a href="{{ route('portal.companies.registry') }}"
                           class="inline-flex items-center gap-2 px-8 py-4 text-lg font-semibold text-gray-700 dark:text-gray-300 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 rounded-xl hover:bg-white dark:hover:bg-gray-800 transition-all duration-200">
                            <x-heroicon-m-magnifying-glass class="h-6 w-6"/>
                            Bedrijvenregister
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection 