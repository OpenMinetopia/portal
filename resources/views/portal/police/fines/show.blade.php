@extends('portal.layouts.app')

@section('title', 'Bekijk Boete')
@section('header', 'Boete Details')

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ route('portal.fines.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <x-heroicon-s-arrow-left class="w-5 h-5 mr-1" />
                Terug naar overzicht
            </a>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Column - Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Fine Details Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Boete #{{ $fine->id }}</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Uitgeschreven op {{ $fine->created_at->format('d M Y') }} om {{ $fine->created_at->format('H:i') }}
                                </p>
                            </div>
                            <span class="inline-flex items-center rounded-md px-2 py-1 text-sm font-medium ring-1 ring-inset 
                                {{ $fine->isPaid() 
                                    ? 'bg-green-50 text-green-800 ring-green-600/20 dark:bg-green-400/10 dark:text-green-500 dark:ring-green-400/20'
                                    : 'bg-yellow-50 text-yellow-800 ring-yellow-600/20 dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-400/20' }}">
                                {{ $fine->isPaid() ? 'Betaald' : 'Openstaand' }}
                            </span>
                        </div>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Bedrag</dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">${{ number_format($fine->amount) }}</dd>
                            </div>
                            @if($fine->paid_at)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Betaald op</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $fine->paid_at->format('d M Y H:i') }}
                                    </dd>
                                </div>
                            @endif
                        </dl>

                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Reden voor Boete</h4>
                            <div class="mt-2 prose prose-sm max-w-none text-gray-900 dark:text-white">
                                {{ $fine->reason }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Involved Parties -->
            <div class="space-y-6">
                <!-- Offender Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Overtreder</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center gap-x-4">
                            <img src="https://crafatar.com/avatars/{{ $fine->user->minecraft_uuid }}?overlay=true&size=128"
                                 alt="{{ $fine->user->minecraft_username }}"
                                 class="h-16 w-16 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $fine->user->minecraft_username }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $fine->user->name }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Officer Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Uitgeschreven door</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center gap-x-4">
                            <img src="https://crafatar.com/avatars/{{ $fine->officer->minecraft_uuid }}?overlay=true&size=128"
                                 alt="{{ $fine->officer->minecraft_username }}"
                                 class="h-16 w-16 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $fine->officer->minecraft_username }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $fine->officer->name }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        @if(!$fine->isPaid() && (auth()->user()->isAdmin() || auth()->user()->roles->contains('id', 3)))
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Acties</h3>
                    <div class="flex gap-4">
                        <button type="button" 
                                class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 dark:bg-green-500 dark:hover:bg-green-400">
                            <x-heroicon-s-check class="h-4 w-4 mr-2" />
                            Markeer als Betaald
                        </button>
                        <button type="button" 
                                class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 dark:bg-red-500 dark:hover:bg-red-400">
                            <x-heroicon-s-trash class="h-4 w-4 mr-2" />
                            Verwijderen
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection 