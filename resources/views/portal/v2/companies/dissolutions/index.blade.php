@extends('portal.layouts.v2.app')

@section('title', 'Opheffings Aanvragen')
@section('header', 'Opheffings Aanvragen')

@section('content')
    <div class="space-y-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <!-- Total Requests -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 to-orange-600/5"></div>
                <div class="relative p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                                <x-heroicon-s-archive-box-x-mark class="h-6 w-6 text-white"/>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Totaal Aanvragen</h3>
                            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $requests->total() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Requests -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/5 to-orange-600/5"></div>
                <div class="relative p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                                <x-heroicon-s-clock class="h-6 w-6 text-white"/>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">In Behandeling</h3>
                            <p class="text-3xl font-black text-gray-900 dark:text-white">
                                {{ $requests->where('status', 'pending')->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Handled Today -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-green-600/5"></div>
                <div class="relative p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                                <x-heroicon-s-check-circle class="h-6 w-6 text-white"/>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Vandaag Verwerkt</h3>
                            <p class="text-3xl font-black text-gray-900 dark:text-white">
                                {{ $requests->where('handled_at', '>=', now()->startOfDay())->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requests Table -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-gray-50/50 to-white/50 dark:from-gray-800/50 dark:to-gray-900/50"></div>
            <div class="relative">
                <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-red-500 to-orange-600 rounded-xl flex items-center justify-center">
                            <x-heroicon-s-archive-box-x-mark class="h-5 w-5 text-white" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Opheffings Aanvragen</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Overzicht van alle opheffings aanvragen</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200/50 dark:divide-gray-700/50">
                            <thead>
                                <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                                    <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400">Bedrijf</th>
                                    <th scope="col" class="px-3 py-4 text-left text-xs font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400">Type</th>
                                    <th scope="col" class="px-3 py-4 text-left text-xs font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400">Aanvrager</th>
                                    <th scope="col" class="px-3 py-4 text-left text-xs font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400">Status</th>
                                    <th scope="col" class="px-3 py-4 text-left text-xs font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400">Datum</th>
                                    <th scope="col" class="relative py-4 pl-3 pr-6">
                                        <span class="sr-only">Acties</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200/30 dark:divide-gray-700/30">
                                @forelse($requests as $request)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-all duration-200">
                                        <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-gradient-to-r from-red-500 to-orange-600 rounded-lg flex items-center justify-center">
                                                    <x-heroicon-s-building-office class="h-4 w-4 text-white" />
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-900 dark:text-white">{{ $request->company->name }}</div>
                                                    <div class="text-sm text-gray-600 dark:text-gray-400 font-mono">{{ $request->company->kvk_number }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span class="inline-flex items-center rounded-xl px-3 py-1 text-xs font-bold bg-blue-100/80 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 border border-blue-200/50 dark:border-blue-500/30">
                                                {{ $request->company->type->name }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $request->user->minecraft_username }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span @class([
                                                'inline-flex items-center rounded-xl px-3 py-1.5 text-xs font-bold shadow-sm backdrop-blur-sm border',
                                                'bg-yellow-500/20 text-yellow-700 dark:text-yellow-300 border-yellow-500/30' => $request->status === 'pending',
                                                'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-500/30' => $request->status === 'approved',
                                                'bg-red-500/20 text-red-700 dark:text-red-300 border-red-500/30' => $request->status === 'denied',
                                            ])>
                                                @if($request->status === 'pending')
                                                    <div class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse mr-2"></div>
                                                @elseif($request->status === 'approved')
                                                    <div class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></div>
                                                @else
                                                    <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                                @endif
                                                {{ $request->getStatusText() }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600 dark:text-gray-400 font-mono">
                                            {{ $request->created_at->format('d-m-Y H:i') }}
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-medium">
                                            <a href="{{ route('portal.companies.dissolutions.show', $request) }}"
                                               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 border border-indigo-500/30 hover:bg-indigo-500/30 transition-all duration-200 backdrop-blur-sm">
                                                <x-heroicon-s-eye class="h-4 w-4" />
                                                Bekijken
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-16 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mb-4">
                                                    <x-heroicon-o-archive-box-x-mark class="h-8 w-8 text-gray-400 dark:text-gray-500"/>
                                                </div>
                                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Geen aanvragen</h3>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    Er zijn nog geen opheffings aanvragen ingediend.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($requests->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200/50 dark:border-gray-700/50">
                        {{ $requests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>


@endsection 