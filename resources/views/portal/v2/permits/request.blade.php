@extends('portal.layouts.v2.app')

@section('title', 'Vergunning Aanvragen')
@section('header', 'Vergunning Aanvragen')

@section('content')
    <div class="space-y-8">
        <!-- Back Navigation -->
        <div>
            <a href="{{ route('portal.permits.index') }}"
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
                        <x-heroicon-s-document-text class="w-8 h-8 text-white"/>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 dark:text-white">{{ $permitType->name }}</h1>
                        <p class="text-lg text-gray-600 dark:text-gray-300">Vergunning aanvragen</p>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">{{ $permitType->description }}</p>
            </div>
        </div>

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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Form -->
            <div class="lg:col-span-2 space-y-8">
                <form action="{{ route('portal.permits.store', $permitType) }}" method="POST" class="space-y-8">
                    @csrf

                    <!-- Form Fields Card -->
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
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Aanvraagformulier</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Vul alle gevraagde informatie in</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Fields -->
                            <div class="p-6 space-y-6">
                                @foreach($permitType->form_fields as $field)
                                    <div>
                                        <label for="form_data_{{ $loop->index }}" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                            {{ $field['label'] }}
                                            @if($field['required'])
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        <div class="relative">
                                            @switch($field['type'])
                                                @case('textarea')
                                                    <textarea id="form_data_{{ $loop->index }}"
                                                              name="form_data[{{ $field['label'] }}]"
                                                              rows="4"
                                                              @if($field['required']) required @endif
                                                              class="w-full px-4 py-3 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 resize-none"
                                                              placeholder="Geef een gedetailleerde beschrijving...">{{ old("form_data.{$field['label']}") }}</textarea>
                                                    @break
                                                @case('number')
                                                    <input type="number"
                                                           id="form_data_{{ $loop->index }}"
                                                           name="form_data[{{ $field['label'] }}]"
                                                           value="{{ old("form_data.{$field['label']}") }}"
                                                           @if($field['required']) required @endif
                                                           class="w-full px-4 py-3 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                                                           placeholder="Vul een getal in...">
                                                    @break
                                                @case('select')
                                                    <select id="form_data_{{ $loop->index }}"
                                                            name="form_data[{{ $field['label'] }}]"
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
                                                               id="form_data_{{ $loop->index }}"
                                                               name="form_data[{{ $field['label'] }}]"
                                                               value="1"
                                                               @checked(old("form_data.{$field['label']}"))
                                                               @if($field['required']) required @endif
                                                               class="w-5 h-5 text-emerald-600 bg-white border-gray-300 rounded focus:ring-emerald-500 focus:ring-2">
                                                        <label for="form_data_{{ $loop->index }}" class="text-sm text-gray-700 dark:text-gray-300">
                                                            Ja, ik ga akkoord met de voorwaarden
                                                        </label>
                                                    </div>
                                                    @break
                                                @default
                                                    <input type="text"
                                                           id="form_data_{{ $loop->index }}"
                                                           name="form_data[{{ $field['label'] }}]"
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
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Betaling</h3>
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
                                        @foreach(auth()->user()->bank_accounts as $account)
                                            <option value="{{ $account['uuid'] }}" 
                                                    @if($account['balance'] < $permitType->price) disabled @endif>
                                                {{ $account['name'] }} (€ {{ number_format($account['balance'], 2, ',', '.') }})
                                                @if($account['balance'] < $permitType->price)
                                                    - Onvoldoende saldo
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        Selecteer de rekening waarmee je de vergunning wilt betalen (€ {{ number_format($permitType->price, 2, ',', '.') }})
                                    </p>
                                </div>

                                @if(collect(auth()->user()->bank_accounts)->every(fn($account) => $account['balance'] < $permitType->price))
                                    <div class="p-4 bg-red-500/10 backdrop-blur-sm border border-red-500/20 rounded-xl">
                                        <div class="flex items-start gap-3">
                                            <div class="w-6 h-6 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <x-heroicon-s-exclamation-triangle class="h-4 w-4 text-white"/>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-bold text-red-800 dark:text-red-300 mb-1">Onvoldoende saldo</h4>
                                                <p class="text-xs text-red-700 dark:text-red-200">
                                                    Je hebt op geen enkele bankrekening voldoende saldo om deze vergunning aan te vragen.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-end">
                        <a href="{{ route('portal.permits.index') }}"
                           class="inline-flex justify-center items-center gap-2 px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 rounded-xl hover:bg-white dark:hover:bg-gray-800 transition-all duration-200">
                            <x-heroicon-s-x-mark class="w-4 h-4"/>
                            Annuleren
                        </a>
                        <button type="submit"
                                class="inline-flex justify-center items-center gap-2 px-8 py-3 text-sm font-bold text-white bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl hover:shadow-lg transition-all duration-200">
                            <x-heroicon-s-check class="w-5 h-5"/>
                            Vergunning Aanvragen
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Column - Info -->
            <div class="space-y-8">
                <!-- Summary Card -->
                <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
                    <div class="relative">
                        <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center">
                                    <x-heroicon-s-information-circle class="h-5 w-5 text-white" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Informatie</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Details over deze vergunning</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Kosten</span>
                                <span class="text-lg font-black text-emerald-600 dark:text-emerald-400">€{{ number_format($permitType->price, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Verwerkingstijd</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">1-3 werkdagen</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</span>
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">
                                    <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                    <span class="text-xs font-semibold">Beschikbaar</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="card-hover relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500/10 via-purple-500/10 to-pink-500/10 backdrop-blur-sm border border-blue-200/50 dark:border-blue-700/50 shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-600/5"></div>
                    <div class="relative">
                        <div class="p-6 border-b border-blue-200/50 dark:border-blue-700/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                    <x-heroicon-s-question-mark-circle class="h-5 w-5 text-white" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Hulp Nodig?</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">We helpen je graag</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <x-heroicon-s-chat-bubble-left class="h-4 w-4 text-white" />
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Contact opnemen</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                            Heb je vragen over deze vergunning of het aanvraagproces? Neem contact op met een medewerker van de gemeente.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <x-heroicon-s-clock class="h-4 w-4 text-white" />
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Verwerkingstijd</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                            Vergunningen worden binnen 1-3 werkdagen behandeld. Je ontvangt een melding zodra er een besluit is genomen.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Process Steps -->
                <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
                    <div class="relative">
                        <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center">
                                    <x-heroicon-s-list-bullet class="h-5 w-5 text-white" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Proces</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Hoe werkt het?</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-white">1</span>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Formulier invullen</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Vul alle gevraagde informatie correct in</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-white">2</span>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Betaling</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Kosten worden direct afgeschreven</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-white">3</span>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Beoordeling</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Je aanvraag wordt beoordeeld</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-white">4</span>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Besluit</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">Je ontvangt een melding met het besluit</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 