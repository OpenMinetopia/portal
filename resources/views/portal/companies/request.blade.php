@extends('portal.layouts.app')

@section('title', 'Bedrijf Aanvragen')
@section('header', 'Bedrijf Aanvragen')

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ route('portal.companies.register') }}"
               class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <x-heroicon-s-arrow-left class="w-5 h-5 mr-1"/>
                Terug naar overzicht
            </a>
        </div>

        <!-- Company Type Info -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $companyType->name }}</h3>
                        @if($companyType->description)
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $companyType->description }}</p>
                        @endif
                    </div>
                    <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20">
                        €{{ number_format($companyType->price, 2) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Request Form -->
        <form action="{{ route('portal.companies.store', $companyType) }}"
              method="POST"
              x-data="formBuilder"
              class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Left Column - Main Form -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Company Name Card -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Bedrijfsgegevens</h3>
                        </div>

                        <div class="px-4 py-5 sm:p-6 space-y-6">
                            <!-- Company Name with Live Check -->
                            <div>
                                <label for="form_data_Bedrijfsnaam" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Bedrijfsnaam <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1">
                                    <div class="relative">
                                        <input type="text"
                                               name="form_data[Bedrijfsnaam]"
                                               id="form_data_Bedrijfsnaam"
                                               x-on:input="checkName($event.target.value)"
                                               value="{{ old('form_data.Bedrijfsnaam') }}"
                                               class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white"
                                               required>

                                        <!-- Availability Indicator -->
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <template x-if="nameAvailable">
                                                <x-heroicon-s-check-circle class="h-5 w-5 text-green-500 dark:text-green-400"/>
                                            </template>
                                            <template x-if="nameExists">
                                                <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-yellow-500 dark:text-yellow-400"/>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Name Check Messages -->
                                <div x-show="nameExists"
                                     x-cloak
                                     class="mt-2 text-sm text-yellow-600 dark:text-yellow-400">
                                    <div class="flex gap-x-2">
                                        <x-heroicon-s-exclamation-triangle class="h-5 w-5 shrink-0"/>
                                        <span x-text="nameMessage"></span>
                                    </div>
                                </div>
                                <div x-show="nameAvailable"
                                     x-cloak
                                     class="mt-2 text-sm text-green-600 dark:text-green-400">
                                    <div class="flex gap-x-2">
                                        <x-heroicon-s-check-circle class="h-5 w-5 shrink-0"/>
                                        <span x-text="nameMessage"></span>
                                    </div>
                                </div>
                                @error('form_data.Bedrijfsnaam')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Dynamic Form Fields -->
                            @foreach($companyType->form_fields as $field)
                                <div>
                                    <label for="form_data[{{ $field['label'] }}]"
                                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ $field['label'] }}
                                        @if($field['required'])
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    <div class="mt-1">
                                        @switch($field['type'])
                                            @case('textarea')
                                                <textarea name="form_data[{{ $field['label'] }}]"
                                                          id="form_data[{{ $field['label'] }}]"
                                                          rows="3"
                                                          @if($field['required']) required @endif
                                                          class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">{{ old("form_data.{$field['label']}") }}</textarea>
                                                @break
                                            @case('number')
                                                <input type="number"
                                                       name="form_data[{{ $field['label'] }}]"
                                                       id="form_data[{{ $field['label'] }}]"
                                                       value="{{ old("form_data.{$field['label']}") }}"
                                                       @if($field['required']) required @endif
                                                       class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                                                @break
                                            @case('select')
                                                <select name="form_data[{{ $field['label'] }}]"
                                                        id="form_data[{{ $field['label'] }}]"
                                                        @if($field['required']) required @endif
                                                        class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                                                    <option value="">Selecteer een optie</option>
                                                    @foreach($field['options'] as $option)
                                                        <option value="{{ $option }}" @selected(old("form_data.{$field['label']}") === $option)>
                                                            {{ $option }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @break
                                            @case('checkbox')
                                                <div class="flex items-center gap-x-3">
                                                    <input type="checkbox"
                                                           name="form_data[{{ $field['label'] }}]"
                                                           id="form_data[{{ $field['label'] }}]"
                                                           value="1"
                                                           @checked(old("form_data.{$field['label']}"))
                                                           @if($field['required']) required @endif
                                                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800">
                                                    <label for="form_data[{{ $field['label'] }}]" class="text-sm text-gray-500 dark:text-gray-400">
                                                        Ja, ik ga akkoord
                                                    </label>
                                                </div>
                                                @break
                                            @default
                                                <input type="text"
                                                       name="form_data[{{ $field['label'] }}]"
                                                       id="form_data[{{ $field['label'] }}]"
                                                       value="{{ old("form_data.{$field['label']}") }}"
                                                       @if($field['required']) required @endif
                                                       class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                                        @endswitch
                                    </div>
                                    @error("form_data.{$field['label']}")
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Summary Card -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Samenvatting</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $companyType->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Kosten</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">€{{ number_format($companyType->price, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Aanvrager</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ auth()->user()->minecraft_username }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Bank Account Selection -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Betaling</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Selecteer een bankrekening voor de betaling</p>
                        </div>
                        <div class="px-4 py-5 sm:p-6 space-y-4">
                            <div>
                                <label for="bank_account_uuid" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Betaalrekening <span class="text-red-500">*</span>
                                </label>
                                <div class="mt-1">
                                    <select name="bank_account_uuid" 
                                            id="bank_account_uuid"
                                            required
                                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:text-white">
                                        <option value="">Selecteer een bankrekening</option>
                                        @foreach($bank_accounts as $account)
                                            <option value="{{ $account['uuid'] }}" 
                                                    @if($account['balance'] < $companyType->price) disabled @endif>
                                                {{ $account['name'] }} (€ {{ number_format($account['balance'], 2, ',', '.') }})
                                                @if($account['balance'] < $companyType->price)
                                                    - Onvoldoende saldo
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    Selecteer de bankrekening waarmee je het bedrijf wilt registreren (€ {{ number_format($companyType->price, 2, ',', '.') }})
                                </p>
                            </div>

                            @if(collect($bank_accounts)->every(fn($account) => $account['balance'] < $companyType->price))
                                <div class="bg-red-50 dark:bg-red-500/10 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-red-400"/>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-800 dark:text-red-300">
                                                Onvoldoende saldo
                                            </h3>
                                            <div class="mt-2 text-sm text-red-700 dark:text-red-200">
                                                <p>Je hebt op geen enkele bankrekening voldoende saldo om dit bedrijf te registreren.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                <x-heroicon-s-paper-airplane class="h-4 w-4 mr-2"/>
                                Aanvraag Indienen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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

    @if(session('success'))
        <div x-data="{ show: true }"
             x-show="show"
             x-transition
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed bottom-0 right-0 m-6 w-96 max-w-full">
            <div class="rounded-lg bg-green-50 p-4 shadow-lg dark:bg-green-500/10">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-s-check-circle class="h-5 w-5 text-green-400 dark:text-green-500"/>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                            {{ session('success') }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button @click="show = false" type="button"
                                    class="inline-flex rounded-md p-1.5 text-green-500 hover:bg-green-100 dark:text-green-400 dark:hover:bg-green-500/20">
                                <span class="sr-only">Sluiten</span>
                                <x-heroicon-s-x-mark class="h-5 w-5"/>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            document.addEventListener('alpine:init', () => {
                Alpine.data('formBuilder', () => ({
                    nameExists: false,
                    nameMessage: '',
                    nameAvailable: false,
                    async checkName(name) {
                        if (name.length > 2) {
                            try {
                                const response = await fetch(`{{ route('portal.companies.lookup') }}?name=${encodeURIComponent(name)}`);
                                const data = await response.json();

                                console.log('Name check response:', data); // Debug log

                                this.nameExists = data.exists;
                                this.nameMessage = data.message;
                                this.nameAvailable = !data.exists;
                            } catch (error) {
                                console.error('Error checking name:', error);
                            }
                        } else {
                            this.nameExists = false;
                            this.nameMessage = '';
                            this.nameAvailable = false;
                        }
                    },
                    fields: @json(old('form_fields', [])),
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
