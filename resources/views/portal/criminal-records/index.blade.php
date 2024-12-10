@extends('portal.layouts.app')

@section('title', 'Mijn strafblad')
@section('header', 'Mijn strafblad')

@section('content')
    <div class="space-y-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <!-- Total Records -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-red-100 dark:bg-red-500/10 rounded-lg">
                            <x-heroicon-s-document-text class="h-6 w-6 text-red-600 dark:text-red-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Totale overtredingen</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ count($records) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div @class([
                            'flex-shrink-0 p-3 rounded-lg',
                            'bg-green-100 dark:bg-green-500/10' => empty($records),
                            'bg-red-100 dark:bg-red-500/10' => !empty($records),
                        ])>
                            @if(empty($records))
                                <x-heroicon-s-shield-check class="h-6 w-6 text-green-600 dark:text-green-400" />
                            @else
                                <x-heroicon-s-shield-exclamation class="h-6 w-6 text-red-600 dark:text-red-400" />
                            @endif
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ empty($records) ? 'Schoon' : 'Overtredingen' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Records Table -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Overtredingen</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Overzicht van al je overtredingen</p>
            </div>

            <div class="flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800/50">
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:pl-3">Reden</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Agent</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Datum</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($records as $record)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-3">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $record['reason'] }}</div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <div class="flex items-center gap-2">
                                                <img src="{{ $record['officer_skin_url'] }}"
                                                     alt="{{ $record['officer_name'] }}"
                                                     class="h-6 w-6 rounded-md"
                                                     onerror="this.src='https://crafatar.com/avatars/steve'">
                                                <span class="text-gray-900 dark:text-white">{{ $record['officer_name'] }}</span>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ \Carbon\Carbon::createFromTimestampMs($record['date'])->format('d-m-Y H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-3 py-8 text-center">
                                            <div class="flex flex-col items-center">
                                                <x-heroicon-o-shield-check class="h-12 w-12 text-gray-400 dark:text-gray-500"/>
                                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen overtredingen</h3>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Je hebt een schoon strafblad.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
