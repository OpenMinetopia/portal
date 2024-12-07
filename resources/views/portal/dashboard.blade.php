@extends('portal.layouts.app')

@section('title', 'Overzicht')
@section('header', 'Overzicht')

@section('content')
    <div class="space-y-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Balance Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-500/10 rounded-lg">
                            <x-heroicon-s-banknotes class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Saldo</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">${{ number_format($stats['balance']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Plots Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-indigo-100 dark:bg-indigo-500/10 rounded-lg">
                            <x-heroicon-s-building-office-2 class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Plots</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['plots']['total']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicles Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-500/10 rounded-lg">
                            <x-heroicon-s-truck class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Voertuigen</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['vehicles']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fitness Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-red-100 dark:bg-red-500/10 rounded-lg">
                            <x-heroicon-s-heart class="h-6 w-6 text-red-600 dark:text-red-400" />
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Conditie</h3>
                            <div class="mt-1">
                                <div class="flex items-center">
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['fitness']['percentage'] }}%</p>
                                    <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                        {{ number_format($stats['fitness']['current']) }}/{{ number_format($stats['fitness']['max']) }}
                                    </span>
                                </div>
                                <div class="mt-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-red-600 dark:bg-red-500 h-2 rounded-full" style="width: {{ $stats['fitness']['percentage'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Level Progress -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Level Voortgang</h3>
                    <div class="mt-6">
                        <div class="flex items-center justify-between text-sm font-medium text-gray-900 dark:text-white">
                            <span>Level {{ $stats['level']['current'] }}</span>
                            <span>Level {{ $stats['level']['next'] }}</span>
                        </div>
                        <div class="mt-2">
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                <div class="bg-indigo-600 dark:bg-indigo-500 h-2.5 rounded-full" style="width: {{ $stats['level']['progress'] }}%"></div>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            {{ $stats['level']['progress'] }}% tot volgend level
                        </p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Recente Activiteit</h3>
                    @include('portal.partials.recent-activity')
                </div>
            </div>
        </div>
    </div>
@endsection
