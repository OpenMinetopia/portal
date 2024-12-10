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
                                        <span>{{ \Carbon\Carbon::createFromTimestampMs($record['date'])->format('d-m-Y H:i') }}</span>
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

                <!-- Bank Accounts -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Bankrekeningen</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Overzicht van alle bankrekeningen</p>
                            </div>
                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20">
                                {{ count($user->bank_accounts) }} rekeningen
                            </span>
                        </div>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <div class="space-y-4">
                            @forelse($user->bank_accounts as $account)
                                <div class="flex items-start justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $account['name'] }}
                                            @if($account['type'] === 'PRIVATE')
                                                <span class="ml-2 inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-600/20 dark:bg-purple-500/10 dark:text-purple-400 dark:ring-purple-500/20">
                                                    Privé
                                                </span>
                                            @endif
                                        </h4>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            Rekeningnummer: {{ $account['uuid'] }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            € {{ number_format($account['balance'], 2, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6">
                                    <x-heroicon-o-credit-card class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"/>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen bankrekeningen</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Deze speler heeft geen bankrekeningen.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Plots -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Plots</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Overzicht van alle plots</p>
                            </div>
                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20">
                                {{ count($user->plots) }} plots
                            </span>
                        </div>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <div class="space-y-4">
                            @forelse($user->plots as $plot)
                                <div class="flex items-start space-x-4 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="flex-shrink-0">
                                        @if($plot['permission'] === 'OWNER')
                                            <x-heroicon-s-key class="h-6 w-6 text-indigo-500"/>
                                        @else
                                            <x-heroicon-s-user class="h-6 w-6 text-gray-400"/>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $plot['name'] }}</h4>
                                            <span @class([
                                                'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                                'bg-indigo-50 text-indigo-700 ring-indigo-600/20 dark:bg-indigo-500/10 dark:text-indigo-400 dark:ring-indigo-500/20' => $plot['permission'] === 'OWNER',
                                                'bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-500/10 dark:text-gray-400 dark:ring-gray-500/20' => $plot['permission'] === 'MEMBER',
                                            ])>
                                                {{ $plot['permission'] === 'OWNER' ? 'Eigenaar' : 'Lid' }}
                                            </span>
                                        </div>
                                        <div class="mt-2 grid grid-cols-2 gap-4 text-sm text-gray-500 dark:text-gray-400">
                                            <div>
                                                <p class="font-medium text-gray-700 dark:text-gray-300">Locatie</p>
                                                <p>{{ $plot['location']['min']['x'] }}, {{ $plot['location']['min']['z'] }}</p>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-700 dark:text-gray-300">Afmetingen</p>
                                                <p>{{ abs($plot['location']['max']['x'] - $plot['location']['min']['x']) + 1 }}x{{ abs($plot['location']['max']['z'] - $plot['location']['min']['z']) + 1 }} blokken</p>
                                            </div>
                                        </div>
                                        @if($plot['permission'] === 'OWNER')
                                            <div class="mt-3">
                                                <p class="font-medium text-sm text-gray-700 dark:text-gray-300">Leden ({{ count($plot['members']) }})</p>
                                                <div class="mt-2 flex flex-wrap gap-2">
                                                    @foreach($plot['members'] as $member)
                                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-md dark:bg-gray-700 dark:text-gray-300">
                                                            <img src="https://crafatar.com/avatars/{{ $member }}?size=16&overlay=true" 
                                                                 alt="" 
                                                                 class="w-4 h-4 rounded">
                                                            {{ \App\Helpers\MinecraftHelper::getName($member) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6">
                                    <x-heroicon-o-map class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"/>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen plots</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Deze speler heeft geen plots.</p>
                                </div>
                            @endforelse
                        </div>
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