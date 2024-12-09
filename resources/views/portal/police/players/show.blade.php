@extends('portal.layouts.app')

@section('title', $user->minecraft_username)
@section('header', $user->minecraft_username)

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ route('portal.police.players.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <x-heroicon-s-arrow-left class="w-5 h-5 mr-1" />
                Terug naar overzicht
            </a>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Column - Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Player Info Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-x-4">
                            <img src="https://crafatar.com/avatars/{{ $user->minecraft_uuid }}?overlay=true&size=128"
                                 alt="{{ $user->minecraft_username }}"
                                 class="h-16 w-16 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $user->minecraft_username }}</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $user->name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Huidige baan</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->prefix }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Level</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->level }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fitheid</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->fitness }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Bank Saldo</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->formatted_balance_with_currency }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Criminal Records -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Strafblad</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Overzicht van alle overtredingen</p>
                            </div>
                            <span @class([
                                'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' => count($user->criminal_records) === 0,
                                'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20' => count($user->criminal_records) > 0,
                            ])>
                                {{ count($user->criminal_records) }} overtredingen
                            </span>
                        </div>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        @forelse($user->criminal_records as $record)
                            <div @class([
                                'flex items-start space-x-4 py-4',
                                'border-t border-gray-200 dark:border-gray-700' => !$loop->first
                            ])>
                                <div class="flex-shrink-0 pt-1">
                                    <x-heroicon-s-exclamation-circle class="h-6 w-6 text-red-500 dark:text-red-400"/>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $record['reason'] }}
                                    </p>
                                    <div class="mt-2 flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                        <span>{{ \Carbon\Carbon::parse($record['date'])->format('d-m-Y H:i') }}</span>
                                        <div class="flex items-center gap-2">
                                            <img src="{{ $record['officer_skin_url'] }}"
                                                 alt="{{ $record['officer_name'] }}"
                                                 class="h-5 w-5 rounded-full"
                                                 onerror="this.src='https://crafatar.com/avatars/steve'">
                                            <span>Agent: {{ $record['officer_name'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <x-heroicon-o-shield-check class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"/>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen overtredingen</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Deze speler heeft een schoon strafblad.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Stats Overview -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Statistieken</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Account Status</dt>
                                <dd class="mt-1">
                                    <span @class([
                                        'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                        'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' => $user->is_online,
                                        'bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-500/10 dark:text-gray-400 dark:ring-gray-500/20' => !$user->is_online,
                                    ])>
                                        {{ $user->is_online ? 'Online' : 'Offline' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Speeltijd</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->playtime }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Account Aangemaakt</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->created_at->format('d-m-Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Minecraft UUID</dt>
                                <dd class="mt-1 text-sm font-mono text-gray-900 dark:text-white">{{ $user->minecraft_uuid }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 