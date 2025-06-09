@extends('portal.layouts.v2.app')

@section('title', 'Overzicht')
@section('header', 'Overzicht')

@section('content')
    <div class="space-y-8">
        <!-- Welcome Section -->
        <div class="relative overflow-hidden glass-card rounded-2xl p-8 shadow-2xl float-animation">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/20 to-blue-500/20"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            Welkom terug, {{ auth()->user()->minecraft_username }}! 👋
                        </h1>
                        <p class="text-gray-600 dark:text-slate-300 text-lg">
                            Klaar voor een nieuwe dag in Minetopia?
                        </p>
                    </div>
                    <div class="hidden lg:block">
                        <div class="w-20 h-20 bg-gray-100/80 dark:bg-white/10 rounded-xl backdrop-blur-sm flex items-center justify-center border border-gray-200/50 dark:border-white/20">
                            <span class="text-2xl">🏠</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Balance Card -->
            <div class="group relative glass-card rounded-xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 float-animation">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                        <span class="text-xl">💰</span>
                    </div>
                    <div class="text-emerald-500 font-semibold text-sm">ACTIEF</div>
                </div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Huidige saldo</h3>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mb-3">{{ auth()->user()->formatted_balance_with_currency }}</p>
                <div class="flex items-center text-sm">
                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">+2.5%</span>
                    <span class="text-gray-400 ml-1">deze week</span>
                </div>
            </div>

            <!-- Plots Card -->
            <div class="group relative glass-card rounded-xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 float-animation">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                        <span class="text-xl">🏘️</span>
                    </div>
                    <div class="text-orange-500 font-semibold text-sm">EIGENDOM</div>
                </div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Mijn plots</h3>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                    {{ count(auth()->user()->getPlotsAttribute()) }}
                </p>
                <div class="flex items-center text-sm">
                    <span class="text-orange-600 dark:text-orange-400 font-medium">Totaal bezit</span>
                </div>
            </div>

            <!-- Job Card -->
            <div class="group relative glass-card rounded-xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 float-animation">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <span class="text-xl">💼</span>
                    </div>
                    <div class="text-blue-500 font-semibold text-sm">WERKZAAM</div>
                </div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Huidige baan</h3>
                <p class="text-lg font-bold text-gray-900 dark:text-white mb-3">{{ auth()->user()->getPrefixAttribute() }}</p>
                <div class="flex items-center text-sm">
                    <span class="text-blue-600 dark:text-blue-400 font-medium">Actieve status</span>
                </div>
            </div>

            <!-- Fitness Card -->
            <div class="group relative glass-card rounded-xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 float-animation">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                        <span class="text-xl">❤️</span>
                    </div>
                    <div class="text-red-500 font-semibold text-sm">GEZOND</div>
                </div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Fitheid level</h3>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mb-3">{{ auth()->user()->getFitnessAttribute() }}</p>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-red-500 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, (floatval(auth()->user()->getFitnessAttribute()) / 100) * 100) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Features & Quick Actions Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Activity -->
            <div class="glass-card rounded-xl p-6 shadow-lg float-animation">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Recente activiteit</h3>
                        <p class="text-gray-500 dark:text-gray-400">Je laatste acties in het portaal</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <span class="text-xl">📊</span>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse($recentActivity as $activity)
                        <div class="flex items-center gap-4 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                <span class="text-sm">{{ $activity['icon'] }}</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $activity['title'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity['time']->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <span class="text-2xl">😴</span>
                            <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm">Nog geen recente activiteit</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="glass-card rounded-xl p-6 shadow-lg float-animation">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Snelle acties</h3>
                <div class="space-y-3">
                    <a href="{{ route('portal.bank-accounts.index') }}"
                       class="flex items-center gap-3 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 border border-transparent hover:border-emerald-200 dark:hover:border-emerald-700 transition-all duration-200 group">
                        <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center group-hover:bg-emerald-200 dark:group-hover:bg-emerald-800/50 transition-colors">
                            <span class="text-lg">💳</span>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">Bankrekeningen</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Beheer je geld</div>
                        </div>
                    </a>

                    <a href="{{ route('portal.plots.index') }}"
                       class="flex items-center gap-3 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-orange-50 dark:hover:bg-orange-900/20 border border-transparent hover:border-orange-200 dark:hover:border-orange-700 transition-all duration-200 group">
                        <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center group-hover:bg-orange-200 dark:group-hover:bg-orange-800/50 transition-colors">
                            <span class="text-lg">🗺️</span>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">Mijn Plots</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Beheer eigendom</div>
                        </div>
                    </a>

                    <a href="{{ route('portal.criminal-records.index') }}"
                       class="flex items-center gap-3 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-red-50 dark:hover:bg-red-900/20 border border-transparent hover:border-red-200 dark:hover:border-red-700 transition-all duration-200 group">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center group-hover:bg-red-200 dark:group-hover:bg-red-800/50 transition-colors">
                            <span class="text-lg">📋</span>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">Strafblad</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Bekijk overtredingen</div>
                        </div>
                    </a>

                    @if(\App\Models\PortalFeature::where('key', 'companies')->where('is_enabled', true)->exists())
                        <a href="{{ route('portal.companies.index') }}"
                           class="flex items-center gap-3 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-blue-50 dark:hover:bg-blue-900/20 border border-transparent hover:border-blue-200 dark:hover:border-blue-700 transition-all duration-200 group">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-800/50 transition-colors">
                                <span class="text-lg">🏢</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 dark:text-white">Mijn Bedrijven</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Beheer bedrijven</div>
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tips & Help Section -->
        <div class="glass-card rounded-xl p-6 relative overflow-hidden float-animation">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-indigo-500/10"></div>
            <div class="relative">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                    <span class="text-xl">💡</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Portal tips</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Financieel beheer</h4>
                            <ul class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                <li class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                    Controleer regelmatig je bankrekeningen
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                    Houd je transacties bij voor overzicht
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Eigendom beheer</h4>
                            <ul class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                <li class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                    Bekijk je plots voor belangrijke informatie
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                    Beheer toegang tot je eigendom zorgvuldig
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
@endsection
