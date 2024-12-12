@extends('portal.layouts.app')

@section('title', 'Bankrekening Details')
@section('header', 'Bankrekening Details')

@section('content')
    <div class="space-y-6">
        <!-- Account Details -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Bankrekening Informatie</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $account['name'] }}</p>
                    </div>
                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $account['type'] === 'PRIVATE' ? 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20' : 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' }}">
                        {{ $account['type'] }}
                    </span>
                </div>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">UUID</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $account['uuid'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Saldo</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">€ {{ number_format($account['balance'], 2, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Users List -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Gebruikers</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gebruikers met toegang tot deze rekening</p>
                    </div>
                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20">
                        {{ count($users) }} gebruikers
                    </span>
                </div>
            </div>
            <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($users as $user)
                    <li class="flex items-center justify-between gap-x-6 px-4 py-5 sm:px-6 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <div class="flex min-w-0 gap-x-4">
                            <img class="h-12 w-12 flex-none rounded-lg bg-gray-50 dark:bg-gray-800"
                                 src="https://crafatar.com/avatars/{{ $user['uuid'] }}?overlay=true"
                                 alt="{{ $user['uuid'] }}">
                            <div class="min-w-0 flex-auto">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user['uuid'] }}</p>
                                <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">
                                    {{ $user['permission'] }}
                                </p>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-4 py-5 sm:px-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Geen gebruikers gekoppeld aan deze rekening</p>
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection 