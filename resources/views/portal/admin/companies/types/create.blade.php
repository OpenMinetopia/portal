@extends('portal.layouts.v2.app')

@section('title', 'Nieuw Bedrijfstype')
@section('header', 'Nieuw Bedrijfstype')

@section('content')
    <div class="space-y-8" x-data="formBuilder">
        <!-- Back Button -->
        <div>
            <a href="{{ route('portal.admin.companies.types.index') }}"
               class="inline-flex items-center text-sm font-medium text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                <x-heroicon-s-arrow-left class="w-5 h-5 mr-2"/>
                Terug naar overzicht
            </a>
        </div>

        <!-- Page Header Card -->
        <div class="relative overflow-hidden glass-card rounded-2xl p-8 shadow-2xl float-animation">
            <div class="absolute inset-0 bg-gradient-to-r from-orange-500/10 to-red-500/10"></div>
            <div class="relative flex items-center gap-6">
                <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <x-heroicon-s-plus class="h-8 w-8 text-white"/>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Nieuw Bedrijfstype</h1>
                    <p class="text-gray-600 dark:text-slate-400 mt-1">Creëer een nieuw bedrijfstype voor aanvragen</p>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="glass-card rounded-2xl p-6 bg-red-500/10 border border-red-500/20 shadow-lg">
                <div class="flex gap-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                        <x-heroicon-s-x-circle class="h-6 w-6 text-white"/>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-red-800 dark:text-red-200 mb-2">
                            {{ $errors->count() }} {{ $errors->count() === 1 ? 'fout gevonden' : 'fouten gevonden' }}
                        </h3>
                        <ul class="space-y-1 text-sm text-red-700 dark:text-red-300">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-center gap-2">
                                    <div class="w-1 h-1 bg-red-500 rounded-full"></div>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('portal.admin.companies.types.store') }}" method="POST" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Left Column - Main Info -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Basic Info Card -->
                    <div class="glass-card rounded-2xl shadow-lg float-animation">
                        <div class="p-6 border-b border-gray-200/50 dark:border-white/10">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <x-heroicon-s-information-circle class="h-6 w-6 text-white"/>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Basis Informatie</h3>
                                    <p class="text-gray-600 dark:text-slate-400">Configureer de basisgegevens</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 space-y-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                    Naam <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name"
                                       value="{{ old('name') }}"
                                       class="w-full bg-gray-100/50 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                       required>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                    Beschrijving
                                </label>
                                <textarea name="description" id="description" rows="3"
                                          class="w-full bg-gray-100/50 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Price -->
                            <div>
                                <label for="price" class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                    Prijs <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-gray-600 dark:text-slate-400 font-bold">€</span>
                                    </div>
                                    <input type="number" name="price" id="price"
                                           value="{{ old('price', '0.00') }}"
                                           min="0" step="0.01"
                                           class="w-full pl-8 bg-gray-100/50 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                           required>
                                </div>
                                @error('price')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status Toggle -->
                            <div x-data="{ enabled: {{ old('is_active', 'true') === 'true' ? 'true' : 'false' }} }">
                                <div class="flex items-center justify-between p-4 bg-gray-50/50 dark:bg-gray-700/30 rounded-xl">
                                    <div class="flex-grow">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">Status</span>
                                        <p class="text-sm text-gray-600 dark:text-slate-400 mt-1">
                                            Dit bedrijfstype is beschikbaar voor aanvragen
                                        </p>
                                    </div>
                                    <button type="button"
                                            x-on:click="enabled = !enabled"
                                            :class="enabled ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'"
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
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
                                                <x-heroicon-s-check class="h-3 w-3 text-blue-600"/>
                                            </span>
                                        </span>
                                    </button>
                                    <input type="hidden" name="is_active" :value="enabled ? '1' : '0'">
                                </div>
                                @error('is_active')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Builder Card -->
                    <div class="glass-card rounded-2xl shadow-lg float-animation">
                        <div class="p-6 border-b border-gray-200/50 dark:border-white/10">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <x-heroicon-s-document-text class="h-6 w-6 text-white"/>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Formulier Velden</h3>
                                        <p class="text-gray-600 dark:text-slate-400">Beheer de velden die ingevuld moeten worden bij het aanvragen</p>
                                    </div>
                                </div>
                                <button type="button" @click="addField"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl font-bold shadow-lg hover:from-emerald-600 hover:to-green-700 transition-all duration-200 transform hover:scale-105">
                                    <x-heroicon-s-plus class="h-4 w-4"/>
                                    Veld Toevoegen
                                </button>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="space-y-6">
                                <template x-for="(field, index) in fields" :key="index">
                                    <div class="relative p-6 bg-gray-50/50 dark:bg-gray-700/30 rounded-xl border border-gray-200/50 dark:border-gray-600/50">
                                        <div class="absolute top-6 right-6">
                                            <button type="button" @click="removeField(index)"
                                                    class="inline-flex items-center p-2 text-red-600 hover:bg-red-100/50 dark:text-red-400 dark:hover:bg-red-500/10 rounded-xl transition-all duration-200">
                                                <x-heroicon-s-trash class="h-5 w-5"/>
                                            </button>
                                        </div>

                                        <div class="space-y-4 pr-12">
                                            <!-- Field Label -->
                                            <div>
                                                <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                                    Label <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text" x-model="field.label"
                                                       :name="`form_fields[${index}][label]`"
                                                       class="w-full bg-white/70 dark:bg-slate-800/70 border border-gray-200 dark:border-slate-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                                                       required>
                                            </div>

                                            <!-- Field Type -->
                                            <div>
                                                <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                                    Type <span class="text-red-500">*</span>
                                                </label>
                                                <select x-model="field.type"
                                                        :name="`form_fields[${index}][type]`"
                                                        class="w-full bg-white/70 dark:bg-slate-800/70 border border-gray-200 dark:border-slate-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200">
                                                    <option value="text">Tekst</option>
                                                    <option value="textarea">Tekstveld</option>
                                                    <option value="number">Nummer</option>
                                                    <option value="select">Keuzelijst</option>
                                                    <option value="checkbox">Checkbox</option>
                                                </select>
                                            </div>

                                            <!-- Select Options -->
                                            <div x-show="field.type === 'select'" class="space-y-3">
                                                <label class="block text-sm font-bold text-gray-900 dark:text-white">
                                                    Opties <span class="text-red-500">*</span>
                                                </label>
                                                <template x-for="(option, optionIndex) in field.options" :key="optionIndex">
                                                    <div class="flex gap-3">
                                                        <input type="text" x-model="field.options[optionIndex]"
                                                               :name="`form_fields[${index}][options][]`"
                                                               class="flex-1 bg-white/70 dark:bg-slate-800/70 border border-gray-200 dark:border-slate-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200"
                                                               placeholder="Optie waarde">
                                                        <button type="button" @click="removeOption(index, optionIndex)"
                                                                class="inline-flex items-center p-2 text-red-600 hover:bg-red-100/50 dark:text-red-400 dark:hover:bg-red-500/10 rounded-xl transition-all duration-200">
                                                            <x-heroicon-s-x-mark class="h-5 w-5"/>
                                                        </button>
                                                    </div>
                                                </template>
                                                <button type="button" @click="addOption(index)"
                                                        class="inline-flex items-center gap-2 text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 font-bold transition-colors">
                                                    <x-heroicon-s-plus class="h-4 w-4"/>
                                                    Optie Toevoegen
                                                </button>
                                            </div>

                                            <!-- Required Toggle -->
                                            <div x-data="{ enabled: field.required }" class="flex items-center justify-between p-4 bg-white/70 dark:bg-slate-800/70 rounded-xl">
                                                <div>
                                                    <span class="text-sm font-bold text-gray-900 dark:text-white">Verplicht veld</span>
                                                    <p class="text-sm text-gray-600 dark:text-slate-400 mt-1">Dit veld moet ingevuld worden</p>
                                                </div>
                                                <button type="button"
                                                        x-on:click="enabled = !enabled; field.required = enabled"
                                                        :class="enabled ? 'bg-emerald-600' : 'bg-gray-300 dark:bg-gray-600'"
                                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
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
                                                            <x-heroicon-s-check class="h-3 w-3 text-emerald-600"/>
                                                        </span>
                                                    </span>
                                                </button>
                                                <input type="hidden" :name="`form_fields[${index}][required]`" :value="enabled ? '1' : '0'">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="fields.length === 0" class="text-center py-12">
                                    <div class="w-16 h-16 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-2xl flex items-center justify-center mb-4 mx-auto">
                                        <x-heroicon-o-document-text class="h-8 w-8 text-gray-400"/>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Geen velden</h3>
                                    <p class="text-gray-600 dark:text-slate-400 mb-6">
                                        Begin met het toevoegen van een formulier veld.
                                    </p>
                                    <button type="button" @click="addField"
                                            class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl font-bold shadow-lg hover:from-emerald-600 hover:to-green-700 transition-all duration-200 transform hover:scale-105">
                                        <x-heroicon-s-plus class="h-4 w-4"/>
                                        Veld Toevoegen
                                    </button>
                                </div>
                            </div>
                            @error('form_fields')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-8">
                    <!-- Authorized Roles Card -->
                    <div class="glass-card rounded-2xl shadow-lg float-animation">
                        <div class="p-6 border-b border-gray-200/50 dark:border-white/10">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <x-heroicon-s-key class="h-6 w-6 text-white"/>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Toegestane Rollen</h3>
                                    <p class="text-gray-600 dark:text-slate-400">Selecteer welke rollen deze bedrijven mogen beheren</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @foreach($roles as $role)
                                    <div class="relative flex items-start p-4 bg-gray-50/50 dark:bg-gray-700/30 rounded-xl hover:bg-gray-100/50 dark:hover:bg-gray-700/50 transition-all duration-200">
                                        <div class="flex h-6 items-center">
                                            <input type="checkbox" name="authorized_roles[]" id="role_{{ $role->id }}"
                                                   value="{{ $role->id }}"
                                                   @checked(in_array($role->id, old('authorized_roles', [])))
                                                   class="h-5 w-5 rounded border-gray-300 text-purple-600 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-700">
                                        </div>
                                        <div class="ml-3">
                                            <label for="role_{{ $role->id }}"
                                                   class="text-sm font-bold text-gray-900 dark:text-white">
                                                {{ $role->name }}
                                            </label>
                                            @if($role->description)
                                                <p class="text-sm text-gray-600 dark:text-slate-400 mt-1">{{ $role->description }}</p>
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
                    <div class="glass-card rounded-2xl shadow-lg float-animation">
                        <div class="p-6">
                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center gap-2 px-6 py-4 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-xl font-bold shadow-lg hover:from-orange-600 hover:to-red-700 transition-all duration-200 transform hover:scale-105">
                                <x-heroicon-s-check class="h-5 w-5"/>
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