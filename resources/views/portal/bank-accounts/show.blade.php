@extends('portal.layouts.app')

@section('title', $account['name'])
@section('header')
    <div class="flex items-center gap-4">
        <a href="{{ route('portal.bank-accounts.index') }}"
           class="group flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
            <x-heroicon-s-arrow-left class="w-5 h-5"/>
            Terug naar overzicht
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Account Header Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="space-y-1">
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $account['name'] }}</h1>
                            <div class="flex items-center gap-2">
                                <span @class([
                                    'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                    'bg-red-400/10 text-red-400 ring-red-400/20' => $account['frozen'],
                                    'bg-green-400/10 text-green-400 ring-green-400/20' => !$account['frozen'],
                                ])>
                                    {{ $account['frozen'] ? 'Bevroren' : 'Actief' }}
                                </span>
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-indigo-400/10 text-indigo-400 ring-indigo-400/20">
                                    {{ $account['type'] }}
                                </span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-mono">{{ $account['uuid'] }}</p>
                    </div>
                    <div class="flex items-center gap-6">
{{--                        <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">--}}
{{--                            <x-heroicon-s-arrow-path class="w-4 h-4 mr-2"/>--}}
{{--                            Overmaken--}}
{{--                        </button>--}}
                        <div class="text-right">
                            <div class="text-3xl font-bold text-gray-900 dark:text-white">
                                € {{ number_format($account['balance'], 2, ',', '.') }}
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Beschikbaar saldo</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Details & Users Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Account Info Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <x-heroicon-s-identification class="h-5 w-5 text-gray-400 dark:text-gray-500"/>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Rekeninggegevens</h2>
                    </div>
                    <dl class="space-y-4 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Type rekening</dt>
                            <dd class="font-medium text-gray-900 dark:text-white">{{ $account['type'] }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Status</dt>
                            <dd>
                                <span @class([
                                    'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                    'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20' => $account['frozen'],
                                    'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' => !$account['frozen'],
                                ])>
                                    {{ $account['frozen'] ? 'Bevroren' : 'Actief' }}
                                </span>
                            </dd>
                        </div>
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <dt class="text-gray-500 dark:text-gray-400 mb-1">Rekeningnummer</dt>
                            <dd class="font-mono text-gray-900 dark:text-white break-all">{{ $account['uuid'] }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Users Card (spans 2 columns) -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-heroicon-s-users class="h-5 w-5 text-gray-400 dark:text-gray-500"/>
                            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Gebruikers</h2>
                        </div>
                        {{-- Add User button commented out for future implementation
                        <button class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            <x-heroicon-s-plus class="h-4 w-4 mr-1.5"/> Gebruiker toevoegen
                        </button>
                        --}}
                    </div>
                </div>
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($users as $user)
                        <li class="flex items-center justify-between gap-x-6 p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <div class="flex min-w-0 gap-x-4">
                                <img class="h-12 w-12 flex-none rounded-lg bg-gray-50 dark:bg-gray-800 object-cover"
                                     src="{{ \App\Helpers\MinecraftHelper::getAvatar($user['uuid']) }}"
                                     alt="{{ \App\Helpers\MinecraftHelper::getName($user['uuid']) }}">
                                <div class="min-w-0 flex-auto">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ \App\Helpers\MinecraftHelper::getName($user['uuid']) }}
                                    </p>
                                    <p class="mt-1 truncate text-xs font-mono text-gray-500 dark:text-gray-400">
                                        {{ $user['uuid'] }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="flex flex-col items-end gap-1">
                                    <span @class([
                                        'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                        'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20' => $user['owner'],
                                        'bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-500/10 dark:text-gray-400 dark:ring-gray-500/20' => !$user['owner'],
                                    ])>
                                        {{ $user['owner'] ? 'Eigenaar' : 'Lid' }}
                                    </span>
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-indigo-50 text-indigo-700 ring-indigo-600/20 dark:bg-indigo-500/10 dark:text-indigo-400 dark:ring-indigo-500/20">
                                        {{ $user['permission'] }}
                                    </span>
                                </div>
{{--                                @unless($user['owner'])--}}
{{--                                    <button class="p-1 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">--}}
{{--                                        <x-heroicon-s-trash class="w-5 h-5"/>--}}
{{--                                    </button>--}}
{{--                                @endunless--}}
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-8">
                            <div class="text-center">
                                <x-heroicon-o-users class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"/>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen gebruikers</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Er zijn geen gebruikers gekoppeld aan deze rekening.
                                </p>
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
