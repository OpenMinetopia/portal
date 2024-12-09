@extends('portal.layouts.app')

@section('title', 'Bedrijfs Aanvraag')
@section('header', 'Bedrijfs Aanvraag')

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
            <!-- Left Column - Request Details -->
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
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">€{{ number_format($companyRequest->type->price, 2) }}</dd>
                            </div>
                            @if($companyRequest->handled_at)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Verwerkt op</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $companyRequest->handled_at->format('d-m-Y H:i') }}</dd>
                                </div>
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
                            @foreach($companyRequest->form_data as $label => $value)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $label }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        @if(is_bool($value))
                                            <span @class([
                                                'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                                'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' => $value,
                                                'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20' => !$value,
                                            ])>
                                                {{ $value ? 'Ja' : 'Nee' }}
                                            </span>
                                        @else
                                            {{ $value }}
                                        @endif
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </div>

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

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Status</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <div class="space-y-4">
                            @if($companyRequest->isPending())
                                <div class="flex items-center gap-x-3">
                                    <div class="flex-shrink-0">
                                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-500/10">
                                            <x-heroicon-s-clock class="h-6 w-6 text-yellow-600 dark:text-yellow-400"/>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">In Behandeling</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Je aanvraag wordt beoordeeld</p>
                                    </div>
                                </div>
                            @elseif($companyRequest->isApproved())
                                <div class="flex items-center gap-x-3">
                                    <div class="flex-shrink-0">
                                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 dark:bg-green-500/10">
                                            <x-heroicon-s-check-circle class="h-6 w-6 text-green-600 dark:text-green-400"/>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Goedgekeurd</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Je bedrijf is aangemaakt</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-x-3">
                                    <div class="flex-shrink-0">
                                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 dark:bg-red-500/10">
                                            <x-heroicon-s-x-circle class="h-6 w-6 text-red-600 dark:text-red-400"/>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Afgewezen</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Je aanvraag is niet goedgekeurd</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Hulp nodig?</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Heb je vragen over je aanvraag? Neem dan contact op met een medewerker van de gemeente.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 