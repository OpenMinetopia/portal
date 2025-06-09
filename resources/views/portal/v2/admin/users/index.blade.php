@extends('portal.layouts.v2.app')

@section('title', 'Gebruikers Beheer')
@section('header', 'Gebruikers Beheer')

@section('content')
    <div class="space-y-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <!-- Total Users -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-indigo-600/5"></div>
                <div class="relative p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                <x-heroicon-s-users class="h-6 w-6 text-white" />
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Totaal Gebruikers</h3>
                            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verified Users -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-green-600/5"></div>
                <div class="relative p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                                <x-heroicon-s-check-badge class="h-6 w-6 text-white" />
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Geverifieerd</h3>
                            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ number_format($stats['verified']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Online Users -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 to-pink-600/5"></div>
                <div class="relative p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                                <x-heroicon-s-signal class="h-6 w-6 text-white" />
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Online Nu</h3>
                            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ number_format($users->where('is_online', true)->count()) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-gray-50/50 to-white/50 dark:from-gray-800/50 dark:to-gray-900/50"></div>
            <div class="relative">
                <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <x-heroicon-s-user-group class="h-5 w-5 text-white" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Gebruikers</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Beheer alle gebruikers en hun rollen</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200/50 dark:divide-gray-700/50">
                            <thead>
                                <tr class="bg-gray-50/50 dark:bg-gray-800/50">
                                    <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400">Gebruiker</th>
                                    <th scope="col" class="px-3 py-4 text-left text-xs font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400">Email</th>
                                    <th scope="col" class="px-3 py-4 text-left text-xs font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400">Level</th>
                                    <th scope="col" class="px-3 py-4 text-left text-xs font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400">Status</th>
                                    <th scope="col" class="px-3 py-4 text-left text-xs font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400">Rollen</th>
                                    <th scope="col" class="relative py-4 pl-3 pr-6">
                                        <span class="sr-only">Acties</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200/30 dark:divide-gray-700/30">
                                @forelse($users as $user)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-all duration-200">
                                        <td class="whitespace-nowrap py-4 pl-6 pr-3">
                                            <div class="flex items-center gap-4">
                                                <div class="relative">
                                                    <img src="https://crafatar.com/avatars/{{ $user->minecraft_uuid }}?overlay=true&size=128"
                                                         alt="{{ $user->minecraft_username }}"
                                                         class="h-12 w-12 rounded-xl border-2 border-white dark:border-gray-800 shadow-lg">
                                                    @if($user->is_online)
                                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-white dark:border-gray-800 animate-pulse"></div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-900 dark:text-white">{{ $user->minecraft_username }}</div>
                                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $user->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600 dark:text-gray-400 font-mono">
                                            {{ $user->email }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center rounded-xl px-3 py-1 text-xs font-bold bg-blue-100/80 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 border border-blue-200/50 dark:border-blue-500/30">
                                                    Level {{ $user->level }}
                                                </span>
                                                @if($user->calculated_level)
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ $user->calculated_level }})</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <div class="flex flex-wrap gap-2">
                                                @if($user->minecraft_verified)
                                                    <span class="inline-flex items-center rounded-xl px-3 py-1 text-xs font-bold bg-emerald-100/80 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-200/50 dark:border-emerald-500/30">
                                                        <x-heroicon-s-check-badge class="w-3 h-3 mr-1" />
                                                        Geverifieerd
                                                    </span>
                                                @endif
                                                @if($user->is_online)
                                                    <span class="inline-flex items-center rounded-xl px-3 py-1 text-xs font-bold bg-green-100/80 text-green-700 dark:bg-green-500/20 dark:text-green-300 border border-green-200/50 dark:border-green-500/30">
                                                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse mr-2"></div>
                                                        Online
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($user->roles as $role)
                                                    <span class="inline-flex items-center rounded-xl px-2 py-1 text-xs font-bold bg-indigo-100/80 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300 border border-indigo-200/50 dark:border-indigo-500/30">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-medium">
                                            <a href="{{ route('portal.admin.users.show', $user) }}"
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
                                                    <x-heroicon-o-users class="h-8 w-8 text-gray-400 dark:text-gray-500" />
                                                </div>
                                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Geen gebruikers</h3>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Er zijn nog geen gebruikers geregistreerd.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200/50 dark:border-gray-700/50">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection 