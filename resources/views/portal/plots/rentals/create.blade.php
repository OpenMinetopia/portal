@extends('portal.layouts.app')

@php
    $isServerListing = $isServerListing ?? false;
    $storeUrl = $storeUrl ?? route('portal.plots.rentals.store', $plot['name']);
    $backUrl = $backUrl ?? route('portal.plots.show', $plot['name']);
    $input = 'block w-full py-3 sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-indigo-500 focus:border-indigo-500';
    $label = 'flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-gray-100';
@endphp

@section('title', 'Plot te huur zetten')
@section('header')
    <div class="flex items-center gap-4">
        <a href="{{ $backUrl }}"
           class="group flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
            <x-heroicon-s-arrow-left class="w-5 h-5"/>
            Terug naar plot
        </a>
    </div>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="bg-blue-50 dark:bg-blue-500/10 rounded-lg p-4">
            <div class="flex">
                <x-heroicon-s-information-circle class="h-5 w-5 flex-shrink-0 text-blue-400 dark:text-blue-300"/>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Zo werkt verhuren</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-200 space-y-1">
                        <p>De huurder betaalt de eerste periode vooruit en wordt <strong>lid</strong> van het plot.
                            {{ $isServerListing ? config('app.name').' blijft' : 'Jij blijft' }} eigenaar.</p>
                        <p>Aan het eind van elke periode wordt de huur automatisch afgeschreven. Lukt dat niet, dan
                            heeft de huurder {{ \App\Models\PlotRental::GRACE_DAYS }} dagen om te betalen; daarna eindigt
                            de huur en verliest de huurder de toegang.</p>
                        <p>Zolang het plot verhuurd is, kun je het niet verkopen.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Plot te huur zetten</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $plot['name'] }}</p>
            </div>

            <form action="{{ $storeUrl }}" method="POST" enctype="multipart/form-data"
                  class="divide-y divide-gray-200 dark:divide-gray-700">
                @csrf

                <div class="px-6 py-5 space-y-8">
                    @if($errors->any())
                        <div class="bg-red-50 dark:bg-red-500/10 rounded-lg p-4 text-sm text-red-700 dark:text-red-200">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2 space-y-2">
                            <label for="price" class="{{ $label }}">
                                <x-heroicon-s-currency-euro class="w-5 h-5 text-gray-400"/>
                                Huurprijs <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 dark:text-gray-400 sm:text-sm">€</span>
                                </div>
                                <input type="number" name="price" id="price" step="0.01" min="0.01" required
                                       value="{{ old('price') }}" placeholder="0.00" class="{{ $input }} pl-7">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label for="period" class="{{ $label }}">
                                <x-heroicon-s-calendar class="w-5 h-5 text-gray-400"/>
                                Per <span class="text-red-500">*</span>
                            </label>
                            <select name="period" id="period" required class="{{ $input }} pl-3">
                                <option value="week" @selected(old('period', 'week') === 'week')>Week</option>
                                <option value="month" @selected(old('period') === 'month')>Maand</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="payout_bank_account_uuid" class="{{ $label }}">
                            <x-heroicon-s-credit-card class="w-5 h-5 text-gray-400"/>
                            Huur ontvangen op @unless($isServerListing)<span class="text-red-500">*</span>@endunless
                        </label>
                        <select name="payout_bank_account_uuid" id="payout_bank_account_uuid" @required(!$isServerListing)
                                class="{{ $input }} pl-3">
                            <option value="">{{ $isServerListing ? 'Geen uitbetaling' : 'Selecteer een bankrekening' }}</option>
                            @foreach(auth()->user()->bank_accounts as $account)
                                <option value="{{ $account['uuid'] }}"
                                        @selected(old('payout_bank_account_uuid', !$isServerListing && $account['type'] === 'PRIVATE' ? $account['uuid'] : null) === $account['uuid'])>
                                    {{ $account['name'] }} (€ {{ number_format($account['balance'], 2, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $isServerListing ? 'Optioneel. Zonder bankrekening verdwijnt de huur uit de economie.' : 'Elke betaling van de huurder komt op deze rekening.' }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <label for="description" class="{{ $label }}">
                            <x-heroicon-s-document-text class="w-5 h-5 text-gray-400"/>
                            Beschrijving <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="5" maxlength="1000" required
                                  placeholder="Wat krijgt de huurder? Denk aan de ligging, wat er al staat en wat er wel en niet mag."
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white p-4">{{ old('description') }}</textarea>
                    </div>

                    <div class="space-y-2">
                        <label for="image" class="{{ $label }}">
                            <x-heroicon-s-photo class="w-5 h-5 text-gray-400"/>
                            Afbeelding
                        </label>
                        <input type="file" id="image" name="image" accept="image/*"
                               class="block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-4 file:rounded-md file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200 dark:file:bg-gray-700 dark:file:text-gray-200">
                        <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF tot 2MB</p>
                    </div>
                </div>

                <div class="px-6 py-5 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-end gap-3">
                    <a href="{{ $backUrl }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        Annuleren
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        <x-heroicon-s-key class="w-4 h-4 mr-2"/>
                        Plot te huur zetten
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
