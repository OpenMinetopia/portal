@extends('portal.layouts.app')

@section('title', 'Rol Bewerken')
@section('header', 'Rol Bewerken')

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ route('portal.admin.roles.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <x-heroicon-s-arrow-left class="w-5 h-5 mr-1" />
                Terug naar overzicht
            </a>
        </div>

        <!-- Edit Role Form -->
        <form action="{{ route('portal.admin.roles.update', $role) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Rol Informatie</h3>
                </div>
                <div class="px-4 py-5 sm:p-6 space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Naam</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Beschrijving</label>
                        <textarea name="description" id="description" rows="3"
                                  class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">{{ old('description', $role->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role Type -->
                    <div class="space-y-4">
                        <div class="relative flex items-start">
                            <div class="flex h-6 items-center">
                                <input type="checkbox" name="is_admin" id="is_admin" value="1" 
                                       {{ old('is_admin', $role->is_admin) ? 'checked' : '' }}
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800">
                            </div>
                            <div class="ml-3">
                                <label for="is_admin" class="text-sm font-medium text-gray-700 dark:text-gray-300">Admin Rol</label>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Deze rol heeft toegang tot het admin paneel</p>
                            </div>
                        </div>

                        <div class="relative flex items-start">
                            <div class="flex h-6 items-center">
                                <input type="checkbox" name="is_game_role" id="is_game_role" value="1" 
                                       {{ old('is_game_role', $role->is_game_role) ? 'checked' : '' }}
                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800">
                            </div>
                            <div class="ml-3">
                                <label for="is_game_role" class="text-sm font-medium text-gray-700 dark:text-gray-300">Game Rol</label>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Deze rol heeft speciale permissies in de game</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions -->
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Permissies</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($permissions as $permission)
                            <div class="relative flex items-start">
                                <div class="flex h-6 items-center">
                                    <input type="checkbox" name="permissions[]" id="permission_{{ $permission->id }}" 
                                           value="{{ $permission->id }}" 
                                           {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}
                                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800">
                                </div>
                                <div class="ml-3">
                                    <label for="permission_{{ $permission->id }}" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ $permission->name }}
                                    </label>
                                    @if($permission->description)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $permission->description }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-4">
                <button type="button" 
                        onclick="window.location.href='{{ route('portal.admin.roles.index') }}'"
                        class="inline-flex items-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                    Annuleren
                </button>
                <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                    <x-heroicon-s-check class="h-4 w-4 mr-2" />
                    Opslaan
                </button>
            </div>
        </form>
    </div>
@endsection 