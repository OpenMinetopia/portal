@extends('portal.layouts.app')

@section('title', 'Boetes')
@section('header', 'Boetes Beheer')

@section('content')
    <div class="space-y-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <!-- Total Fines -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-500/10 rounded-lg">
                            <x-heroicon-s-document-text class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Totaal Boetes</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unpaid Fines -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-yellow-100 dark:bg-yellow-500/10 rounded-lg">
                            <x-heroicon-s-clock class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Openstaand</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['unpaid']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Amount -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-500/10 rounded-lg">
                            <x-heroicon-s-banknotes class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Totaal Bedrag</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">${{ number_format($stats['totalAmount']) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fines Table -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Boetes Overzicht</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Beheer alle uitgeschreven boetes</p>
                    </div>
                    <div>
                        <button type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                            <x-heroicon-s-plus class="h-4 w-4 mr-2" />
                            Nieuwe Boete
                        </button>
                    </div>
                </div>
            </div>

            <div class="flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800/50">
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:pl-3">Overtreder</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Agent</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Reden</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Bedrag</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-3">
                                        <span class="sr-only">Acties</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($fines as $fine)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-3">
                                            <div class="flex items-center gap-x-4">
                                                <img src="https://crafatar.com/avatars/{{ $fine->user->minecraft_uuid }}?overlay=true&size=128"
                                                     alt="{{ $fine->user->minecraft_username }}"
                                                     class="h-8 w-8 rounded-full bg-gray-50 dark:bg-gray-800">
                                                <div>
                                                    <div class="font-medium text-gray-900 dark:text-white">{{ $fine->user->minecraft_username }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $fine->user->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4">
                                            <div class="flex items-center gap-x-4">
                                                <img src="https://crafatar.com/avatars/{{ $fine->officer->minecraft_uuid }}?overlay=true&size=128"
                                                     alt="{{ $fine->officer->minecraft_username }}"
                                                     class="h-8 w-8 rounded-full bg-gray-50 dark:bg-gray-800">
                                                <div>
                                                    <div class="font-medium text-gray-900 dark:text-white">{{ $fine->officer->minecraft_username }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $fine->officer->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ Str::limit($fine->reason, 50) }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span class="font-medium text-gray-900 dark:text-white">${{ number_format($fine->amount) }}</span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset 
                                                {{ $fine->isPaid() 
                                                    ? 'bg-green-50 text-green-800 ring-green-600/20 dark:bg-green-400/10 dark:text-green-500 dark:ring-green-400/20'
                                                    : 'bg-yellow-50 text-yellow-800 ring-yellow-600/20 dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-400/20' }}">
                                                {{ $fine->isPaid() ? 'Betaald' : 'Openstaand' }}
                                            </span>
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-3">
                                            <a href="{{ route('portal.fines.show', $fine) }}"
                                               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                Bekijken<span class="sr-only">, {{ $fine->id }}</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 py-8 text-center">
                                            <div class="flex flex-col items-center">
                                                <x-heroicon-o-document-text class="h-12 w-12 text-gray-400 dark:text-gray-500" />
                                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen boetes</h3>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Er zijn nog geen boetes uitgeschreven.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            @if($fines->hasPages())
                <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 sm:px-6">
                    {{ $fines->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection 