@extends('portal.layouts.v2.app')

@section('title', 'Vergunning Aanvraag')
@section('header', 'Vergunning Aanvraag')

@section('content')
    <div class="space-y-8">
        <!-- Header -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500/10 via-blue-500/10 to-purple-500/10 backdrop-blur-sm border border-white/20 p-8">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
            <div class="relative">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <span class="text-2xl">📄</span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 dark:text-white">{{ $permitRequest->type->name }}</h1>
                        <p class="text-lg text-gray-600 dark:text-gray-300">Vergunning aanvraag details</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Aanvraag #{{ $permitRequest->id }}</span>
                </div>
            </div>
        </div>

        <!-- Status Card -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-600/5"></div>
            <div class="relative p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <x-heroicon-s-clipboard-document-check class="h-5 w-5 text-white" />
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Status</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Huidige status van je aanvraag</p>
                        </div>
                    </div>
                    <div class="text-right">
                        @if($permitRequest->status === 'pending')
                            <span class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-bold bg-yellow-50 text-yellow-700 ring-1 ring-yellow-600/20 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/20">
                                <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2 animate-pulse"></div>
                                In behandeling
                            </span>
                        @elseif($permitRequest->status === 'approved')
                            <span class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-bold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20">
                                <div class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></div>
                                Goedgekeurd
                            </span>
                        @elseif($permitRequest->status === 'denied')
                            <span class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-bold bg-red-50 text-red-700 ring-1 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20">
                                <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                Afgewezen
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Details -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
            <div class="relative p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <x-heroicon-s-document-text class="h-5 w-5 text-white" />
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Aanvraag Details</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Vergunning Type</label>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $permitRequest->type->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Aanvraag Datum</label>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $permitRequest->created_at->format('d-m-Y H:i') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Kosten</label>
                            <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ $permitRequest->type->formatted_price }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @if($permitRequest->handled_at)
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Behandeld op</label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $permitRequest->handled_at->format('d-m-Y H:i') }}</p>
                            </div>
                        @endif

                        @if($permitRequest->handler)
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Behandeld door</label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $permitRequest->handler->name }}</p>
                            </div>
                        @endif

                        @if($permitRequest->refunded)
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Terugbetaling</label>
                                <span class="inline-flex items-center rounded-lg px-2 py-1 text-xs font-semibold bg-blue-50 text-blue-700 ring-1 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20">
                                    Terugbetaald
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Data -->
        @if($permitRequest->form_data && count($permitRequest->form_data) > 0)
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 to-pink-600/5"></div>
                <div class="relative p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                            <x-heroicon-s-clipboard-document-list class="h-5 w-5 text-white" />
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Ingediende Gegevens</h2>
                    </div>

                    <div class="space-y-4">
                        @foreach($permitRequest->form_data as $key => $value)
                            <div class="flex items-start gap-4 p-4 rounded-xl bg-gray-50/50 dark:bg-gray-700/50">
                                <div class="w-6 h-6 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <x-heroicon-s-chevron-right class="h-3 w-3 text-white" />
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ $key }}</label>
                                    <p class="text-sm text-gray-900 dark:text-white">{{ is_array($value) ? implode(', ', $value) : $value }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Admin Notes -->
        @if($permitRequest->admin_notes)
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500/5 to-red-600/5"></div>
                <div class="relative p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center">
                            <x-heroicon-s-chat-bubble-left-ellipsis class="h-5 w-5 text-white" />
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Toelichting</h2>
                    </div>
                    <div class="p-4 rounded-xl bg-gray-50/50 dark:bg-gray-700/50">
                        <p class="text-sm text-gray-900 dark:text-white leading-relaxed">{{ $permitRequest->admin_notes }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Back Button -->
        <div class="flex justify-start">
            <a href="{{ route('portal.permits.index') }}"
               class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 rounded-xl hover:bg-white dark:hover:bg-gray-800 transition-all duration-200">
                <x-heroicon-s-arrow-left class="w-4 h-4"/>
                Terug naar vergunningen
            </a>
        </div>
    </div>
@endsection 