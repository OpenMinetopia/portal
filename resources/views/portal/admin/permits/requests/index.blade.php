@extends('portal.layouts.app')

@section('title', 'Vergunning Aanvragen')
@section('header', 'Vergunning Aanvragen')

@section('content')
    <div class="space-y-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-4">
            <!-- Total Requests -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-500/10 rounded-lg">
                            <x-heroicon-s-inbox-stack class="h-6 w-6 text-blue-600 dark:text-blue-400"/>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Totaal Aanvragen</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Requests -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-yellow-100 dark:bg-yellow-500/10 rounded-lg">
                            <x-heroicon-s-clock class="h-6 w-6 text-yellow-600 dark:text-yellow-400"/>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">In Behandeling</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['pending']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approved Requests -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-500/10 rounded-lg">
                            <x-heroicon-s-check-circle class="h-6 w-6 text-green-600 dark:text-green-400"/>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Goedgekeurd</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['approved']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Denied Requests -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-red-100 dark:bg-red-500/10 rounded-lg">
                            <x-heroicon-s-x-circle class="h-6 w-6 text-red-600 dark:text-red-400"/>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Afgewezen</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['denied']) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requests Table -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Vergunning Aanvragen</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Beheer alle ingediende vergunning aanvragen</p>
                    </div>
                </div>
            </div>

            <div class="flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800/50">
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:pl-3">Aanvrager</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Type</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Aangevraagd op</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Behandelaar</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-3">
                                        <span class="sr-only">Acties</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($requests as $request)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-3">
                                            <div class="flex items-center gap-x-4">
                                                <img src="https://crafatar.com/avatars/{{ $request->user->minecraft_uuid }}?overlay=true&size=128"
                                                     alt="{{ $request->user->minecraft_username }}"
                                                     class="h-8 w-8 rounded-full bg-gray-50 dark:bg-gray-800">
                                                <div>
                                                    <div class="font-medium text-gray-900 dark:text-white">{{ $request->user->minecraft_username }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $request->user->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $request->type->name }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $request->created_at->format('d-m-Y H:i') }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span @class([
                                                'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                                'bg-yellow-50 text-yellow-700 ring-yellow-600/20 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/20' => $request->isPending(),
                                                'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' => $request->isApproved(),
                                                'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20' => $request->isDenied(),
                                            ])>
                                                @if($request->isPending())
                                                    In behandeling
                                                @elseif($request->isApproved())
                                                    Goedgekeurd
                                                @else
                                                    Afgewezen
                                                @endif
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            @if($request->handler)
                                                {{ $request->handler->name }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-3">
                                            <a href="{{ route('portal.admin.permits.requests.show', $request) }}"
                                               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                Bekijken<span class="sr-only">, {{ $request->type->name }}</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 py-8 text-center">
                                            <div class="flex flex-col items-center">
                                                <x-heroicon-o-inbox class="h-12 w-12 text-gray-400 dark:text-gray-500"/>
                                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen aanvragen</h3>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                    Er zijn nog geen vergunning aanvragen ingediend.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($requests->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                    {{ $requests->links() }}
                </div>
            @endif
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