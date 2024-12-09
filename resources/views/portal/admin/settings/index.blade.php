@extends('portal.layouts.app')

@section('title', 'Portal Instellingen')
@section('header', 'Portal Instellingen')

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ route('portal.admin.users.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <x-heroicon-s-arrow-left class="w-5 h-5 mr-1" />
                Terug naar overzicht
            </a>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Column - Main Settings -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Portal Features Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Portal Functies</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Beheer welke functies beschikbaar zijn in het portal</p>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('portal.admin.settings.update-features') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="px-4 py-5 sm:p-6 space-y-4">
                            @foreach($features as $feature)
                                <div class="relative flex items-start py-2">
                                    <div class="flex h-6 items-center">
                                        <input type="checkbox" 
                                               name="features[{{ $feature->key }}]" 
                                               id="feature_{{ $feature->key }}"
                                               @checked($feature->is_enabled)
                                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800">
                                    </div>
                                    <div class="ml-3">
                                        <label for="feature_{{ $feature->key }}" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ $feature->name }}
                                        </label>
                                        @if($feature->description)
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $feature->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 text-right sm:px-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit" class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                <x-heroicon-s-check class="h-4 w-4 mr-2" />
                                Instellingen Opslaan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Plugin Integration Settings -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Plugin Integratie</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configureer de verbinding met de Minecraft server</p>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">API Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20">
                                        Verbonden
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Laatste Synchronisatie</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ now()->subMinutes(5)->diffForHumans() }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Snelle Acties</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6 space-y-4">
                        <button type="button" 
                                class="w-full inline-flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                            <x-heroicon-s-arrow-path class="h-4 w-4 mr-2" />
                            Synchroniseer Data
                        </button>
                        <button type="button"
                                class="w-full inline-flex items-center justify-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <x-heroicon-s-trash class="h-4 w-4 mr-2" />
                            Cache Legen
                        </button>
                    </div>
                </div>

                <!-- System Info -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Systeem Informatie</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">PHP Versie</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ phpversion() }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Laravel Versie</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ app()->version() }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Omgeving</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst(config('app.env')) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Debug Mode</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ config('app.debug')
                                        ? 'bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-600/20 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/20'
                                        : 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20'
                                    }}">
                                        {{ config('app.debug') ? 'Ingeschakeld' : 'Uitgeschakeld' }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Notification -->
    @if(session('success'))
        <div x-data="{ show: true }"
             x-show="show"
             x-transition
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed bottom-0 right-0 m-6 w-96 max-w-full">
            <div class="rounded-lg bg-green-50 p-4 shadow-lg dark:bg-green-500/10">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-s-check-circle class="h-5 w-5 text-green-400 dark:text-green-500"/>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                            {{ session('success') }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button @click="show = false" type="button"
                                    class="inline-flex rounded-md p-1.5 text-green-500 hover:bg-green-100 dark:text-green-400 dark:hover:bg-green-500/20">
                                <span class="sr-only">Sluiten</span>
                                <x-heroicon-s-x-mark class="h-5 w-5"/>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection 