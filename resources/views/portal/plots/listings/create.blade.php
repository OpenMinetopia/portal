@extends('portal.layouts.app')

@section('title', 'Plot te koop zetten')
@section('header')
    <div class="flex items-center gap-4">
        <a href="{{ route('portal.plots.show', $plot['name']) }}"
           class="group flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
            <x-heroicon-s-arrow-left class="w-5 h-5"/>
            Terug naar plot
        </a>
    </div>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Info Alert -->
        <div class="bg-blue-50 dark:bg-blue-500/10 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-heroicon-s-information-circle class="h-5 w-5 text-blue-400 dark:text-blue-300"/>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Automatisch systeem</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-200">
                        <p>Dit is een automatisch systeem. Wanneer iemand je plot koopt, zal het automatisch worden
                            overgedragen aan de nieuwe eigenaar. Zorg ervoor dat je plot leeg is en dat je zeker weet
                            dat je het wilt verkopen.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form Card -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Plot te koop zetten</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Vul de details in voor je plot verkoop</p>
            </div>
            <form action="{{ route('portal.plots.listings.store', $plot['name']) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="divide-y divide-gray-200 dark:divide-gray-700">
                @csrf

                <!-- Plot Details Section -->
                <div class="px-6 py-5">
                    <div class="grid grid-cols-1 gap-8">
                        <!-- Plot Info -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Plot naam</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $plot['name'] }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Locatie</dt>
                                    <dd class="mt-1 text-sm font-mono text-gray-900 dark:text-white">
                                        {{ $plot['location']['min']['x'] }}, {{ $plot['location']['min']['z'] }}
                                    </dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Afmetingen</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ abs($plot['location']['max']['x'] - $plot['location']['min']['x']) + 1 }}
                                        x{{ abs($plot['location']['max']['z'] - $plot['location']['min']['z']) + 1 }}
                                        blokken
                                    </dd>
                                </div>
                            </div>
                        </div>

                        <!-- Price Input -->
                        <div class="space-y-2">
                            <label for="price"
                                   class="flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                <x-heroicon-s-currency-euro class="w-5 h-5 text-gray-400"/>
                                Vraagprijs <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 dark:text-gray-400 sm:text-sm">€</span>
                                </div>
                                <input type="number"
                                       name="price"
                                       id="price"
                                       step="0.01"
                                       min="0"
                                       class="block w-full pl-7 pr-12 py-3 sm:text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="0.00"
                                       required>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 dark:text-gray-400 sm:text-sm">EUR</span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Voer een redelijke prijs in voor je plot
                            </p>
                        </div>

                        <!-- Bank Account Selection -->
                        <div class="space-y-2">
                            <label for="payout_bank_account_uuid" 
                                   class="flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                <x-heroicon-s-credit-card class="w-5 h-5 text-gray-400"/>
                                Uitbetaling bankrekening <span class="text-red-500">*</span>
                            </label>
                            <div class="relative rounded-md shadow-sm">
                                <select name="payout_bank_account_uuid" 
                                        id="payout_bank_account_uuid"
                                        required
                                        class="block w-full py-3 pl-3 pr-10 border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Selecteer een bankrekening</option>
                                    @foreach(auth()->user()->bank_accounts as $account)
                                        <option value="{{ $account['uuid'] }}" 
                                                @if($account['type'] === 'PRIVATE') selected @endif>
                                            {{ $account['name'] }} (€ {{ number_format($account['balance'], 2, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <x-heroicon-s-chevron-down class="w-5 h-5 text-gray-400"/>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Selecteer de bankrekening waarop je de betaling wilt ontvangen
                            </p>
                        </div>

                        <!-- Description Input -->
                        <div class="space-y-2">
                            <label for="description"
                                   class="flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                <x-heroicon-s-document-text class="w-5 h-5 text-gray-400"/>
                                Beschrijving <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                            <textarea id="description"
                                      name="description"
                                      rows="6"
                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white resize-none p-4"
                                      style="padding: 1rem !important;"
                                      required
                                      placeholder="Beschrijf je plot hier..."></textarea>
                                <div class="absolute bottom-3 right-3 text-xs text-gray-400">
                                    <span id="charCount">0</span>/1000
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-3">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Tips voor een goede
                                    beschrijving:</h4>
                                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2 list-disc list-inside">
                                    <li>Beschrijf de locatie en omgeving van je plot</li>
                                    <li>Noem bijzondere kenmerken of mogelijkheden</li>
                                    <li>Vermeld eventuele bouwbeperkingen of -mogelijkheden</li>
                                    <li>Wees eerlijk en duidelijk over de staat van het plot</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="space-y-2">
                            <label for="image"
                                   class="flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                <x-heroicon-s-photo class="w-5 h-5 text-gray-400"/>
                                Afbeelding
                            </label>
                            <div class="mt-2">
                                <div
                                    class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                                    <div class="space-y-2 text-center">
                                        <div class="mx-auto h-12 w-12 text-gray-400">
                                            <x-heroicon-o-photo class="mx-auto h-12 w-12"/>
                                        </div>
                                        <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                            <label for="image"
                                                   class="relative cursor-pointer rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 focus-within:outline-none">
                                                <span>Upload een foto</span>
                                                <input type="file"
                                                       id="image"
                                                       name="image"
                                                       accept="image/*"
                                                       class="sr-only">
                                            </label>
                                            <p class="pl-1">of sleep deze hier naartoe</p>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            PNG, JPG, GIF tot 2MB
                                        </p>
                                    </div>
                                </div>
                                <div id="image-preview" class="mt-4 hidden">
                                    <img src="" alt="Preview" class="max-h-48 rounded-lg mx-auto">
                                </div>
                            </div>
                        </div>

                        <!-- Instant Buy Toggle -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <div class="relative flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox"
                                           id="instant_buy"
                                           name="instant_buy"
                                           value="1"
                                           checked
                                           class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded dark:bg-gray-600 dark:border-gray-500">
                                </div>
                                <div class="ml-3">
                                    <label for="instant_buy"
                                           class="text-sm font-medium text-gray-900 dark:text-gray-100">Direct kopen
                                        toestaan</label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Wanneer ingeschakeld kunnen kopers je plot direct kopen en wordt het automatisch
                                        overgedragen.
                                        Schakel dit uit als je eerst contact wilt hebben met potentiële kopers.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-5 bg-gray-50 dark:bg-gray-800/50">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="text-red-500">*</span> Verplichte velden
                        </p>
                        <div class="flex gap-3">
                            <a href="{{ route('portal.plots.show', $plot['name']) }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                Annuleren
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                <x-heroicon-s-currency-euro class="w-4 h-4 mr-2"/>
                                Plot te koop zetten
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Character counter for description
            const description = document.getElementById('description');
            const charCount = document.getElementById('charCount');

            description.addEventListener('input', function () {
                charCount.textContent = this.value.length;
            });

            // Image preview
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('image-preview');
            const previewImage = imagePreview.querySelector('img');

            imageInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewImage.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });
        </script>
    @endpush
@endsection
