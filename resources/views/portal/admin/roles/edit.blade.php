@extends('layouts.app')

@section('title', 'Edit Role')
@section('header', 'Edit Role')

@section('content')
<div class="space-y-6">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Role Details</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Update role information and permissions.
                </p>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="shadow sm:rounded-md">
                    <div class="px-4 py-5 bg-white dark:bg-gray-800 sm:p-6 space-y-6">
                        <!-- Role Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Role Name
                            </label>
                            <input type="text" name="name" id="name" 
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                value="{{ old('name', $role->name) }}" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Description
                            </label>
                            <textarea name="description" id="description" rows="3" 
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('description', $role->description) }}</textarea>
                        </div>

                        <!-- Role Type -->
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_admin" id="is_admin" 
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded"
                                    {{ old('is_admin', $role->is_admin) ? 'checked' : '' }}>
                                <label for="is_admin" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                    Administrator Role
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_game_role" id="is_game_role" 
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded"
                                    {{ old('is_game_role', $role->is_game_role) ? 'checked' : '' }}>
                                <label for="is_game_role" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                    Game Role
                                </label>
                            </div>
                        </div>

                        <!-- Permissions -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Permissions</h3>
                            <div class="space-y-6">
                                @foreach($permissions->groupBy('group') as $group => $groupPermissions)
                                    <div>
                                        <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                                            {{ Str::title($group) }}
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            @foreach($groupPermissions as $permission)
                                                <div class="flex items-center">
                                                    <input type="checkbox" name="permissions[]" id="permission_{{ $permission->id }}" 
                                                        value="{{ $permission->id }}"
                                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded"
                                                        {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                    <label for="permission_{{ $permission->id }}" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/50 text-right sm:px-6 space-x-2">
                        <a href="{{ route('admin.roles.index') }}" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Cancel
                        </a>
                        <button type="submit" 
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Update Role
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 