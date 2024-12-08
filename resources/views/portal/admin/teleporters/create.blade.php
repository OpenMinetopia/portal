@extends('portal.layouts.app')

@section('title', 'Nieuwe Teleporter')
@section('header', 'Nieuwe Teleporter Aanmaken')

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ route('portal.admin.teleporters.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <x-heroicon-s-arrow-left class="w-5 h-5 mr-1" />
                Terug naar overzicht
            </a>
        </div>

        <!-- Create Form -->
        <form action="{{ route('portal.admin.teleporters.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Teleporter Informatie</h3>
                </div>
                <div class="px-4 py-5 sm:p-6 space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Naam</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-4">
                        <div class="sm:col-span-1">
                            <label for="world" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Wereld</label>
                            <input type="text" name="location[world]" id="world" value="{{ old('location.world') }}"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            @error('location.world')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-1">
                            <label for="x" class="block text-sm font-medium text-gray-700 dark:text-gray-300">X Coördinaat</label>
                            <input type="number" step="any" name="location[x]" id="x" value="{{ old('location.x') }}"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            @error('location.x')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-1">
                            <label for="y" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Y Coördinaat</label>
                            <input type="number" step="any" name="location[y]" id="y" value="{{ old('location.y') }}"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            @error('location.y')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-1">
                            <label for="z" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Z Coördinaat</label>
                            <input type="number" step="any" name="location[z]" id="z" value="{{ old('location.z') }}"
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                            @error('location.z')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Display Lines -->
                    <div x-data="{ lines: {{ json_encode(old('display_lines', [''])) }} }">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Weergave Tekst</label>
                        <div class="space-y-3">
                            <template x-for="(line, index) in lines" :key="index">
                                <div class="flex gap-2">
                                    <input type="text" :name="'display_lines[' + index + ']'" x-model="lines[index]"
                                           class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white"
                                           placeholder="Voer een tekstregel in">
                                    <button type="button" @click="lines = lines.filter((_, i) => i !== index)"
                                            class="inline-flex items-center p-2 border border-transparent rounded-md text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10">
                                        <x-heroicon-s-trash class="h-5 w-5" />
                                    </button>
                                </div>
                            </template>
                            <button type="button" @click="lines.push('')"
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <x-heroicon-s-plus class="h-4 w-4 mr-2" />
                                Regel Toevoegen
                            </button>
                        </div>
                        @error('display_lines')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="relative flex items-start">
                        <div class="flex h-6 items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800">
                        </div>
                        <div class="ml-3">
                            <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">Actief</label>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Deze teleporter is beschikbaar voor gebruik</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-4">
                <button type="button" 
                        onclick="window.location.href='{{ route('portal.admin.teleporters.index') }}'"
                        class="inline-flex items-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                    Annuleren
                </button>
                <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                    <x-heroicon-s-check class="h-4 w-4 mr-2" />
                    Teleporter Aanmaken
                </button>
            </div>
        </form>
    </div>
@endsection 