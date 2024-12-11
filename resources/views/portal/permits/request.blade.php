@extends('portal.layouts.app')

@section('title', 'Vergunning Aanvragen')
@section('header', 'Vergunning Aanvragen')

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ route('portal.permits.index') }}"
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

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Column - Form -->
            <div class="lg:col-span-2 space-y-6">
                <form action="{{ route('portal.permits.store', $permitType) }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Form Card -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $permitType->name }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $permitType->description }}</p>
                        </div>
                        <div class="px-4 py-5 sm:p-6 space-y-6">
                            @foreach($permitType->form_fields as $field)
                                <div>
                                    <label for="form_data_{{ $loop->index }}"
                                           class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ $field['label'] }}
                                        @if($field['required'])
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    <div class="mt-1">
                                        @switch($field['type'])
                                            @case('textarea')
                                                <textarea id="form_data_{{ $loop->index }}"
                                                          name="form_data[{{ $field['label'] }}]"
                                                          rows="3"
                                                          @if($field['required']) required @endif
                                                          class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">{{ old("form_data.{$field['label']}") }}</textarea>
                                                @break
                                            @case('number')
                                                <input type="number"
                                                       id="form_data_{{ $loop->index }}"
                                                       name="form_data[{{ $field['label'] }}]"
                                                       value="{{ old("form_data.{$field['label']}") }}"
                                                       @if($field['required']) required @endif
                                                       class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white">
                                                @break
                                            @case('select')
                                                <select id="form_data_{{ $loop->index }}"
                                                        name="form_data[{{ $field['label'] }}]"
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
                                                <div class="flex items-center">
                                                    <input type="checkbox"
                                                           id="form_data_{{ $loop->index }}"
                                                           name="form_data[{{ $field['label'] }}]"
                                                           value="1"
                                                           @checked(old("form_data.{$field['label']}"))
                                                           @if($field['required']) required @endif
                                                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800">
                                                    <label for="form_data_{{ $loop->index }}" class="ml-2 block text-sm text-gray-900 dark:text-white">
                                                        Ja, ik ga akkoord
                                                    </label>
                                                </div>
                                                @break
                                            @default
                                                <input type="text"
                                                       id="form_data_{{ $loop->index }}"
                                                       name="form_data[{{ $field['label'] }}]"
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

                    <!-- Bank Account Selection -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mt-6">
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
                                </div>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    Selecteer de bankrekening waarmee je de vergunning wilt betalen (€ {{ number_format($permitType->price, 2, ',', '.') }})
                                </p>
                            </div>

                            @if(collect(auth()->user()->bank_accounts)->every(fn($account) => $account['balance'] < $permitType->price))
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
                                                <p>Je hebt op geen enkele bankrekening voldoende saldo om deze vergunning aan te vragen.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end gap-4">
                        <a href="{{ route('portal.permits.index') }}"
                           class="inline-flex justify-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                            Annuleren
                        </a>
                        <button type="submit"
                                class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                            <x-heroicon-s-check class="h-4 w-4 mr-2"/>
                            Aanvragen
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Info Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Informatie</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Kosten</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">€{{ number_format($permitType->price, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Verwerkingstijd</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">1-3 werkdagen</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20">
                                        Beschikbaar voor aanvraag
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Hulp nodig?</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Heb je vragen over deze vergunning of het aanvraagproces? Neem dan contact op met een medewerker van de gemeente.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
