@extends('portal.layouts.v2.app')

@section('title', 'Portal Instellingen')
@section('header', 'Portal Instellingen')

@section('content')
    <div class="space-y-8">
        <!-- Back Button -->
        <div>
            <a href="{{ route('portal.admin.users.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-100/80 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 hover:bg-gray-200/80 dark:hover:bg-gray-600/50 border border-gray-200/50 dark:border-gray-600/50 transition-all duration-200 backdrop-blur-sm">
                <x-heroicon-s-arrow-left class="w-4 h-4" />
                Terug naar overzicht
            </a>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Left Column - Main Settings -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Portal Features Card -->
                <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-indigo-600/5"></div>
                    <div class="relative">
                        <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                                    <x-heroicon-s-adjustments-horizontal class="h-5 w-5 text-white" />
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Portal Functies</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Beheer welke functies beschikbaar zijn in het portal</p>
                                </div>
                            </div>
                        </div>
                        
                        <form action="{{ route('portal.admin.settings.update-features') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="p-6 space-y-4">
                                @foreach($features as $feature)
                                    <div class="relative flex items-start py-3 px-4 rounded-xl bg-gray-50/50 dark:bg-gray-700/30 border border-gray-200/30 dark:border-gray-600/30 transition-all duration-200 hover:bg-gray-100/50 dark:hover:bg-gray-600/30">
                                        <div class="flex h-6 items-center">
                                            <input type="checkbox" 
                                                   name="features[{{ $feature->key }}]" 
                                                   id="feature_{{ $feature->key }}"
                                                   @checked($feature->is_enabled)
                                                   class="h-5 w-5 rounded-lg border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 transition-all duration-200">
                                        </div>
                                        <div class="ml-4">
                                            <label for="feature_{{ $feature->key }}" class="text-sm font-bold text-gray-900 dark:text-white">
                                                {{ $feature->name }}
                                            </label>
                                            @if($feature->description)
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $feature->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="p-6 bg-gray-50/50 dark:bg-gray-800/50 border-t border-gray-200/50 dark:border-gray-700/50">
                                <div class="flex justify-end">
                                    <button type="submit" 
                                            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-green-600 px-6 py-3 text-sm font-bold text-white shadow-lg hover:from-emerald-600 hover:to-green-700 transition-all duration-200 transform hover:scale-105">
                                        <x-heroicon-s-check class="h-4 w-4" />
                                        Instellingen Opslaan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Plugin Integration Settings -->
                <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-green-600/5"></div>
                    <div class="relative">
                        <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-green-600 rounded-xl flex items-center justify-center">
                                    <x-heroicon-s-server class="h-5 w-5 text-white" />
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Plugin Integratie</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Configureer de verbinding met de Minecraft server</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div class="p-4 rounded-xl bg-emerald-50/50 dark:bg-emerald-500/10 border border-emerald-200/50 dark:border-emerald-500/30">
                                    <dt class="text-sm font-bold text-emerald-700 dark:text-emerald-300 uppercase tracking-wider">API Status</dt>
                                    <dd class="mt-2">
                                        <span class="inline-flex items-center gap-2 rounded-xl bg-emerald-100/80 px-3 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-200/50 dark:border-emerald-500/30">
                                            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                            Verbonden
                                        </span>
                                    </dd>
                                </div>
                                <div class="p-4 rounded-xl bg-blue-50/50 dark:bg-blue-500/10 border border-blue-200/50 dark:border-blue-500/30">
                                    <dt class="text-sm font-bold text-blue-700 dark:text-blue-300 uppercase tracking-wider">Laatste Synchronisatie</dt>
                                    <dd class="mt-2 text-sm text-blue-900 dark:text-blue-100 font-mono">{{ now()->subMinutes(5)->diffForHumans() }}</dd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permit Settings -->
                @if(\App\Models\PortalFeature::where('key', 'permits')->where('is_enabled', true)->exists())
                    <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 to-pink-600/5"></div>
                        <div class="relative">
                            <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                                        <x-heroicon-s-document-text class="h-5 w-5 text-white" />
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Vergunningen Instellingen</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Configureer de financiële instellingen voor vergunningen</p>
                                    </div>
                                </div>
                            </div>
                            
                            <form action="{{ route('portal.admin.settings.update-permit-settings') }}" method="POST" class="p-6">
                                @csrf
                                @method('PUT')
                                
                                <div class="space-y-6">
                                    <!-- Bank Account Selection -->
                                    <div>
                                        <label for="payout_bank_account_uuid" 
                                               class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                            Uitbetalingsrekening
                                        </label>
                                        <select name="payout_bank_account_uuid" 
                                                id="payout_bank_account_uuid"
                                                class="block w-full rounded-xl border-gray-300 dark:border-gray-600 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white px-4 py-3 shadow-sm transition-all duration-200">
                                            <option value="">Selecteer een bankrekening</option>
                                            @foreach(auth()->user()->bank_accounts as $account)
                                                @if($account['type'] !== 'PRIVATE')
                                                    <option value="{{ $account['uuid'] }}" 
                                                            @if($permitSettings->payout_bank_account_uuid === $account['uuid']) selected @endif>
                                                        {{ $account['name'] }} (€ {{ number_format($account['balance'], 2, ',', '.') }})
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                            Selecteer de overheidsrekening waar alle vergunningsgelden op worden gestort. 
                                            Let op: alleen overheidsrekeningen worden getoond.
                                        </p>
                                    </div>

                                    <div class="p-4 rounded-xl bg-blue-50/50 dark:bg-blue-500/10 border border-blue-200/50 dark:border-blue-500/30">
                                        <div class="flex gap-3">
                                            <div class="flex-shrink-0">
                                                <x-heroicon-s-information-circle class="h-5 w-5 text-blue-500"/>
                                            </div>
                                            <div>
                                                <h3 class="text-sm font-bold text-blue-900 dark:text-blue-200">
                                                    Alleen overheidsrekeningen
                                                </h3>
                                                <p class="mt-1 text-sm text-blue-800 dark:text-blue-300">
                                                    Als administrator met OP-rechten op de server zie je alle overheidsrekeningen. 
                                                    Deze rekening wordt gebruikt voor alle financiële transacties rondom vergunningen, 
                                                    zoals betalingen en terugstortingen.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <button type="submit" 
                                            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-3 text-sm font-bold text-white shadow-lg hover:from-purple-600 hover:to-pink-700 transition-all duration-200 transform hover:scale-105">
                                        <x-heroicon-s-check class="h-4 w-4"/>
                                        Instellingen Opslaan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Company Settings -->
                @if(\App\Models\PortalFeature::where('key', 'companies')->where('is_enabled', true)->exists())
                    <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                        <div class="absolute inset-0 bg-gradient-to-br from-orange-500/5 to-red-600/5"></div>
                        <div class="relative">
                            <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center">
                                        <x-heroicon-s-building-office class="h-5 w-5 text-white" />
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Bedrijven Instellingen</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Configureer de financiële instellingen voor bedrijfsaanvragen</p>
                                    </div>
                                </div>
                            </div>
                            
                            <form action="{{ route('portal.admin.settings.update-company-settings') }}" method="POST" class="p-6">
                                @csrf
                                @method('PUT')
                                
                                <div class="space-y-6">
                                    <!-- Bank Account Selection -->
                                    <div>
                                        <label for="company_payout_bank_account_uuid" 
                                               class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                            Uitbetalingsrekening
                                        </label>
                                        <select name="payout_bank_account_uuid" 
                                                id="company_payout_bank_account_uuid"
                                                class="block w-full rounded-xl border-gray-300 dark:border-gray-600 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white px-4 py-3 shadow-sm transition-all duration-200">
                                            <option value="">Selecteer een bankrekening</option>
                                            @foreach(auth()->user()->bank_accounts as $account)
                                                @if($account['type'] !== 'PRIVATE')
                                                    <option value="{{ $account['uuid'] }}" 
                                                            @if($companySettings->payout_bank_account_uuid === $account['uuid']) selected @endif>
                                                        {{ $account['name'] }} (€ {{ number_format($account['balance'], 2, ',', '.') }})
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                            Selecteer de overheidsrekening waar alle bedrijfsaanvraag gelden op worden gestort. 
                                            Let op: alleen overheidsrekeningen worden getoond.
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <button type="submit" 
                                            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-orange-500 to-red-600 px-6 py-3 text-sm font-bold text-white shadow-lg hover:from-orange-600 hover:to-red-700 transition-all duration-200 transform hover:scale-105">
                                        <x-heroicon-s-check class="h-4 w-4"/>
                                        Instellingen Opslaan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - Quick Stats -->
            <div class="space-y-8">
                <!-- System Stats -->
                <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-600/5"></div>
                    <div class="relative p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <x-heroicon-s-chart-bar class="h-4 w-4 text-white" />
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Systeem Overzicht</h3>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Actieve Gebruikers</span>
                                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ \App\Models\User::count() }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Portal Versie</span>
                                <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">V2.0</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Laravel Versie</span>
                                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ app()->version() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection 