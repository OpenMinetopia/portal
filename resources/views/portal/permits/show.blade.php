@extends('portal.layouts.app')

@section('title', 'Vergunning Aanvraag')
@section('header', 'Vergunning Aanvraag')

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

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Request Details -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $permitRequest->type->name }}</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $permitRequest->type->description }}</p>
                            </div>
                            <span @class([
                                'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                'bg-yellow-50 text-yellow-700 ring-yellow-600/20 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/20' => $permitRequest->isPending(),
                                'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' => $permitRequest->isApproved(),
                                'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20' => $permitRequest->isDenied(),
                            ])>
                                @if($permitRequest->isPending())
                                    In behandeling
                                @elseif($permitRequest->isApproved())
                                    Goedgekeurd
                                @else
                                    Afgewezen
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-6">
                            @foreach($permitRequest->type->form_fields as $field)
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
                                                <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $permitRequest->form_data[$field['label']] }}</p>
                                                @break
                                            @case('checkbox')
                                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $permitRequest->form_data[$field['label']]
                                                    ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20'
                                                    : 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20'
                                                }}">
                                                    {{ $permitRequest->form_data[$field['label']] ? 'Ja' : 'Nee' }}
                                                </span>
                                                @break
                                            @default
                                                <p class="text-sm text-gray-900 dark:text-white">{{ $permitRequest->form_data[$field['label']] }}</p>
                                        @endswitch
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </div>

                @if($permitRequest->admin_notes)
                    <!-- Admin Notes -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Opmerkingen</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $permitRequest->admin_notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Status Informatie</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Aangevraagd op</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $permitRequest->created_at->format('d-m-Y H:i') }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Kosten</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">€{{ number_format($permitRequest->type->price, 2) }}</dd>
                            </div>

                            @if($permitRequest->handler)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Behandeld door</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $permitRequest->handler->name }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Behandeld op</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $permitRequest->handled_at->format('d-m-Y H:i') }}</dd>
                                </div>
                            @endif
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
                            Heb je vragen over deze aanvraag? Neem dan contact op met een medewerker van de gemeente.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 