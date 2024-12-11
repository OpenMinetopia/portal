@extends('portal.layouts.app')

@section('title', 'Bedrijfs Aanvraag Details')
@section('header', 'Bedrijfs Aanvraag Details')

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

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Request Info -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Aanvraag Details</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Ingediend op {{ $companyRequest->created_at->format('d-m-Y H:i') }}
                                </p>
                            </div>
                            <span @class([
                                'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                'bg-yellow-50 text-yellow-700 ring-yellow-600/20 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/20' => $companyRequest->isPending(),
                                'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' => $companyRequest->isApproved(),
                                'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20' => $companyRequest->isDenied(),
                            ])>
                                {{ $companyRequest->getStatusText() }}
                            </span>
                        </div>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Bedrijfsnaam</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $companyRequest->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $companyRequest->type->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Kosten</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">€{{ number_format($companyRequest->price, 2, ',', '.') }}</dd>
                            </div>
                            @if($companyRequest->handled_at)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Verwerkt door</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $companyRequest->handler->minecraft_username }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Verwerkt op</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $companyRequest->handled_at->format('d-m-Y H:i') }}</dd>
                                </div>
                                @if($companyRequest->isDenied() && $companyRequest->refunded)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Terugbetaling</dt>
                                        <dd class="mt-1">
                                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20">
                                                Bedrag teruggestort
                                            </span>
                                        </dd>
                                    </div>
                                @endif
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Form Data -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Formulier Gegevens</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                            @foreach($companyRequest->type->form_fields as $field)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ $field['label'] }}
                                        @if($field['required'])
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </dt>
                                    <dd class="mt-1">
                                        @switch($field['type'])
                                            @case('textarea')
                                                <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $companyRequest->form_data[$field['label']] }}</p>
                                                @break
                                            @case('checkbox')
                                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $companyRequest->form_data[$field['label']]
                                                    ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20'
                                                    : 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20'
                                                }}">
                                                    {{ $companyRequest->form_data[$field['label']] ? 'Ja' : 'Nee' }}
                                                </span>
                                                @break
                                            @default
                                                <p class="text-sm text-gray-900 dark:text-white">{{ $companyRequest->form_data[$field['label']] }}</p>
                                        @endswitch
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                @if($companyRequest->isApproved() && $companyRequest->company)
                    <!-- Company Info -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Bedrijfsgegevens</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">KvK Nummer</dt>
                                    <dd class="mt-1 text-sm font-mono text-gray-900 dark:text-white">{{ $companyRequest->company->kvk_number }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                    <dd class="mt-1">
                                        <span @class([
                                            'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                            'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' => $companyRequest->company->is_active,
                                            'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20' => !$companyRequest->company->is_active,
                                        ])>
                                            {{ $companyRequest->company->is_active ? 'Actief' : 'Inactief' }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="pt-4">
                                    <a href="{{ route('portal.companies.show', $companyRequest->company) }}"
                                       class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                        <x-heroicon-s-building-office class="h-4 w-4 mr-2"/>
                                        Naar bedrijfspagina
                                    </a>
                                </div>
                            </dl>
                        </div>
                    </div>
                @endif

                @if($companyRequest->admin_notes)
                    <!-- Admin Notes -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Toelichting</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $companyRequest->admin_notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection 