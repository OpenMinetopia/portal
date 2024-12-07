@extends('layouts.app')

@section('title', 'Create Role')
@section('header', 'Create New Role')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Role Details</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Create a new role and assign permissions. Roles define what actions users can perform in the system.
                </p>
                <div class="mt-6 space-y-4">
                    <div class="flex items-center space-x-3">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Admin roles have access to all permissions</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Game roles sync with in-game permissions</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="shadow rounded-lg">
                    <!-- Basic Information -->
                    <div class="px-4 py-5 bg-white dark:bg-gray-800 sm:p-6 space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Role Name
                            </label>
                            <div class="mt-1">
                                <input type="text" name="name" id="name" 
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                                    value="{{ old('name') }}" required>
                            </div>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Description
                            </label>
                            <div class="mt-1">
                                <textarea name="description" id="description" rows="3" 
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md"
                                    >{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <!-- Role Type Selection -->
                        <div class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-10">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_admin" id="is_admin" 
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded"
                                    {{ old('is_admin') ? 'checked' : '' }}
                                    x-model="isAdmin">
                                <label for="is_admin" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Administrator Role
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_game_role" id="is_game_role" 
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded"
                                    {{ old('is_game_role') ? 'checked' : '' }}>
                                <label for="is_game_role" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Game Role
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Permissions Section -->
                    <div class="px-4 py-5 bg-gray-50 dark:bg-gray-900/50 sm:p-6 space-y-4" x-data="{ 
                        isAdmin: {{ old('is_admin') ? 'true' : 'false' }},
                        selectAll(group) {
                            const checkboxes = document.querySelectorAll(`input[data-group='${group}']`);
                            const checked = !Array.from(checkboxes).every(cb => cb.checked);
                            checkboxes.forEach(cb => cb.checked = checked);
                        }
                    }">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Permissions</h3>
                            <button type="button" @click="selectAll('all')"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 dark:text-indigo-400 bg-indigo-100 dark:bg-indigo-900/50 hover:bg-indigo-200 dark:hover:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                Select All
                            </button>
                        </div>

                        <div class="space-y-6" :class="{ 'opacity-50 pointer-events-none': isAdmin }">
                            @foreach($permissions->groupBy('group') as $group => $groupPermissions)
                                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white uppercase">
                                            {{ Str::title($group) }}
                                        </h4>
                                        <button type="button" @click="selectAll('{{ $group }}')"
                                            class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                            Toggle All
                                        </button>
                                    </div>
                                    <div class="px-4 py-3">
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            @foreach($groupPermissions as $permission)
                                                <div class="relative flex items-start">
                                                    <div class="flex items-center h-5">
                                                        <input type="checkbox" name="permissions[]" 
                                                            id="permission_{{ $permission->id }}"
                                                            value="{{ $permission->id }}"
                                                            data-group="{{ $group }}"
                                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded"
                                                            {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                    </div>
                                                    <div class="ml-3 text-sm">
                                                        <label for="permission_{{ $permission->id }}" 
                                                            class="font-medium text-gray-700 dark:text-gray-300">
                                                            {{ $permission->name }}
                                                        </label>
                                                        @if($permission->description)
                                                            <p class="text-gray-500 dark:text-gray-400">{{ $permission->description }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div x-show="isAdmin" class="bg-indigo-50 dark:bg-indigo-900/30 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-indigo-800 dark:text-indigo-300">Administrator Role</h3>
                                    <div class="mt-2 text-sm text-indigo-700 dark:text-indigo-400">
                                        <p>This role will have access to all permissions automatically.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-4 py-3 bg-white dark:bg-gray-800 text-right sm:px-6 space-x-2 rounded-b-lg">
                        <a href="{{ route('admin.roles.index') }}" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Cancel
                        </a>
                        <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Create Role
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 