@extends('portal.layouts.app')

@section('title', 'Nieuw Bedrijfstype')
@section('header', 'Nieuw Bedrijfstype')

@section('content')
    <div class="space-y-6" x-data="formBuilder">
        <!-- Back Button -->
        <div>
            <a href="{{ route('portal.admin.companies.types.index') }}"
               class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <x-heroicon-s-arrow-left class="w-5 h-5 mr-1"/>
                Terug naar overzicht
            </a>
        </div>

        @if($errors->any())
            <div class="rounded-md bg-red-50 p-4 dark:bg-red-500/10">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-heroicon-s-x-circle class="h-5 w-5 text-red-400 dark:text-red-500"/>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                            Er zijn {{ $errors->count() }} fouten gevonden:
                        </h3>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                            <ul role="list" class="list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('portal.admin.companies.types.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Left Column - Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Info Card -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Basis Informatie</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6 space-y-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Naam <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1">
                                    <input type="text" name="name" id="name"
                                           value="{{ old('name') }}"
                                           class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white"
                                           required>
                                </div>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Beschrijving
                                </label>
                                <div class="mt-1">
                                    <textarea name="description" id="description" rows="3"
                                              class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">{{ old('description') }}</textarea>
                                </div>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Price -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Prijs <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">€</span>
                                    </div>
                                    <input type="number" name="price" id="price"
                                           value="{{ old('price', '0.00') }}"
                                           min="0" step="0.01"
                                           class="appearance-none block w-full pl-7 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white"
                                           required>
                                </div>
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status Toggle -->
                            <div x-data="{ enabled: {{ old('is_active', 'true') === 'true' ? 'true' : 'false' }} }">
                                <div class="flex items-center justify-between">
                                    <div class="flex-grow flex flex-col">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            Dit bedrijfstype is beschikbaar voor aanvragen
                                        </span>
                                    </div>
                                    <button type="button"
                                            x-on:click="enabled = !enabled"
                                            :class="enabled ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700'"
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                            role="switch">
                                        <span
                                            :class="enabled ? 'translate-x-5' : 'translate-x-0'"
                                            class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out">
                                            <span
                                                :class="enabled ? 'opacity-0 duration-100 ease-out' : 'opacity-100 duration-200 ease-in'"
                                                class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity">
                                                <x-heroicon-s-x-mark class="h-3 w-3 text-gray-400"/>
                                            </span>
                                            <span
                                                :class="enabled ? 'opacity-100 duration-200 ease-in' : 'opacity-0 duration-100 ease-out'"
                                                class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity">
                                                <x-heroicon-s-check class="h-3 w-3 text-indigo-600"/>
                                            </span>
                                        </span>
                                    </button>
                                    <input type="hidden" name="is_active" :value="enabled ? '1' : '0'">
                                </div>
                                @error('is_active')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Builder Card -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Formulier Velden</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Beheer de velden die ingevuld moeten worden bij het aanvragen
                                    </p>
                                </div>
                                <button type="button" @click="addField"
                                        class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                    <x-heroicon-s-plus class="h-4 w-4 mr-2"/>
                                    Veld Toevoegen
                                </button>
                            </div>
                        </div>

                        <div class="px-4 py-5 sm:p-6">
                            <div class="space-y-4">
                                <template x-for="(field, index) in fields" :key="index">
                                    <div class="relative p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <div class="absolute top-4 right-4">
                                            <button type="button" @click="removeField(index)"
                                                    class="inline-flex items-center rounded-md p-1.5 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10">
                                                <x-heroicon-s-trash class="h-5 w-5"/>
                                            </button>
                                        </div>

                                        <div class="space-y-4 pr-8">
                                            <!-- Field Label -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Label <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text" x-model="field.label"
                                                       :name="`form_fields[${index}][label]`"
                                                       class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white"
                                                       required>
                                            </div>

                                            <!-- Field Type -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Type <span class="text-red-500">*</span>
                                                </label>
                                                <select x-model="field.type"
                                                        :name="`form_fields[${index}][type]`"
                                                        class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                                                    <option value="text">Tekst</option>
                                                    <option value="textarea">Tekstveld</option>
                                                    <option value="number">Nummer</option>
                                                    <option value="select">Keuzelijst</option>
                                                    <option value="checkbox">Checkbox</option>
                                                </select>
                                            </div>

                                            <!-- Select Options -->
                                            <div x-show="field.type === 'select'" class="space-y-2">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Opties <span class="text-red-500">*</span>
                                                </label>
                                                <template x-for="(option, optionIndex) in field.options" :key="optionIndex">
                                                    <div class="flex gap-2">
                                                        <input type="text" x-model="field.options[optionIndex]"
                                                               :name="`form_fields[${index}][options][]`"
                                                               class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white"
                                                               placeholder="Optie waarde">
                                                        <button type="button" @click="removeOption(index, optionIndex)"
                                                                class="inline-flex items-center rounded-md p-1.5 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10">
                                                            <x-heroicon-s-x-mark class="h-5 w-5"/>
                                                        </button>
                                                    </div>
                                                </template>
                                                <button type="button" @click="addOption(index)"
                                                        class="inline-flex items-center text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                    <x-heroicon-s-plus class="h-4 w-4 mr-1"/>
                                                    Optie Toevoegen
                                                </button>
                                            </div>

                                            <!-- Required Toggle -->
                                            <div x-data="{ enabled: field.required }" class="flex items-center gap-x-3">
                                                <button type="button"
                                                        x-on:click="enabled = !enabled; field.required = enabled"
                                                        :class="enabled ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700'"
                                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                                        role="switch">
                                                    <span
                                                        :class="enabled ? 'translate-x-5' : 'translate-x-0'"
                                                        class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out">
                                                        <span
                                                            :class="enabled ? 'opacity-0 duration-100 ease-out' : 'opacity-100 duration-200 ease-in'"
                                                            class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity">
                                                            <x-heroicon-s-x-mark class="h-3 w-3 text-gray-400"/>
                                                        </span>
                                                        <span
                                                            :class="enabled ? 'opacity-100 duration-200 ease-in' : 'opacity-0 duration-100 ease-out'"
                                                            class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity">
                                                            <x-heroicon-s-check class="h-3 w-3 text-indigo-600"/>
                                                        </span>
                                                    </span>
                                                </button>
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    Verplicht veld
                                                </span>
                                                <input type="hidden" :name="`form_fields[${index}][required]`" :value="enabled ? '1' : '0'">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="fields.length === 0" class="text-center py-6">
                                    <x-heroicon-o-document-text class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"/>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen velden</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Begin met het toevoegen van een formulier veld.
                                    </p>
                                    <div class="mt-6">
                                        <button type="button" @click="addField"
                                                class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                            <x-heroicon-s-plus class="h-4 w-4 mr-2"/>
                                            Veld Toevoegen
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @error('form_fields')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Authorized Roles Card -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Toegestane Rollen</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Selecteer welke rollen deze bedrijven mogen beheren
                            </p>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <div class="space-y-4">
                                @foreach($roles as $role)
                                    <div class="relative flex items-start">
                                        <div class="flex h-6 items-center">
                                            <input type="checkbox" name="authorized_roles[]" id="role_{{ $role->id }}"
                                                   value="{{ $role->id }}"
                                                   @checked(in_array($role->id, old('authorized_roles', [])))
                                                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800">
                                        </div>
                                        <div class="ml-3">
                                            <label for="role_{{ $role->id }}"
                                                   class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ $role->name }}
                                            </label>
                                            @if($role->description)
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $role->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('authorized_roles')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                <x-heroicon-s-check class="h-4 w-4 mr-2"/>
                                Bedrijfstype Opslaan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('formBuilder', () => ({
                    fields: @json(old('form_fields', $suggestedFields)),
                    addField() {
                        this.fields.push({
                            type: 'text',
                            label: '',
                            required: false,
                            options: []
                        });
                    },
                    removeField(index) {
                        this.fields.splice(index, 1);
                    },
                    addOption(fieldIndex) {
                        if (!this.fields[fieldIndex].options) {
                            this.fields[fieldIndex].options = [];
                        }
                        this.fields[fieldIndex].options.push('');
                    },
                    removeOption(fieldIndex, optionIndex) {
                        this.fields[fieldIndex].options.splice(optionIndex, 1);
                    }
                }));
            });
        </script>
    @endpush
@endsection 