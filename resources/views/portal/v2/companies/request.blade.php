@extends('portal.layouts.v2.app')

@section('title', 'Bedrijf Aanvragen')
@section('header', 'Bedrijf Aanvragen')

@section('content')
    <div class="space-y-8">
        <!-- Back Navigation -->
        <div>
            <a href="{{ route('portal.companies.register') }}"
               class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors duration-200">
                <x-heroicon-s-arrow-left class="w-5 h-5"/>
                Terug naar overzicht
            </a>
        </div>

        <!-- Page Header -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500/10 via-blue-500/10 to-purple-500/10 backdrop-blur-sm border border-white/20 p-8">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
            <div class="relative">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <x-heroicon-s-building-office class="w-8 h-8 text-white"/>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 dark:text-white">{{ $companyType->name }}</h1>
                        <p class="text-lg text-gray-600 dark:text-gray-300">Registratie aanvraag</p>
                    </div>
                </div>
                @if($companyType->description)
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">{{ $companyType->description }}</p>
                @endif
            </div>
        </div>

        <!-- Request Form -->
        <form action="{{ route('portal.companies.store', $companyType) }}"
              method="POST"
              x-data="formBuilder"
              class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Main Form -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Company Details Card -->
                    <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
                        <div class="relative">
                            <!-- Header -->
                            <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center">
                                        <x-heroicon-s-document-text class="h-5 w-5 text-white" />
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Bedrijfsgegevens</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Vul de gevraagde informatie in</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Fields -->
                            <div class="p-6 space-y-6">
                                <!-- Company Name with Live Check -->
                                <div>
                                    <label for="form_data_Bedrijfsnaam" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Bedrijfsnaam <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text"
                                               name="form_data[Bedrijfsnaam]"
                                               id="form_data_Bedrijfsnaam"
                                               x-on:input="checkName($event.target.value)"
                                               value="{{ old('form_data.Bedrijfsnaam') }}"
                                               class="w-full px-4 py-3 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                                               placeholder="Bijv. Mijn Geweldige Bedrijf BV"
                                               required>

                                        <!-- Availability Indicator -->
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                                            <template x-if="nameAvailable">
                                                <div class="w-6 h-6 bg-gradient-to-r from-emerald-500 to-green-600 rounded-lg flex items-center justify-center">
                                                    <x-heroicon-s-check class="h-4 w-4 text-white"/>
                                                </div>
                                            </template>
                                            <template x-if="nameExists">
                                                <div class="w-6 h-6 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-lg flex items-center justify-center">
                                                    <x-heroicon-s-exclamation-triangle class="h-4 w-4 text-white"/>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Name Check Messages -->
                                    <div x-show="nameExists" x-cloak class="mt-3 p-3 bg-yellow-500/10 backdrop-blur-sm border border-yellow-500/20 rounded-xl">
                                        <div class="flex items-center gap-2">
                                            <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-yellow-500"/>
                                            <span class="text-sm text-yellow-700 dark:text-yellow-300" x-text="nameMessage"></span>
                                        </div>
                                    </div>
                                    <div x-show="nameAvailable" x-cloak class="mt-3 p-3 bg-emerald-500/10 backdrop-blur-sm border border-emerald-500/20 rounded-xl">
                                        <div class="flex items-center gap-2">
                                            <x-heroicon-s-check-circle class="h-5 w-5 text-emerald-500"/>
                                            <span class="text-sm text-emerald-700 dark:text-emerald-300" x-text="nameMessage"></span>
                                        </div>
                                    </div>
                                    @error('form_data.Bedrijfsnaam')
                                        <div class="mt-3 p-3 bg-red-500/10 backdrop-blur-sm border border-red-500/20 rounded-xl">
                                            <div class="flex items-center gap-2">
                                                <x-heroicon-s-x-circle class="h-5 w-5 text-red-500"/>
                                                <span class="text-sm text-red-700 dark:text-red-300">{{ $message }}</span>
                                            </div>
                                        </div>
                                    @enderror
                                </div>

                                <!-- Dynamic Form Fields -->
                                @foreach($companyType->form_fields as $field)
                                    <div>
                                        <label for="form_data[{{ $field['label'] }}]" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            {{ $field['label'] }}
                                            @if($field['required'])
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        <div class="relative">
                                            @switch($field['type'])
                                                @case('textarea')
                                                    <textarea name="form_data[{{ $field['label'] }}]"
                                                              id="form_data[{{ $field['label'] }}]"
                                                              rows="4"
                                                              @if($field['required']) required @endif
                                                              class="w-full px-4 py-3 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 resize-none"
                                                              placeholder="Geef een gedetailleerde beschrijving...">{{ old("form_data.{$field['label']}") }}</textarea>
                                                    @break
                                                @case('number')
                                                    <input type="number"
                                                           name="form_data[{{ $field['label'] }}]"
                                                           id="form_data[{{ $field['label'] }}]"
                                                           value="{{ old("form_data.{$field['label']}") }}"
                                                           @if($field['required']) required @endif
                                                           class="w-full px-4 py-3 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                                                           placeholder="Vul een getal in...">
                                                    @break
                                                @case('select')
                                                    <select name="form_data[{{ $field['label'] }}]"
                                                            id="form_data[{{ $field['label'] }}]"
                                                            @if($field['required']) required @endif
                                                            class="w-full px-4 py-3 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 rounded-xl text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200">
                                                        <option value="">Selecteer een optie</option>
                                                        @foreach($field['options'] as $option)
                                                            <option value="{{ $option }}" @selected(old("form_data.{$field['label']}") === $option)>
                                                                {{ $option }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @break
                                                @case('checkbox')
                                                    <div class="flex items-center gap-3 p-4 bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm rounded-xl border border-gray-200/50 dark:border-gray-700/50">
                                                        <input type="checkbox"
                                                               name="form_data[{{ $field['label'] }}]"
                                                               id="form_data[{{ $field['label'] }}]"
                                                               value="1"
                                                               @checked(old("form_data.{$field['label']}"))
                                                               @if($field['required']) required @endif
                                                               class="w-5 h-5 text-emerald-600 bg-white border-gray-300 rounded focus:ring-emerald-500 focus:ring-2">
                                                        <label for="form_data[{{ $field['label'] }}]" class="text-sm text-gray-700 dark:text-gray-300">
                                                            Ja, ik ga akkoord met de voorwaarden
                                                        </label>
                                                    </div>
                                                    @break
                                                @default
                                                    <input type="text"
                                                           name="form_data[{{ $field['label'] }}]"
                                                           id="form_data[{{ $field['label'] }}]"
                                                           value="{{ old("form_data.{$field['label']}") }}"
                                                           @if($field['required']) required @endif
                                                           class="w-full px-4 py-3 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                                                           placeholder="Vul hier je antwoord in...">
                                            @endswitch
                                        </div>
                                        @error("form_data.{$field['label']}")
                                            <div class="mt-3 p-3 bg-red-500/10 backdrop-blur-sm border border-red-500/20 rounded-xl">
                                                <div class="flex items-center gap-2">
                                                    <x-heroicon-s-x-circle class="h-5 w-5 text-red-500"/>
                                                    <span class="text-sm text-red-700 dark:text-red-300">{{ $message }}</span>
                                                </div>
                                            </div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Summary & Payment -->
                <div class="space-y-8">
                    <!-- Summary Card -->
                    <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
                        <div class="relative">
                            <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center">
                                        <x-heroicon-s-document-check class="h-5 w-5 text-white" />
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Samenvatting</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Overzicht van je aanvraag</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Type</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $companyType->name }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Kosten</span>
                                    <span class="text-lg font-black text-emerald-600 dark:text-emerald-400">€{{ number_format($companyType->price, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Aanvrager</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->minecraft_username }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bank Account Selection -->
                    <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
                        <div class="relative">
                            <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center">
                                        <x-heroicon-s-credit-card class="h-5 w-5 text-white" />
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Betaling</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Selecteer je betaalrekening</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <label for="bank_account_uuid" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Betaalrekening <span class="text-red-500">*</span>
                                    </label>
                                    <select name="bank_account_uuid" 
                                            id="bank_account_uuid"
                                            required
                                            class="w-full px-4 py-3 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 rounded-xl text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200">
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
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Selecteer de rekening waarmee je de registratiekosten wilt betalen
                                    </p>
                                </div>

                                @if(collect($bank_accounts)->every(fn($account) => $account['balance'] < $companyType->price))
                                    <div class="p-4 bg-red-500/10 backdrop-blur-sm border border-red-500/20 rounded-xl">
                                        <div class="flex items-start gap-3">
                                            <div class="w-6 h-6 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <x-heroicon-s-exclamation-triangle class="h-4 w-4 text-white"/>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-bold text-red-800 dark:text-red-300 mb-1">Onvoldoende saldo</h4>
                                                <p class="text-xs text-red-700 dark:text-red-200">
                                                    Je hebt op geen enkele bankrekening voldoende saldo om dit bedrijf te registreren.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="card-hover relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-500 to-blue-600 p-6 shadow-lg">
                        <div class="relative">
                            <button type="submit"
                                    class="w-full flex justify-center items-center gap-2 px-6 py-4 text-lg font-bold text-white bg-white/20 hover:bg-white/30 backdrop-blur-sm border border-white/30 rounded-xl transition-all duration-200 hover:shadow-lg">
                                <x-heroicon-s-rocket-launch class="h-6 w-6"/>
                                Aanvraag Indienen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="card-hover relative overflow-hidden rounded-2xl bg-red-500/10 backdrop-blur-sm border border-red-500/20 p-6 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 to-red-600/5"></div>
                <div class="relative">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-red-600 rounded-xl flex items-center justify-center flex-shrink-0">
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
            </div>
        @endif
    </div>



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

                                this.nameExists = data.exists;
                                this.nameMessage = data.message;
                                this.nameAvailable = !data.exists;
                            } catch (error) {
                                console.error('Error checking name:', error);
                                this.nameExists = false;
                                this.nameMessage = '';
                                this.nameAvailable = false;
                            }
                        } else {
                            this.nameExists = false;
                            this.nameMessage = '';
                            this.nameAvailable = false;
                        }
                    }
                }));
            });
        </script>
    @endpush
@endsection 