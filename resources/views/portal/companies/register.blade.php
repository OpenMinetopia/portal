@extends('portal.layouts.app')

@section('title', 'Nieuw Bedrijf')
@section('header', 'Nieuw Bedrijf')

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ route('portal.companies.index') }}"
               class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <x-heroicon-s-arrow-left class="w-5 h-5 mr-1"/>
                Terug naar overzicht
            </a>
        </div>

        <!-- Company Types Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($companyTypes as $type)
                <div class="flex flex-col bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden h-full">
                    <!-- Card Content -->
                    <div class="flex-1 p-6">
                        <!-- Type Info -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $type->name }}</h3>
                            @if($type->description)
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $type->description }}</p>
                            @endif
                        </div>

                        <!-- Price -->
                        <div class="mb-6">
                            <div class="flex items-baseline">
                                <span class="text-2xl font-semibold text-gray-900 dark:text-white">€{{ number_format($type->price, 2) }}</span>
                                <span class="ml-1 text-sm text-gray-500 dark:text-gray-400">eenmalig</span>
                            </div>
                        </div>

                        <!-- Required Fields -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Benodigde informatie:</h4>
                            <ul class="mt-2 space-y-1">
                                <li class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    <x-heroicon-s-check-circle class="h-4 w-4 mr-2 text-green-500 dark:text-green-400"/>
                                    Bedrijfsnaam
                                    <span class="text-red-500 ml-1">*</span>
                                </li>
                                @foreach($type->form_fields as $field)
                                    <li class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        <x-heroicon-s-check-circle @class([
                                            'h-4 w-4 mr-2',
                                            'text-green-500 dark:text-green-400' => $field['required'],
                                            'text-gray-400 dark:text-gray-500' => !$field['required'],
                                        ])/>
                                        {{ $field['label'] }}
                                        @if($field['required'])
                                            <span class="text-red-500 ml-1">*</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="p-6 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 mt-auto">
                        <a href="{{ route('portal.companies.request', $type) }}"
                           class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400 transition-colors duration-200">
                            <x-heroicon-s-building-office class="h-4 w-4 mr-2"/>
                            Aanvragen
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        @if($companyTypes->isEmpty())
            <div class="text-center">
                <x-heroicon-o-building-office class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"/>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen bedrijfstypes beschikbaar</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Er zijn momenteel geen bedrijfstypes beschikbaar voor aanvragen.
                </p>
            </div>
        @endif
    </div>
@endsection 