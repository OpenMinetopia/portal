@extends('portal.layouts.app')

@section('title', 'Rollen Beheer')
@section('header', 'Rollen Beheer')

@section('content')
    <div class="space-y-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <!-- Total Roles -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-500/10 rounded-lg">
                            <x-heroicon-s-key class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Totaal Rollen</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Roles -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-red-100 dark:bg-red-500/10 rounded-lg">
                            <x-heroicon-s-shield-check class="h-6 w-6 text-red-600 dark:text-red-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Admin Rollen</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['admin_roles']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Game Roles -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-500/10 rounded-lg">
                            <x-heroicon-s-puzzle-piece class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Game Rollen</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['game_roles']) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roles Table -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Rollen</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Beheer alle rollen en hun permissies</p>
                    </div>
                    <div>
                        <a href="{{ route('portal.admin.roles.create') }}"
                           class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                            <x-heroicon-s-plus class="h-4 w-4 mr-2" />
                            Nieuwe Rol
                        </a>
                    </div>
                </div>
            </div>

            <div class="flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800/50">
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:pl-3">Naam</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Type</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Gebruikers</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Beschrijving</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-3">
                                        <span class="sr-only">Acties</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($roles as $role)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-3">
                                            <div class="flex items-center gap-x-4">
                                                <div class="h-8 w-8 flex-shrink-0 rounded-lg {{ $role->is_admin ? 'bg-red-100 dark:bg-red-500/10' : 'bg-green-100 dark:bg-green-500/10' }} flex items-center justify-center">
                                                    @if($role->is_admin)
                                                        <x-heroicon-s-shield-check class="h-5 w-5 text-red-600 dark:text-red-400" />
                                                    @else
                                                        <x-heroicon-s-puzzle-piece class="h-5 w-5 text-green-600 dark:text-green-400" />
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900 dark:text-white">{{ $role->name }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $role->slug }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            @if($role->is_admin)
                                                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/20">
                                                    Admin
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-400/10 dark:text-green-400 dark:ring-green-400/20">
                                                    Game
                                                </span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $role->users_count }} gebruikers
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ Str::limit($role->description, 50) }}
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-3">
                                            <a href="{{ route('portal.admin.roles.edit', $role) }}"
                                               class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                Bewerken<span class="sr-only">, {{ $role->name }}</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-8 text-center">
                                            <div class="flex flex-col items-center">
                                                <x-heroicon-o-key class="h-12 w-12 text-gray-400 dark:text-gray-500" />
                                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen rollen</h3>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Er zijn nog geen rollen aangemaakt.</p>
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
            @if($roles->hasPages())
                <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 sm:px-6">
                    {{ $roles->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection 