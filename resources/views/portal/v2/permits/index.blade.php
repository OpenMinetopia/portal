@extends('portal.layouts.v2.app')

@section('title', 'Vergunningen')
@section('header', 'Vergunningen')

@section('content')
    <div class="space-y-8">
        <!-- Welcome Header -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500/10 via-blue-500/10 to-purple-500/10 backdrop-blur-sm border border-white/20 p-8">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
            <div class="relative">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <span class="text-2xl">📄</span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 dark:text-white">Vergunningen</h1>
                        <p class="text-lg text-gray-600 dark:text-gray-300">Beheer je vergunningen en aanvragen</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Permits -->
        <div class="space-y-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <x-heroicon-s-plus class="h-5 w-5 text-white" />
                </div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white">Beschikbare Vergunningen</h2>
            </div>
            
            @if($permitTypes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($permitTypes as $type)
                        <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg transition-all duration-300">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
                            <div class="relative flex flex-col h-full">
                                <!-- Header -->
                                <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                                <x-heroicon-s-document-text class="h-6 w-6 text-white" />
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-black text-gray-900 dark:text-white">{{ $type->name }}</h3>
                                                <div class="flex items-center gap-2">
                                                    <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                                                    <span class="text-sm text-gray-600 dark:text-gray-400">Beschikbaar</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">€{{ number_format($type->price, 2, ',', '.') }}</div>
                                            <div class="text-xs text-gray-600 dark:text-gray-400">eenmalig</div>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $type->description }}</p>
                                </div>

                                <!-- Requirements -->
                                <div class="flex-1 p-6">
                                    <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-4">Benodigde Informatie</h4>
                                    <div class="space-y-3">
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
                                    <a href="{{ route('portal.permits.request', $type) }}"
                                       class="w-full inline-flex justify-center items-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl hover:shadow-lg transition-all duration-200">
                                        <x-heroicon-s-document-plus class="h-5 w-5"/>
                                        Vergunning Aanvragen
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
                            <x-heroicon-o-document-text class="h-12 w-12 text-white"/>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Geen vergunningen beschikbaar</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Er zijn momenteel geen vergunningen beschikbaar voor aanvraag. 
                            Neem contact op met de beheerders voor meer informatie.
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <!-- User's Permit Requests -->
        <div class="space-y-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <x-heroicon-s-clock class="h-5 w-5 text-white" />
                </div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white">Mijn Aanvragen</h2>
            </div>

            @if($requests->count() > 0)
                <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-600/5"></div>
                    <div class="relative">
                        <!-- Table Header -->
                        <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                <div>Vergunning</div>
                                <div>Aangevraagd op</div>
                                <div>Status</div>
                                <div>Behandeld door</div>
                                <div class="text-right">Acties</div>
                            </div>
                        </div>

                        <!-- Table Content -->
                        <div class="divide-y divide-gray-200/50 dark:divide-gray-700/50">
                            @foreach($requests as $request)
                                <div class="p-6 hover:bg-white/50 dark:hover:bg-gray-700/50 transition-all duration-200">
                                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-center">
                                        <!-- Permit Name -->
                                        <div>
                                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $request->type->name }}</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">€{{ number_format($request->price, 2, ',', '.') }}</div>
                                        </div>

                                        <!-- Date -->
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $request->created_at->format('d M Y') }}</div>
                                            <div class="text-xs text-gray-600 dark:text-gray-400">{{ $request->created_at->format('H:i') }}</div>
                                        </div>

                                        <!-- Status -->
                                        <div>
                                            @if($request->isPending())
                                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-yellow-500/20 text-yellow-700 dark:text-yellow-300 border border-yellow-500/30">
                                                    <div class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></div>
                                                    <span class="text-sm font-semibold">In behandeling</span>
                                                </div>
                                            @elseif($request->isApproved())
                                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">
                                                    <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                                                    <span class="text-sm font-semibold">Goedgekeurd</span>
                                                </div>
                                            @else
                                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-red-500/20 text-red-700 dark:text-red-300 border border-red-500/30">
                                                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                                    <span class="text-sm font-semibold">Afgewezen</span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Handler -->
                                        <div>
                                            @if($request->handler)
                                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $request->handler->name }}</div>
                                                <div class="text-xs text-gray-600 dark:text-gray-400">Behandelaar</div>
                                            @else
                                                <div class="text-sm text-gray-500 dark:text-gray-400">Nog niet toegewezen</div>
                                            @endif
                                        </div>

                                        <!-- Actions -->
                                        <div class="text-right">
                                            <a href="{{ route('portal.permits.show', $request) }}"
                                               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 rounded-xl hover:bg-white dark:hover:bg-gray-800 transition-all duration-200">
                                                <x-heroicon-s-eye class="w-4 h-4"/>
                                                Bekijken
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State for Requests -->
                <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-12 shadow-lg text-center">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-600/5"></div>
                    <div class="relative">
                        <div class="w-24 h-24 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <x-heroicon-o-clock class="h-12 w-12 text-white"/>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Nog geen aanvragen</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            Je hebt nog geen vergunningen aangevraagd. Bekijk de beschikbare vergunningen hierboven.
                        </p>
                        @if($permitTypes->count() > 0)
                            <a href="#" onclick="document.querySelector('h2').scrollIntoView({behavior: 'smooth'})"
                               class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 rounded-xl hover:bg-white dark:hover:bg-gray-800 transition-all duration-200">
                                <x-heroicon-s-arrow-up class="w-4 h-4"/>
                                Bekijk beschikbare vergunningen
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Help Section -->
        @if($permitTypes->count() > 0)
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
                                <p>• Vergunningen worden binnen 5 werkdagen behandeld</p>
                                <p>• De kosten worden direct afgeschreven bij het indienen</p>
                                <p>• Je ontvangt een melding zodra je aanvraag is behandeld</p>
                                <p>• Velden met een <span class="text-red-500">*</span> zijn verplicht om in te vullen</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>


@endsection 