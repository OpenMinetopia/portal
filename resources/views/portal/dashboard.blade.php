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
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Huidige saldo</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ auth()->user()->formatted_balance_with_currency }}</p>
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
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ count(auth()->user()->getPlotsAttribute()) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Job Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-500/10 rounded-lg">
                            <x-heroicon-s-briefcase class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Huidige baan</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ auth()->user()->getPrefixAttribute() }}</p>
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
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Fitheid</h3>
                            <div class="mt-1">
                                <div class="flex items-center">
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ auth()->user()->getFitnessAttribute() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Level Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-yellow-100 dark:bg-yellow-500/10 rounded-lg">
                            <x-heroicon-s-star class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Level</h3>
                            <div class="mt-1">
                                <div class="flex items-center">
                                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ auth()->user()->level }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
