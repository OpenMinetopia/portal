@extends('portal.layouts.app')

@section('title', 'Gebruiker Details')
@section('header', 'Gebruiker Details')

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ route('portal.admin.users.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <x-heroicon-s-arrow-left class="w-5 h-5 mr-1" />
                Terug naar overzicht
            </a>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Column - Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- User Details Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-x-4">
                                <img src="https://crafatar.com/avatars/{{ $user->minecraft_uuid }}?overlay=true&size=128"
                                     alt="{{ $user->minecraft_username }}"
                                     class="h-16 w-16 rounded-lg bg-gray-50 dark:bg-gray-800">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $user->minecraft_username }}</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $user->name }}</p>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('portal.admin.users.edit', $user) }}"
                                   class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                    <x-heroicon-s-pencil class="h-4 w-4 mr-2" />
                                    Bewerken
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->email }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Level</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $user->level }}
                                    @if($user->calculated_level)
                                        <span class="text-gray-500 dark:text-gray-400">({{ $user->calculated_level }})</span>
                                    @endif
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Minecraft UUID</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->minecraft_uuid }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1">
                                    <div class="flex gap-2">
                                        @if($user->minecraft_verified)
                                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20">
                                                Geverifieerd
                                            </span>
                                        @endif
                                        @if($user->is_online)
                                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20">
                                                Online
                                            </span>
                                        @endif
                                    </div>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Laatste Login</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $user->last_login ? $user->last_login->format('d M Y H:i') : 'Nooit' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Laatste Logout</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $user->last_logout ? $user->last_logout->format('d M Y H:i') : 'Nooit' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Game Stats -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Game Statistieken</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Speeltijd</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->playtime ?? '0' }} minuten</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Bank Saldo</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">${{ number_format($user->getCurrentBalance()) }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Plots</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">TO DO</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Voertuigen</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">TO DO</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Roles Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Rollen</h3>

                            <!-- Role Assignment Dropdown -->
                            <div x-data="{ open: false, confirmDelete: false, roleToDelete: null }" @keydown.escape.window="open = false; confirmDelete = false">
                                <!-- Role Assignment Button -->
                                <button @click="open = !open" type="button"
                                        class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                    <x-heroicon-s-plus class="h-4 w-4 mr-2" />
                                    Rol Toewijzen
                                </button>

                                <!-- Role Assignment Dropdown -->
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     @click.away="open = false"
                                     class="absolute right-0 mt-2 w-72 origin-top-right rounded-md bg-white dark:bg-gray-700 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                                    <form action="{{ route('portal.admin.users.roles.update', $user) }}" method="POST" class="p-4">
                                        @csrf
                                        <div class="space-y-3 max-h-60 overflow-y-auto">
                                            @foreach($roles as $role)
                                                <label class="relative flex items-start py-1">
                                                    <div class="min-w-0 flex-1 text-sm leading-6">
                                                        <div class="font-medium text-gray-900 dark:text-white select-none">{{ $role->name }}</div>
                                                        @if($role->description)
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $role->description }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="ml-3 flex h-6 items-center">
                                                        <input type="checkbox"
                                                               name="roles[]"
                                                               value="{{ $role->id }}"
                                                               {{ $user->roles->contains($role) ? 'checked' : '' }}
                                                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800">
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                        <div class="mt-4 flex justify-end">
                                            <button type="submit"
                                                    class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                                Opslaan
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Delete Role Confirmation Modal -->
                                <template x-teleport="body">
                                    <div x-show="confirmDelete"
                                         class="relative z-50"
                                         @click.away="confirmDelete = false">
                                        <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/75 transition-opacity"></div>

                                        <div class="fixed inset-0 z-50 overflow-y-auto">
                                            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                                <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm sm:p-6"
                                                     @click.away="confirmDelete = false">
                                                    <div class="sm:flex sm:items-start">
                                                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                                                            <x-heroicon-s-exclamation-triangle class="h-6 w-6 text-red-600 dark:text-red-400" />
                                                        </div>
                                                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                                            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">
                                                                Rol Verwijderen
                                                            </h3>
                                                            <div class="mt-2">
                                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                    Weet je zeker dat je deze rol wilt verwijderen?
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-5 sm:mt-4 flex gap-3 justify-end">
                                                        <button type="button"
                                                                @click="confirmDelete = false"
                                                                class="inline-flex justify-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                            Annuleren
                                                        </button>
                                                        <form :action="`{{ route('portal.admin.users.roles.update', $user) }}`" method="POST" class="inline-flex">
                                                            @csrf
                                                            <input type="hidden" name="roles[]" :value="roleToDelete">
                                                            <button type="submit"
                                                                    class="inline-flex justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                                                                Verwijderen
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <div class="space-y-2">
                            @forelse($user->roles as $role)
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-sm font-medium text-indigo-700 ring-1 ring-inset ring-indigo-600/20 dark:bg-indigo-500/10 dark:text-indigo-400 dark:ring-indigo-500/20">
                                        {{ $role->name }}
                                    </span>
                                    <button type="button"
                                            @click="confirmDelete = true; roleToDelete = {{ $role->id }}"
                                            class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                        <x-heroicon-s-x-mark class="h-5 w-5" />
                                    </button>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">Geen rollen toegewezen.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Appearance Settings -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Uiterlijk</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Standaard Prefix</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->default_prefix ?? 'Geen' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Prefix Kleur</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->prefix_color ?? 'Standaard' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Level Kleur</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->level_color ?? 'Standaard' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Naam Kleur</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->name_color ?? 'Standaard' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Chat Kleur</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->chat_color ?? 'Standaard' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
