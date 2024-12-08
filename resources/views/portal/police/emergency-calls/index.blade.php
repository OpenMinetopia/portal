@extends('portal.layouts.app')

@section('title', 'Noodoproepen')
@section('header', 'Noodoproepen Beheer')

@section('content')
    <div class="space-y-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <!-- Total Calls -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-500/10 rounded-lg">
                            <x-heroicon-s-phone class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Totaal Oproepen</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Calls -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-yellow-100 dark:bg-yellow-500/10 rounded-lg">
                            <x-heroicon-s-clock class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">In Afwachting</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['pending']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Responded Calls -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-500/10 rounded-lg">
                            <x-heroicon-s-check-circle class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Beantwoord</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['responded']) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calls Table -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Recente Noodoproepen</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Overzicht van alle noodoproepen</p>
                    </div>
                </div>
            </div>

            <div class="flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800/50">
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:pl-3">Melder</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Bericht</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Datum</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-3">
                                        <span class="sr-only">Acties</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($calls as $call)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-3">
                                            <div class="flex items-center gap-x-4">
                                                <img src="https://crafatar.com/avatars/{{ $call->caller->minecraft_uuid }}?overlay=true&size=128"
                                                     alt="{{ $call->caller->minecraft_username }}"
                                                     class="h-8 w-8 rounded-full bg-gray-50 dark:bg-gray-800">
                                                <div>
                                                    <div class="font-medium text-gray-900 dark:text-white">{{ $call->caller->minecraft_username }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $call->caller->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ Str::limit($call->message, 50) }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset 
                                                {{ $call->status === 'pending' 
                                                    ? 'bg-yellow-50 text-yellow-800 ring-yellow-600/20 dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-400/20'
                                                    : 'bg-green-50 text-green-800 ring-green-600/20 dark:bg-green-400/10 dark:text-green-500 dark:ring-green-400/20' }}">
                                                {{ $call->status === 'pending' ? 'In Afwachting' : 'Beantwoord' }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $call->created_at->format('d M Y H:i') }}
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-3">
                                            <a href="{{ route('portal.emergency-calls.show', $call) }}"
                                               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                Bekijken<span class="sr-only">, {{ $call->id }}</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-8 text-center">
                                            <div class="flex flex-col items-center">
                                                <x-heroicon-o-phone class="h-12 w-12 text-gray-400 dark:text-gray-500" />
                                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen noodoproepen</h3>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Er zijn nog geen noodoproepen gedaan.</p>
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
            @if($calls->hasPages())
                <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 sm:px-6">
                    {{ $calls->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection 