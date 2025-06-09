@extends('portal.layouts.v2.app')

@section('title', 'Rollen Beheer')
@section('header', 'Rollen Beheer')

@section('content')
    <div class="space-y-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <!-- Total Roles -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-indigo-600/5"></div>
                <div class="relative p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                <x-heroicon-s-key class="h-6 w-6 text-white" />
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Totaal Rollen</h3>
                            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Roles -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 to-pink-600/5"></div>
                <div class="relative p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                                <x-heroicon-s-shield-check class="h-6 w-6 text-white" />
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Admin Rollen</h3>
                            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ number_format($stats['admin_roles']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Game Roles -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-green-600/5"></div>
                <div class="relative p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                                <x-heroicon-s-puzzle-piece class="h-6 w-6 text-white" />
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Game Rollen</h3>
                            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ number_format($stats['game_roles']) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roles Table -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-gray-50/50 to-white/50 dark:from-gray-800/50 dark:to-gray-900/50"></div>
            <div class="relative">
                <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center">
                                <x-heroicon-s-key class="h-5 w-5 text-white" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Rollen</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Beheer alle rollen en hun permissies</p>
                            </div>
                        </div>
                        <a href="{{ route('portal.admin.roles.create') }}"
                           class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-green-600 px-4 py-2 text-sm font-bold text-white shadow-lg hover:from-emerald-600 hover:to-green-700 transition-all duration-200 transform hover:scale-105">
                            <x-heroicon-s-plus class="h-4 w-4" />
                            Nieuwe Rol
                        </a>
                    </div>
                </div>

                <div class="overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200/50 dark:divide-gray-700/50">
                            <thead>
                                <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                                    <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400">Naam</th>
                                    <th scope="col" class="px-3 py-4 text-left text-xs font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400">Type</th>
                                    <th scope="col" class="px-3 py-4 text-left text-xs font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400">Gebruikers</th>
                                    <th scope="col" class="px-3 py-4 text-left text-xs font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400">Beschrijving</th>
                                    <th scope="col" class="relative py-4 pl-3 pr-6">
                                        <span class="sr-only">Acties</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200/30 dark:divide-gray-700/30">
                                @forelse($roles as $role)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-all duration-200">
                                        <td class="whitespace-nowrap py-4 pl-6 pr-3">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $role->is_admin ? 'bg-gradient-to-r from-red-500 to-pink-600' : 'bg-gradient-to-r from-emerald-500 to-green-600' }} shadow-lg">
                                                    @if($role->is_admin)
                                                        <x-heroicon-s-shield-check class="h-5 w-5 text-white" />
                                                    @else
                                                        <x-heroicon-s-puzzle-piece class="h-5 w-5 text-white" />
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-900 dark:text-white">{{ $role->name }}</div>
                                                    <div class="text-sm text-gray-600 dark:text-gray-400 font-mono">{{ $role->slug }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            @if($role->is_admin)
                                                <span class="inline-flex items-center rounded-xl px-3 py-1 text-xs font-bold bg-red-100/80 text-red-700 dark:bg-red-500/20 dark:text-red-300 border border-red-200/50 dark:border-red-500/30">
                                                    <x-heroicon-s-shield-check class="w-3 h-3 mr-1" />
                                                    Admin
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-xl px-3 py-1 text-xs font-bold bg-emerald-100/80 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-200/50 dark:border-emerald-500/30">
                                                    <x-heroicon-s-puzzle-piece class="w-3 h-3 mr-1" />
                                                    Game
                                                </span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span class="inline-flex items-center rounded-xl px-3 py-1 text-xs font-bold bg-blue-100/80 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 border border-blue-200/50 dark:border-blue-500/30">
                                                <x-heroicon-s-users class="w-3 h-3 mr-1" />
                                                {{ $role->users_count }} gebruikers
                                            </span>
                                        </td>
                                        <td class="px-3 py-4 text-sm text-gray-600 dark:text-gray-400 max-w-xs">
                                            <p class="truncate">{{ Str::limit($role->description, 50) }}</p>
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-medium">
                                            <a href="{{ route('portal.admin.roles.edit', $role) }}"
                                               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 border border-indigo-500/30 hover:bg-indigo-500/30 transition-all duration-200 backdrop-blur-sm">
                                                <x-heroicon-s-pencil class="h-4 w-4" />
                                                Bewerken
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-16 text-center">
                                            <div class="flex flex-col items-center">
                                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mb-4">
                                                    <x-heroicon-o-key class="h-8 w-8 text-gray-400 dark:text-gray-500" />
                                                </div>
                                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Geen rollen</h3>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Er zijn nog geen rollen aangemaakt.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                @if($roles->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200/50 dark:border-gray-700/50">
                        {{ $roles->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection 