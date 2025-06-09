@extends('portal.layouts.v2.app')

@section('title', 'Mijn bankrekeningen')
@section('header', 'Mijn bankrekeningen')

@section('content')
    <div class="space-y-8">
        <!-- Header Stats -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <!-- Total Accounts -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <span class="text-xl">🏦</span>
                    </div>
                    <div class="text-blue-500 font-semibold text-sm">TOTAAL</div>
                </div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Bankrekeningen</h3>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ count($accounts) }}</p>
            </div>

            <!-- Total Balance -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                        <span class="text-xl">💰</span>
                    </div>
                    <div class="text-emerald-500 font-semibold text-sm">SALDO</div>
                </div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Totaal saldo</h3>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    € {{ number_format(collect($accounts)->sum('balance'), 2, ',', '.') }}
                </p>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <span class="text-xl">⚡</span>
                    </div>
                    <div class="text-purple-500 font-semibold text-sm">ACTIES</div>
                </div>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Snelle acties</h3>
                <div class="flex items-center gap-2">
                    <button class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        <span class="mr-1">🔄</span> Vernieuwen
                    </button>
                </div>
            </div>
        </div>

        <!-- Bank Accounts Grid -->
        @if(count($accounts) > 0)
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($accounts as $account)
                    <div class="group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                        <!-- Account Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                <span class="text-xl">💳</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ $account['type'] }}
                                </span>
                            </div>
                        </div>

                        <!-- Account Info -->
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                                {{ $account['name'] }}
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-mono">
                                {{ $account['uuid'] }}
                            </p>
                        </div>

                        <!-- Balance -->
                        <div class="mb-6">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Huidige saldo</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                € {{ number_format($account['balance'], 2, ',', '.') }}
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <a href="{{ route('portal.bank-accounts.show', $account['uuid']) }}" 
                               class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors group">
                                <span class="mr-1">👁️</span>
                                Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="text-3xl">🏦</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Geen bankrekeningen gevonden</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Je hebt nog geen bankrekeningen. Contact een bankmedewerker om er een aan te maken.</p>
                <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                    <span class="mr-2">💬</span>
                    Contact Support
                </button>
            </div>
        @endif

        <!-- Tips Section -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/10 dark:to-indigo-900/10 rounded-xl border border-blue-200/50 dark:border-blue-700/50 p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                    <span class="text-lg">💡</span>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Tips voor bankbeheer</h3>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                            Controleer regelmatig je transacties voor ongeautoriseerde activiteit
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                            Gebruik verschillende rekeningen voor verschillende doeleinden
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                            Bewaar belangrijke transactiegegevens voor je administratie
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection 