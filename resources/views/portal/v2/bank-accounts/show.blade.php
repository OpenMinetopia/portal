@extends('portal.layouts.v2.app')

@section('title', $account['name'])
@section('header')
    <div class="flex items-center gap-4">
        <a href="{{ route('portal.bank-accounts.index') }}"
           class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors duration-200">
            <x-heroicon-s-arrow-left class="w-5 h-5"/>
            Terug naar overzicht
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-8">
        <!-- Account Header -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500/10 via-blue-500/10 to-purple-500/10 backdrop-blur-sm border border-white/20 p-8">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <span class="text-2xl">💰</span>
                        </div>
                        <div>
                            <h1 class="text-3xl font-black text-gray-900 dark:text-white mb-2">{{ $account['name'] }}</h1>
                            <div class="flex items-center gap-4">
                                <span @class([
                                    'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold',
                                    'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400' => $account['frozen'],
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400' => !$account['frozen'],
                                ])>
                                    @if($account['frozen'])
                                        <x-heroicon-s-lock-closed class="w-4 h-4" />
                                        Bevroren
                                    @else
                                        <x-heroicon-s-check-circle class="w-4 h-4" />
                                        Actief
                                    @endif
                                </span>
                                <span class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">
                                    <x-heroicon-s-identification class="w-4 h-4" />
                                    {{ $account['type'] }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-mono mt-2">{{ $account['uuid'] }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-4xl font-black text-gray-900 dark:text-white mb-2">
                            € {{ number_format($account['balance'], 2, ',', '.') }}
                        </div>
                        <p class="text-lg text-gray-600 dark:text-gray-400">Beschikbaar saldo</p>
                        @if(\App\Models\PortalFeature::isEnabled('transactions'))
                            <a href="{{ route('portal.bank-accounts.transactions.create', $account['uuid']) }}"
                               class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl hover:shadow-lg transition-all duration-200 mt-4">
                                <x-heroicon-s-arrow-path class="w-5 w-5"/>
                                Geld overmaken
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Balance -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-6 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-green-600/10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-heroicon-s-banknotes class="h-6 w-6 text-white" />
                        </div>
                        <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Saldo</h3>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">€ {{ number_format($account['balance'], 2, ',', '.') }}</p>
                </div>
            </div>

            <!-- Users Count -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-6 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-600/10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-heroicon-s-users class="h-6 w-6 text-white" />
                        </div>
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Gebruikers</h3>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ count($users) }}</p>
                </div>
            </div>

            <!-- Account Type -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-6 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 to-red-600/10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-heroicon-s-identification class="h-6 w-6 text-white" />
                        </div>
                        <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Type</h3>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $account['type'] }}</p>
                </div>
            </div>
        </div>

        <!-- Account Details & Users -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Account Info -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
                <div class="relative">
                    <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                <x-heroicon-s-identification class="h-5 w-5 text-white"/>
                            </div>
                            <h2 class="text-lg font-black text-gray-900 dark:text-white">Rekeninggegevens</h2>
                        </div>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-6">
                            <div>
                                <dt class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Type rekening</dt>
                                <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $account['type'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Status</dt>
                                <dd>
                                    <span @class([
                                        'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold',
                                        'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400' => $account['frozen'],
                                        'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400' => !$account['frozen'],
                                    ])>
                                        @if($account['frozen'])
                                            <x-heroicon-s-lock-closed class="w-4 h-4" />
                                            Bevroren
                                        @else
                                            <x-heroicon-s-check-circle class="w-4 h-4" />
                                            Actief
                                        @endif
                                    </span>
                                </dd>
                            </div>
                            <div class="pt-4 border-t border-gray-200/50 dark:border-gray-700/50">
                                <dt class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Rekeningnummer</dt>
                                <dd class="font-mono text-gray-900 dark:text-white break-all bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">{{ $account['uuid'] }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Users (spans 2 columns) -->
            <div class="lg:col-span-2 card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-600/5"></div>
                <div class="relative">
                    <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <x-heroicon-s-users class="h-5 w-5 text-white"/>
                                </div>
                                <div>
                                    <h2 class="text-lg font-black text-gray-900 dark:text-white">Gebruikers</h2>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ count($users) }} gebruikers</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @forelse($users as $user)
                                <div class="flex items-center justify-between p-4 rounded-xl bg-white/70 dark:bg-gray-700/70 backdrop-blur-sm border border-gray-200/30 dark:border-gray-600/30">
                                    <div class="flex items-center gap-4">
                                        <img class="h-12 w-12 rounded-lg bg-gray-50 dark:bg-gray-800 object-cover shadow-lg"
                                             src="{{ \App\Helpers\MinecraftHelper::getAvatar($user['uuid']) }}"
                                             alt="{{ \App\Helpers\MinecraftHelper::getName($user['uuid']) }}">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ \App\Helpers\MinecraftHelper::getName($user['uuid']) }}
                                            </p>
                                            <p class="text-xs font-mono text-gray-500 dark:text-gray-400">
                                                {{ $user['uuid'] }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex flex-col items-end gap-2">
                                            <span @class([
                                                'inline-flex items-center gap-1 rounded-lg px-3 py-1 text-xs font-semibold',
                                                'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400' => $user['owner'],
                                                'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-400' => !$user['owner'],
                                            ])>
                                                @if($user['owner'])
                                                    <x-heroicon-s-key class="w-3 h-3" />
                                                    Eigenaar
                                                @else
                                                    <x-heroicon-s-users class="w-3 h-3" />
                                                    Lid
                                                @endif
                                            </span>
                                            <span class="inline-flex items-center gap-1 rounded-lg px-3 py-1 text-xs font-semibold bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400">
                                                <x-heroicon-s-shield-check class="w-3 h-3" />
                                                {{ $user['permission'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 bg-gradient-to-r from-gray-400 to-gray-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <x-heroicon-o-users class="h-8 w-8 text-white"/>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Geen gebruikers</h3>
                                    <p class="text-gray-600 dark:text-gray-400">Er zijn geen gebruikers gekoppeld aan deze rekening.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(\App\Models\PortalFeature::isEnabled('transactions'))
            <!-- Transactions Section -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 to-pink-600/5"></div>
                <div class="relative">
                    <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <x-heroicon-s-arrow-path class="h-5 w-5 text-white"/>
                                </div>
                                <div>
                                    <h2 class="text-lg font-black text-gray-900 dark:text-white">Transacties</h2>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Alleen transacties via het portaal</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @php
                            $transactions = \App\Models\BankTransaction::where('from_account_uuid', $account['uuid'])
                                ->orWhere('to_account_uuid', $account['uuid'])
                                ->with(['fromUser', 'toUser'])
                                ->latest()
                                ->get();
                        @endphp

                        <div class="space-y-4">
                            @forelse($transactions as $transaction)
                                <div class="flex items-center justify-between p-4 rounded-xl bg-white/70 dark:bg-gray-700/70 backdrop-blur-sm border border-gray-200/30 dark:border-gray-600/30">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-gradient-to-r {{ $transaction->from_account_uuid === $account['uuid'] ? 'from-red-500 to-orange-600' : 'from-emerald-500 to-green-600' }} rounded-xl flex items-center justify-center shadow-lg">
                                            @if($transaction->from_account_uuid === $account['uuid'])
                                                <x-heroicon-s-arrow-up-right class="h-6 w-6 text-white" />
                                            @else
                                                <x-heroicon-s-arrow-down-left class="h-6 w-6 text-white" />
                                            @endif
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $transaction->description }}</h3>
                                            <div class="flex items-center gap-3 mt-1">
                                                @if($transaction->from_account_uuid === $account['uuid'])
                                                    <span class="text-sm text-red-600 dark:text-red-400">
                                                        Naar: {{ $transaction->toUser->minecraft_username }}
                                                    </span>
                                                @else
                                                    <span class="text-sm text-emerald-600 dark:text-emerald-400">
                                                        Van: {{ $transaction->fromUser->minecraft_username }}
                                                    </span>
                                                @endif
                                                <span class="text-sm text-gray-500 dark:text-gray-400">•</span>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $transaction->created_at->format('d-m-Y H:i') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span @class([
                                            'text-lg font-black',
                                            'text-red-600 dark:text-red-400' => $transaction->from_account_uuid === $account['uuid'],
                                            'text-emerald-600 dark:text-emerald-400' => $transaction->to_account_uuid === $account['uuid'],
                                        ])>
                                            {{ $transaction->from_account_uuid === $account['uuid'] ? '-' : '+' }}
                                            €{{ number_format($transaction->amount, 2, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12">
                                    <div class="w-24 h-24 bg-gradient-to-r from-gray-400 to-gray-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <x-heroicon-o-arrow-path class="h-12 w-12 text-white"/>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Geen transacties</h3>
                                    <p class="text-gray-600 dark:text-gray-400 mb-6">Er zijn nog geen transacties via het portaal uitgevoerd.</p>
                                    <a href="{{ route('portal.bank-accounts.transactions.create', $account['uuid']) }}"
                                       class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl hover:shadow-lg transition-all duration-200">
                                        <x-heroicon-s-arrow-path class="w-5 h-5"/>
                                        Eerste transactie
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection 