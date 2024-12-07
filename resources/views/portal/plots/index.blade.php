@extends('portal.layouts.app')

@section('title', 'Mijn Plots')
@section('header', 'Plot Beheer')

@section('content')
    <div class="space-y-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-indigo-100 dark:bg-indigo-500/10 rounded-lg">
                            <x-heroicon-s-building-office-2 class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Totaal Plots</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-500/10 rounded-lg">
                            <x-heroicon-s-square-3-stack-3d class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Totale Oppervlakte</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['area']) }} <span class="text-sm text-gray-500 dark:text-gray-400">blokken</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plots Table Card -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <div class="p-4 sm:p-6">
                    <div class="sm:flex sm:items-center">
                        <div class="sm:flex-auto">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Jouw Plots</h3>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Beheer al je plots en hun leden vanaf één plek.</p>
                        </div>
                        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                            <button type="button"
                                    class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                <x-heroicon-s-plus class="h-4 w-4 mr-2" />
                                Nieuwe Plot
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50">
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:pl-3">Plot Details</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Locatie</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Leden</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-3">
                                    <span class="sr-only">Acties</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($plots as $plot)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-3">
                                        <div class="flex items-center gap-x-4">
                                            <div class="h-10 w-10 flex-shrink-0 rounded-lg bg-indigo-100 dark:bg-indigo-500/10 flex items-center justify-center">
                                                <x-heroicon-s-building-office-2 class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $plot->name }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($plot->getArea()) }} blokken</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $plot->world }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $plot->coordinates['x'] ?? 'N/A' }}, {{ $plot->coordinates['z'] ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4">
                                        <div class="flex -space-x-2">
                                            @foreach($plot->members->take(3) as $member)
                                                <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white dark:ring-gray-800"
                                                     src="https://crafatar.com/avatars/{{ $member->minecraft_uuid }}?overlay=true&size=128"
                                                     alt="{{ $member->minecraft_username }}">
                                            @endforeach
                                            @if($plot->members->count() > 3)
                                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full ring-2 ring-white dark:ring-gray-800 bg-gray-100 dark:bg-gray-700">
                                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">+{{ $plot->members->count() - 3 }}</span>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4">
                                        <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $plot->flags && in_array('active', $plot->flags)
                                            ? 'bg-green-100 text-green-800 dark:bg-green-500/10 dark:text-green-400'
                                            : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                            {{ $plot->flags && in_array('active', $plot->flags) ? 'Actief' : 'Inactief' }}
                                        </span>
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-3">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('portal.plots.show', $plot) }}"
                                               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                Bekijken<span class="sr-only">, {{ $plot->name }}</span>
                                            </a>
                                            <span class="text-gray-400 dark:text-gray-600">&middot;</span>
                                            <a href="{{ route('portal.plots.edit', $plot) }}"
                                               class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300">
                                                Bewerken
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-8 text-center">
                                        <div class="flex flex-col items-center">
                                            <x-heroicon-o-square-3-stack-3d class="h-12 w-12 text-gray-400 dark:text-gray-500" />
                                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen plots</h3>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Begin met het maken van een nieuwe plot.</p>
                                            <div class="mt-6">
                                                <button type="button"
                                                        class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                                    <x-heroicon-s-plus class="h-4 w-4 mr-2" />
                                                    Nieuwe Plot
                                                </button>
                                            </div>
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
            @if($plots->hasPages())
                <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 sm:px-6">
                    {{ $plots->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
