@extends('portal.layouts.app')

@section('title', 'Gebruiker Bewerken')
@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->minecraft_username }} Bewerken</h1>
        <a href="{{ route('portal.admin.users.show', $user) }}" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
            <x-heroicon-s-x-mark class="h-4 w-4 mr-2"/>
            Annuleren
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ route('portal.admin.users.show', $user) }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <x-heroicon-s-arrow-left class="w-5 h-5 mr-1" />
                Terug naar details
            </a>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Column - Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info Form -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-x-4">
                            <img src="https://crafatar.com/avatars/{{ $user->minecraft_uuid }}?overlay=true&size=128"
                                 alt="{{ $user->minecraft_username }}"
                                 class="h-16 w-16 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Basis Informatie</h3>
                                <p class="mt-1 text-sm text-gray-500">Bewerk de basis gegevens van de gebruiker</p>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('portal.admin.users.update', $user) }}" method="POST" class="px-4 py-5 sm:p-6">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Naam</label>
                                <div class="mt-1">
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           value="{{ old('name', $user->name) }}"
                                           class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                                </div>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <div class="mt-1">
                                    <input type="email" 
                                           name="email" 
                                           id="email" 
                                           value="{{ old('email', $user->email) }}"
                                           class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                                </div>
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Minecraft Username -->
                            <div>
                                <label for="minecraft_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Minecraft Gebruikersnaam</label>
                                <div class="mt-1">
                                    <input type="text" 
                                           name="minecraft_username" 
                                           id="minecraft_username" 
                                           value="{{ old('minecraft_username', $user->minecraft_username) }}"
                                           class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                                </div>
                                @error('minecraft_username')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                    <x-heroicon-s-check class="h-4 w-4 mr-2"/>
                                    Opslaan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Current Stats -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Huidige Statistieken</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Huidige baan</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->prefix }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Level</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->level }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fitheid</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->fitness }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Bank Saldo</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->formatted_balance_with_currency }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Role Assignment -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Rollen Toewijzen</h3>
                    </div>
                    <form action="{{ route('portal.admin.users.roles.update', $user) }}" method="POST" class="px-4 py-5 sm:p-6">
                        @csrf

                        <div class="space-y-4">
                            @foreach($roles as $role)
                                <div class="relative flex items-start p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <div class="flex h-6 items-center">
                                        <input type="checkbox" 
                                               id="role_{{ $role->id }}" 
                                               name="roles[]" 
                                               value="{{ $role->id }}"
                                               {{ $user->roles->contains($role) ? 'checked' : '' }}
                                               class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700">
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <label for="role_{{ $role->id }}" class="text-sm font-medium text-gray-900 dark:text-white">{{ $role->name }}</label>
                                        @if($role->description)
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $role->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                <x-heroicon-s-check class="h-4 w-4 mr-2"/>
                                Rollen Opslaan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Stats Overview -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Statistieken</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Account Status</dt>
                                <dd class="mt-1">
                                    <span @class([
                                        'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                        'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' => $user->is_online,
                                        'bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-500/10 dark:text-gray-400 dark:ring-gray-500/20' => !$user->is_online,
                                    ])>
                                        {{ $user->is_online ? 'Online' : 'Offline' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Speeltijd</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->playtime }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Minecraft UUID</dt>
                                <dd class="mt-1 text-sm font-mono text-gray-900 dark:text-white">{{ $user->minecraft_uuid }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 