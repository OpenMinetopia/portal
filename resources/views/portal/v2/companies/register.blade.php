@extends('portal.layouts.v2.app')

@section('title', 'Nieuw Bedrijf')
@section('header', 'Nieuw Bedrijf')

@section('content')
    <div class="space-y-8">
        <!-- Back Navigation -->
        <div>
            <a href="{{ route('portal.companies.index') }}"
               class="group flex items-center gap-2 text-sm text-slate-400 hover:text-white transition-colors duration-200">
                <x-heroicon-s-arrow-left class="w-5 h-5"/>
                Terug naar overzicht
            </a>
        </div>

        <!-- Welcome Hero -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500/10 via-blue-500/10 to-purple-500/10 backdrop-blur-sm border border-white/20 p-8">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
            <div class="relative">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <span class="text-2xl">🏢</span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 dark:text-white">Start Je Bedrijf</h1>
                        <p class="text-lg text-gray-600 dark:text-gray-300">Kies het type bedrijf dat bij jou past</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Company Types Grid -->
        @if($companyTypes->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($companyTypes as $type)
                    <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg transition-all duration-300">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
                        <div class="relative flex flex-col h-full">
                            <!-- Header -->
                            <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <x-heroicon-s-building-office class="h-6 w-6 text-white" />
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-black text-gray-900 dark:text-white">{{ $type->name }}</h3>
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Beschikbaar</span>
                                        </div>
                                    </div>
                                </div>
                                @if($type->description)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $type->description }}</p>
                                @endif
                            </div>

                            <!-- Price Section -->
                            <div class="px-6 py-4 bg-gradient-to-r from-emerald-500/10 to-blue-600/10">
                                <div class="flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-3xl font-black text-gray-900 dark:text-white">€{{ number_format($type->price, 2, ',', '.') }}</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">eenmalige registratie</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Requirements -->
                            <div class="flex-1 p-6">
                                <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-4">Benodigde Informatie</h4>
                                <div class="space-y-3">
                                    <!-- Company Name (Always Required) -->
                                    <div class="flex items-center gap-3">
                                        <div class="w-6 h-6 bg-gradient-to-r from-emerald-500 to-green-600 rounded-lg flex items-center justify-center">
                                            <x-heroicon-s-check class="h-4 w-4 text-white" />
                                        </div>
                                        <span class="text-sm text-gray-900 dark:text-white font-medium">
                                            Bedrijfsnaam
                                            <span class="text-red-500 ml-1">*</span>
                                        </span>
                                    </div>

                                    <!-- Dynamic Fields -->
                                    @foreach($type->form_fields as $field)
                                        <div class="flex items-center gap-3">
                                            <div class="w-6 h-6 bg-gradient-to-r {{ $field['required'] ? 'from-emerald-500 to-green-600' : 'from-gray-400 to-gray-500' }} rounded-lg flex items-center justify-center">
                                                <x-heroicon-s-check class="h-4 w-4 text-white" />
                                            </div>
                                            <span class="text-sm {{ $field['required'] ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-600 dark:text-gray-400' }}">
                                                {{ $field['label'] }}
                                                @if($field['required'])
                                                    <span class="text-red-500 ml-1">*</span>
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="p-6 border-t border-gray-200/50 dark:border-gray-700/50">
                                <a href="{{ route('portal.companies.request', $type) }}"
                                   class="w-full inline-flex justify-center items-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl hover:shadow-lg transition-all duration-200">
                                    <x-heroicon-s-rocket-launch class="h-5 w-5"/>
                                    Bedrijf Aanvragen
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-12 shadow-lg text-center">
                <div class="absolute inset-0 bg-gradient-to-br from-gray-500/5 to-gray-600/5"></div>
                <div class="relative">
                    <div class="w-24 h-24 bg-gradient-to-r from-gray-400 to-gray-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <x-heroicon-o-building-office class="h-12 w-12 text-white"/>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Geen bedrijfstypes beschikbaar</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Er zijn momenteel geen bedrijfstypes beschikbaar voor aanvragen. 
                        Neem contact op met de beheerders voor meer informatie.
                    </p>
                    <a href="{{ route('portal.companies.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 rounded-xl hover:bg-white dark:hover:bg-gray-800 transition-all duration-200">
                        <x-heroicon-s-arrow-left class="w-4 h-4"/>
                        Terug naar overzicht
                    </a>
                </div>
            </div>
        @endif

        <!-- Info Card -->
        @if($companyTypes->count() > 0)
            <div class="card-hover relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500/10 via-purple-500/10 to-pink-500/10 backdrop-blur-sm border border-blue-200/50 dark:border-blue-700/50 p-6 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-600/5"></div>
                <div class="relative">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                            <x-heroicon-s-information-circle class="h-6 w-6 text-white" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Belangrijk om te weten</h3>
                            <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                <p>• Je kunt meerdere bedrijven registreren, elk met een eigen specialisatie</p>
                                <p>• De registratiekosten worden eenmalig afgeschreven van je bankrekening</p>
                                <p>• Na goedkeuring ontvang je een KvK-nummer en wordt je bedrijf actief</p>
                                <p>• Velden met een <span class="text-red-500">*</span> zijn verplicht in te vullen</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection 