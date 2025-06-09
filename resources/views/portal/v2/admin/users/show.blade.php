@extends('portal.layouts.v2.app')

@section('title', $user->minecraft_username)
@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-black text-gray-900 dark:text-white">{{ $user->minecraft_username }}</h1>
        <a href="{{ route('portal.admin.users.edit', $user) }}" 
           class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 px-4 py-2 text-sm font-bold text-white shadow-lg hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 transform hover:scale-105">
            <x-heroicon-s-pencil class="h-4 w-4"/>
            Bewerken
        </a>
    </div>
@endsection

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

        <!-- Account Info Section -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-indigo-600/5"></div>
            <div class="relative">
                <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                            <x-heroicon-s-user class="h-5 w-5 text-white" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Account Informatie</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                        <div class="p-4 rounded-xl bg-blue-50/50 dark:bg-blue-500/10 border border-blue-200/50 dark:border-blue-500/30">
                            <dt class="text-sm font-bold text-blue-700 dark:text-blue-300 uppercase tracking-wider">Email</dt>
                            <dd class="mt-1 text-sm text-blue-900 dark:text-blue-100 font-mono">{{ $user->email }}</dd>
                        </div>
                        <div class="p-4 rounded-xl bg-green-50/50 dark:bg-green-500/10 border border-green-200/50 dark:border-green-500/30">
                            <dt class="text-sm font-bold text-green-700 dark:text-green-300 uppercase tracking-wider">Account Aangemaakt</dt>
                            <dd class="mt-1 text-sm text-green-900 dark:text-green-100 font-mono">{{ $user->created_at->format('d-m-Y H:i') }}</dd>
                        </div>
                        <div class="sm:col-span-2 p-4 rounded-xl bg-purple-50/50 dark:bg-purple-500/10 border border-purple-200/50 dark:border-purple-500/30">
                            <dt class="text-sm font-bold text-purple-700 dark:text-purple-300 uppercase tracking-wider mb-2">Rollen</dt>
                            <dd class="flex flex-wrap gap-2">
                                @foreach($user->roles as $role)
                                    <span class="inline-flex items-center rounded-xl px-3 py-1 text-xs font-bold bg-purple-100/80 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300 border border-purple-200/50 dark:border-purple-500/30">
                                        <x-heroicon-s-key class="w-3 h-3 mr-1" />
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Left Column - Main Info -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Player Info Card -->
                <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-green-600/5"></div>
                    <div class="relative">
                        <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                            <div class="flex items-center gap-4">
                                <img src="https://crafatar.com/avatars/{{ $user->minecraft_uuid }}?overlay=true&size=128"
                                     alt="{{ $user->minecraft_username }}"
                                     class="h-16 w-16 rounded-2xl bg-gray-50 dark:bg-gray-800 shadow-lg">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $user->minecraft_username }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->name }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div class="p-4 rounded-xl bg-orange-50/50 dark:bg-orange-500/10 border border-orange-200/50 dark:border-orange-500/30">
                                    <dt class="text-sm font-bold text-orange-700 dark:text-orange-300 uppercase tracking-wider">Huidige baan</dt>
                                    <dd class="mt-1 text-lg font-bold text-orange-900 dark:text-orange-100">{{ $user->prefix }}</dd>
                                </div>
                                <div class="p-4 rounded-xl bg-blue-50/50 dark:bg-blue-500/10 border border-blue-200/50 dark:border-blue-500/30">
                                    <dt class="text-sm font-bold text-blue-700 dark:text-blue-300 uppercase tracking-wider">Level</dt>
                                    <dd class="mt-1 text-lg font-bold text-blue-900 dark:text-blue-100">{{ $user->level }}</dd>
                                </div>
                                <div class="p-4 rounded-xl bg-green-50/50 dark:bg-green-500/10 border border-green-200/50 dark:border-green-500/30">
                                    <dt class="text-sm font-bold text-green-700 dark:text-green-300 uppercase tracking-wider">Fitheid</dt>
                                    <dd class="mt-1 text-lg font-bold text-green-900 dark:text-green-100">{{ $user->fitness }}</dd>
                                </div>
                                <div class="p-4 rounded-xl bg-emerald-50/50 dark:bg-emerald-500/10 border border-emerald-200/50 dark:border-emerald-500/30">
                                    <dt class="text-sm font-bold text-emerald-700 dark:text-emerald-300 uppercase tracking-wider">Bank Saldo</dt>
                                    <dd class="mt-1 text-lg font-bold text-emerald-900 dark:text-emerald-100">{{ $user->formatted_balance_with_currency }}</dd>
                                </div>
                                <div class="sm:col-span-2 p-4 rounded-xl bg-gray-50/50 dark:bg-gray-500/10 border border-gray-200/50 dark:border-gray-500/30">
                                    <dt class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3">Beschikbare Prefixes</dt>
                                    <dd>
                                        <div class="flex flex-wrap gap-2">
                                            @forelse($user->available_prefixes as $prefix)
                                                <span @class([
                                                    'inline-flex items-center rounded-xl px-3 py-1 text-xs font-bold border',
                                                    'bg-indigo-100/80 text-indigo-700 border-indigo-200/50' => $prefix['prefix'] === $user->prefix,
                                                    'bg-gray-100/80 text-gray-700 border-gray-200/50' => $prefix['prefix'] !== $user->prefix,
                                                ])>
                                                    {{ $prefix['prefix'] }}
                                                    @if($prefix['prefix'] === $user->prefix)
                                                        <span class="ml-1 text-xs text-indigo-500">(Actief)</span>
                                                    @endif
                                                    @if($prefix['expires_at'] !== -1)
                                                        <span class="ml-1 text-xs text-gray-500">
                                                            (Verloopt: {{ \Carbon\Carbon::createFromTimestamp($prefix['expires_at'])->format('d-m-Y') }})
                                                        </span>
                                                    @endif
                                                </span>
                                            @empty
                                                <span class="text-sm text-gray-600 dark:text-gray-400">Geen prefixes beschikbaar</span>
                                            @endforelse
                                        </div>
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Criminal Records -->
                <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 to-pink-600/5"></div>
                    <div class="relative">
                        <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-red-500 to-pink-600 rounded-xl flex items-center justify-center">
                                        <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-white" />
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Strafblad</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Overzicht van alle overtredingen</p>
                                    </div>
                                </div>
                                <span @class([
                                    'inline-flex items-center rounded-xl px-3 py-1 text-xs font-bold border',
                                    'bg-emerald-100/80 text-emerald-700 border-emerald-200/50' => count($user->criminal_records) === 0,
                                    'bg-red-100/80 text-red-700 border-red-200/50' => count($user->criminal_records) > 0,
                                ])>
                                    {{ count($user->criminal_records) }} overtredingen
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            @forelse($user->criminal_records as $record)
                                <div @class([
                                    'flex items-start space-x-4 py-4 px-4 rounded-xl bg-red-50/30 dark:bg-red-500/10 border border-red-200/30 dark:border-red-500/20',
                                    'mt-4' => !$loop->first
                                ])>
                                    <div class="flex-shrink-0 pt-1">
                                        <x-heroicon-s-exclamation-circle class="h-6 w-6 text-red-500 dark:text-red-400"/>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold text-red-900 dark:text-red-100">
                                            {{ $record['reason'] }}
                                        </p>
                                        <div class="mt-2 flex items-center gap-4 text-sm text-red-700 dark:text-red-300">
                                            <span class="font-mono">{{ \Carbon\Carbon::parse($record['date'])->format('d-m-Y H:i') }}</span>
                                            <div class="flex items-center gap-2">
                                                <img src="{{ $record['officer_skin_url'] }}"
                                                     alt="{{ $record['officer_name'] }}"
                                                     class="h-5 w-5 rounded-full border border-red-200 dark:border-red-400"
                                                     onerror="this.src='https://crafatar.com/avatars/steve'">
                                                <span>Agent: {{ $record['officer_name'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <x-heroicon-o-shield-check class="h-8 w-8 text-emerald-500 dark:text-emerald-400"/>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Geen overtredingen</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Deze speler heeft een schoon strafblad.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Bank Accounts -->
                <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-500/5 to-emerald-600/5"></div>
                    <div class="relative">
                        <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                                        <x-heroicon-s-banknotes class="h-5 w-5 text-white" />
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Bankrekeningen</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Overzicht van alle bankrekeningen</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center rounded-xl px-3 py-1 text-xs font-bold bg-blue-100/80 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 border border-blue-200/50 dark:border-blue-500/30">
                                    {{ count($user->bank_accounts) }} rekeningen
                                </span>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @forelse($user->bank_accounts as $account)
                                    <div class="flex items-center justify-between p-4 rounded-xl bg-emerald-50/50 dark:bg-emerald-500/10 border border-emerald-200/50 dark:border-emerald-500/30">
                                        <div>
                                            <p class="text-sm font-bold text-emerald-900 dark:text-emerald-100">
                                                {{ $account['name'] }}
                                                @if($account['type'] === 'PRIVATE')
                                                    <span class="ml-2 inline-flex items-center rounded-xl px-2 py-1 text-xs font-bold bg-purple-100/80 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300 border border-purple-200/50 dark:border-purple-500/30">
                                                        Privé
                                                    </span>
                                                @endif
                                            </p>
                                            <p class="mt-1 text-xs text-emerald-700 dark:text-emerald-300 font-mono">{{ $account['uuid'] }}</p>
                                        </div>
                                        <p class="text-lg font-bold text-emerald-900 dark:text-emerald-100">
                                            € {{ number_format($account['balance'], 2, ',', '.') }}
                                        </p>
                                    </div>
                                @empty
                                    <div class="text-center py-8">
                                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                            <x-heroicon-o-banknotes class="h-8 w-8 text-gray-400 dark:text-gray-500"/>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Geen bankrekeningen</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Deze speler heeft geen bankrekeningen.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Quick Stats -->
            <div class="space-y-8">
                <!-- Player Stats -->
                <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-600/5"></div>
                    <div class="relative p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <x-heroicon-s-chart-bar class="h-4 w-4 text-white" />
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Speler Stats</h3>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Plots Eigendom</span>
                                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ count($user->owned_plots ?? []) }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Plots Toegang</span>
                                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ count($user->accessible_plots ?? []) }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Online Status</span>
                                <span class="inline-flex items-center gap-2 text-sm font-bold">
                                    <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                    <span class="text-emerald-600 dark:text-emerald-400">Online</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 