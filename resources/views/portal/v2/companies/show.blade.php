@extends('portal.layouts.v2.app')

@section('title', $company->name)
@section('header', $company->name)

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

        <!-- Company Header -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500/10 via-blue-500/10 to-purple-500/10 backdrop-blur-sm border border-white/20 p-8">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
            <div class="relative">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <x-heroicon-s-building-office class="w-8 h-8 text-white"/>
                        </div>
                        <div>
                            <h1 class="text-3xl font-black text-gray-900 dark:text-white">{{ $company->name }}</h1>
                            <p class="text-lg text-gray-600 dark:text-gray-300">{{ $company->type->name }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Opgericht op {{ $company->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <div @class([
                            'px-4 py-2 rounded-xl text-sm font-bold backdrop-blur-sm border shadow-sm',
                            'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-500/30' => $company->is_active && !$company->hasPendingDissolution(),
                            'bg-yellow-500/20 text-yellow-700 dark:text-yellow-300 border-yellow-500/30' => $company->hasPendingDissolution(),
                            'bg-red-500/20 text-red-700 dark:text-red-300 border-red-500/30' => !$company->is_active,
                        ])>
                            @if($company->hasPendingDissolution())
                                <div class="flex items-center gap-2">
                                    <x-heroicon-s-clock class="w-4 h-4"/>
                                    Opheffing in behandeling
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    @if($company->is_active)
                                        <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                        Actief
                                    @else
                                        <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                        Inactief
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-600 dark:text-gray-400">KvK Nummer</div>
                            <div class="text-lg font-black font-mono text-gray-900 dark:text-white">{{ $company->kvk_number }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Company Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Company Information -->
                <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
                    <div class="relative">
                        <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center">
                                    <x-heroicon-s-information-circle class="h-5 w-5 text-white" />
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Bedrijfsinformatie</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Alle details over jouw bedrijf</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">Bedrijfstype</div>
                                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $company->type->name }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">Registratiedatum</div>
                                        <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $company->created_at->format('d M Y') }}</div>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">KvK Nummer</div>
                                        <div class="text-lg font-bold font-mono text-gray-900 dark:text-white">{{ $company->kvk_number }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">Status</div>
                                        <div class="flex items-center gap-2">
                                            @if($company->is_active && !$company->hasPendingDissolution())
                                                <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></div>
                                                <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">Actief</span>
                                            @elseif($company->hasPendingDissolution())
                                                <div class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></div>
                                                <span class="text-lg font-bold text-yellow-600 dark:text-yellow-400">Opheffing in behandeling</span>
                                            @else
                                                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                                <span class="text-lg font-bold text-red-600 dark:text-red-400">Inactief</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($company->description)
                                <div class="mt-6 pt-6 border-t border-gray-200/50 dark:border-gray-700/50">
                                    <div class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Beschrijving</div>
                                    <div class="text-gray-900 dark:text-white leading-relaxed whitespace-pre-wrap">{{ $company->description }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Company Data -->
                @if(is_array($company->data) && count($company->data) > 0)
                    <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
                        <div class="relative">
                            <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center">
                                        <x-heroicon-s-document-text class="h-5 w-5 text-white" />
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Extra Gegevens</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Aanvullende bedrijfsinformatie</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach($company->data as $label => $value)
                                        <div>
                                            <div class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">{{ $label }}</div>
                                            <div class="text-lg font-bold text-gray-900 dark:text-white">
                                                @if(is_bool($value))
                                                    <div class="flex items-center gap-2">
                                                        @if($value)
                                                            <div class="w-6 h-6 bg-gradient-to-r from-emerald-500 to-green-600 rounded-lg flex items-center justify-center">
                                                                <x-heroicon-s-check class="h-4 w-4 text-white"/>
                                                            </div>
                                                            <span class="text-emerald-600 dark:text-emerald-400">Ja</span>
                                                        @else
                                                            <div class="w-6 h-6 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center justify-center">
                                                                <x-heroicon-s-x-mark class="h-4 w-4 text-white"/>
                                                            </div>
                                                            <span class="text-red-600 dark:text-red-400">Nee</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - Actions & Info -->
            <div class="space-y-8">
                <!-- Actions Card -->
                @if($company->is_active && !$company->hasPendingDissolution())
                    <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                        <div class="absolute inset-0 bg-gradient-to-br from-red-500/5 to-red-600/5"></div>
                        <div class="relative">
                            <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-red-500 to-red-600 rounded-xl flex items-center justify-center">
                                        <x-heroicon-s-wrench-screwdriver class="h-5 w-5 text-white" />
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Bedrijfsacties</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Beheer je bedrijf</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                <form action="{{ route('portal.companies.dissolve', $company) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('⚠️ Weet je zeker dat je dit bedrijf wilt opheffen?\n\nDeze actie kan niet ongedaan worden gemaakt en alle gegevens gaan verloren.');">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex justify-center items-center gap-2 px-6 py-3 text-sm font-bold text-white bg-gradient-to-r from-red-500 to-red-600 rounded-xl hover:shadow-lg transition-all duration-200">
                                        <x-heroicon-s-archive-box-x-mark class="h-5 w-5"/>
                                        Bedrijf Opheffen
                                    </button>
                                </form>
                                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400 text-center">
                                    Let op: Deze actie kan niet ongedaan worden gemaakt
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($company->hasPendingDissolution())
                    <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                        <div class="absolute inset-0 bg-gradient-to-br from-yellow-500/5 to-orange-600/5"></div>
                        <div class="relative">
                            <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center">
                                        <x-heroicon-s-clock class="h-5 w-5 text-white" />
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Opheffing Status</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">In behandeling</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 text-center">
                                <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <x-heroicon-s-clock class="h-8 w-8 text-white"/>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Wachten op goedkeuring</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Je opheffingsverzoek wordt momenteel beoordeeld door de gemeente. 
                                    Je ontvangt bericht zodra er een besluit is genomen.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Help Card -->
                <div class="card-hover relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500/10 via-purple-500/10 to-pink-500/10 backdrop-blur-sm border border-blue-200/50 dark:border-blue-700/50 shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-600/5"></div>
                    <div class="relative">
                        <div class="p-6 border-b border-blue-200/50 dark:border-blue-700/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                    <x-heroicon-s-question-mark-circle class="h-5 w-5 text-white" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Hulp Nodig?</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">We helpen je graag</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <x-heroicon-s-chat-bubble-left class="h-4 w-4 text-white" />
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Contact opnemen</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                            Heb je vragen over je bedrijf? Neem contact op met een medewerker van de gemeente.
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <x-heroicon-s-document-text class="h-4 w-4 text-white" />
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Documentatie</h4>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                            Bekijk de handleiding voor meer informatie over bedrijfsbeheer.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
                    <div class="relative">
                        <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center">
                                    <x-heroicon-s-chart-bar class="h-5 w-5 text-white" />
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Bedrijfsstatistieken</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Overzicht van prestaties</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Actief sinds</span>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ $company->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Dagen actief</span>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ $company->created_at->diffInDays(now()) }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Type</span>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $company->type->name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection 