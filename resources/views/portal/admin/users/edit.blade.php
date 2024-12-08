@extends('portal.layouts.app')

@section('title', 'Gebruiker Bewerken')
@section('header', 'Gebruiker Bewerken')

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ route('portal.admin.users.show', $user) }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <x-heroicon-s-arrow-left class="w-5 h-5 mr-1" />
                Terug naar details
            </a>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Basic Info Form -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Basis Informatie</h3>
                </div>
                <form action="{{ route('portal.admin.users.update', $user) }}" method="POST" class="px-4 py-5 sm:p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Naam</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Minecraft Username -->
                        <div>
                            <label for="minecraft_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Minecraft Gebruikersnaam</label>
                            <input type="text" name="minecraft_username" id="minecraft_username" value="{{ old('minecraft_username', $user->minecraft_username) }}"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            @error('minecraft_username')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Level -->
                        <div>
                            <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Level</label>
                            <input type="number" name="level" id="level" value="{{ old('level', $user->level) }}"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            @error('level')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                <x-heroicon-s-check class="h-4 w-4 mr-2" />
                                Opslaan
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Appearance Settings -->
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
                                <div class="flex items-center">
                                    <input type="checkbox" id="role_{{ $role->id }}" name="roles[]" value="{{ $role->id }}"
                                           {{ $user->roles->contains($role) ? 'checked' : '' }}
                                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900">
                                    <label for="role_{{ $role->id }}" class="ml-3">
                                        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $role->name }}</span>
                                        @if($role->description)
                                            <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $role->description }}</span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                <x-heroicon-s-check class="h-4 w-4 mr-2" />
                                Rollen Opslaan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Appearance Settings -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Uiterlijk Instellingen</h3>
                    </div>
                    <form action="{{ route('portal.admin.users.update', $user) }}" method="POST" class="px-4 py-5 sm:p-6">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- Default Prefix -->
                            <div>
                                <label for="default_prefix" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Standaard Prefix</label>
                                <input type="text" name="default_prefix" id="default_prefix" value="{{ old('default_prefix', $user->default_prefix) }}"
                                       class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            </div>

                            <!-- Prefix Color -->
                            <div>
                                <label for="prefix_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prefix Kleur</label>
                                <input type="text" name="prefix_color" id="prefix_color" value="{{ old('prefix_color', $user->prefix_color) }}"
                                       class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            </div>

                            <!-- Level Color -->
                            <div>
                                <label for="level_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Level Kleur</label>
                                <input type="text" name="level_color" id="level_color" value="{{ old('level_color', $user->level_color) }}"
                                       class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            </div>

                            <!-- Name Color -->
                            <div>
                                <label for="name_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Naam Kleur</label>
                                <input type="text" name="name_color" id="name_color" value="{{ old('name_color', $user->name_color) }}"
                                       class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            </div>

                            <!-- Chat Color -->
                            <div>
                                <label for="chat_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Chat Kleur</label>
                                <input type="text" name="chat_color" id="chat_color" value="{{ old('chat_color', $user->chat_color) }}"
                                       class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                    <x-heroicon-s-check class="h-4 w-4 mr-2" />
                                    Uiterlijk Opslaan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 