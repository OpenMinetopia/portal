@extends('portal.layouts.v2.app')

@section('title', 'Plot Details')
@section('header', 'Plot Details')

@section('content')
    <div class="space-y-8">
        <!-- Plot Header -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500/10 via-purple-500/10 to-emerald-500/10 backdrop-blur-sm border border-white/20 p-8">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-600/5"></div>
            <div class="relative">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-xl">{{ substr($plot['name'], 0, 2) }}</span>
                        </div>
                        <div>
                            <h1 class="text-3xl font-black text-gray-900 dark:text-white mb-2">{{ $plot['name'] }}</h1>
                            <div class="flex items-center gap-4">
                                <span @class([
                                    'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold',
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400' => $plot['permission'] === 'OWNER',
                                    'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400' => $plot['permission'] === 'MEMBER',
                                ])>
                                    @if($plot['permission'] === 'OWNER')
                                        <x-heroicon-s-key class="w-4 h-4" />
                                        Eigenaar
                                    @else
                                        <x-heroicon-s-users class="w-4 h-4" />
                                        Lid
                                    @endif
                                </span>
                                <span class="text-gray-600 dark:text-gray-400">
                                    📍 {{ $plot['location']['min']['x'] }}, {{ $plot['location']['min']['z'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @if($isOwner)
                        <div class="flex items-center gap-3">
                            <a href="{{ route('portal.plots.listings.create', $plot['name']) }}"
                               class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl hover:shadow-lg transition-all duration-200">
                                <x-heroicon-m-currency-euro class="h-5 w-5"/>
                                Plot Verkopen
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Plot Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Plot Size -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-6 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-600/10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-heroicon-s-calculator class="h-6 w-6 text-white" />
                        </div>
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Oppervlakte</h3>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">
                        {{ number_format(abs(($plot['location']['max']['x'] - $plot['location']['min']['x']) * ($plot['location']['max']['z'] - $plot['location']['min']['z']))) }}m²
                    </p>
                </div>
            </div>

            <!-- Owners Count -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-6 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-blue-600/10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-heroicon-s-user class="h-6 w-6 text-white" />
                        </div>
                        <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Eigenaren</h3>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ count($plot['owners']) }}</p>
                </div>
            </div>

            <!-- Members Count -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-6 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 to-red-600/10"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-heroicon-s-users class="h-6 w-6 text-white" />
                        </div>
                        <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Leden</h3>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ count($plot['members']) }}</p>
                </div>
            </div>
        </div>

        <!-- Plot Details -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-600/5"></div>
            <div class="relative">
                <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                    <h2 class="text-xl font-black text-gray-900 dark:text-white">Plot Informatie</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Gedetailleerde informatie over dit plot</p>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Minimum Coördinaten</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">
                                X: {{ $plot['location']['min']['x'] ?? 0 }}, Y: {{ $plot['location']['min']['y'] ?? 0 }}, Z: {{ $plot['location']['min']['z'] ?? 0 }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Maximum Coördinaten</dt>
                            <dd class="text-lg font-semibold text-gray-900 dark:text-white">
                                X: {{ $plot['location']['max']['x'] ?? 0 }}, Y: {{ $plot['location']['max']['y'] ?? 0 }}, Z: {{ $plot['location']['max']['z'] ?? 0 }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        @if($plot['permission'] === 'OWNER')
            @php
                $activeListing = \App\Models\PlotListing::where('plot_name', $plot['name'])
                    ->where('status', 'active')
                    ->first();
            @endphp

            @if($activeListing)
                <!-- Active Listing Card -->
                <div class="card-hover relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500/10 via-green-500/10 to-blue-500/10 backdrop-blur-sm border border-emerald-200/50 dark:border-emerald-700/50 shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-green-600/5"></div>
                    <div class="relative">
                        <div class="p-6 border-b border-emerald-200/50 dark:border-emerald-700/50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <x-heroicon-s-currency-euro class="h-6 w-6 text-white"/>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-black text-gray-900 dark:text-white">Te Koop</h2>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Dit plot staat momenteel te koop</p>
                                    </div>
                                </div>
                                <form action="{{ route('portal.plots.listings.destroy', $activeListing) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 rounded-xl transition-all duration-200">
                                        <x-heroicon-s-x-mark class="w-4 h-4"/>
                                        Van de markt halen
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Vraagprijs</dt>
                                    <dd class="text-2xl font-black text-gray-900 dark:text-white">
                                        {{ $activeListing->formatted_price }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Status</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400">
                                            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                            Te koop
                                        </span>
                                    </dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Beschrijving</dt>
                                    <dd class="text-gray-900 dark:text-white">
                                        {{ $activeListing->description }}
                                    </dd>
                                </div>
                                @if($activeListing->image_path)
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Afbeelding</dt>
                                        <dd class="mt-2">
                                            <img src="{{ Storage::url($activeListing->image_path) }}"
                                                 alt="Afbeelding van {{ $plot['name'] }}"
                                                 class="rounded-xl max-h-64 object-cover shadow-lg">
                                        </dd>
                                    </div>
                                @endif
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Instellingen</dt>
                                    <dd class="mt-2">
                                        <span @class([
                                            'inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold',
                                            'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400' => $activeListing->instant_buy,
                                            'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-400' => !$activeListing->instant_buy,
                                        ])>
                                            @if($activeListing->instant_buy)
                                                <x-heroicon-s-bolt class="w-4 h-4" />
                                                Direct kopen mogelijk
                                            @else
                                                <x-heroicon-s-chat-bubble-left-right class="w-4 h-4" />
                                                Contact verkoper
                                            @endif
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- Users Management -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Owners Card -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
                <div class="relative">
                    <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <x-heroicon-s-user class="h-5 w-5 text-white"/>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-gray-900 dark:text-white">Eigenaren</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ count($plot['owners']) }} eigenaren</p>
                                </div>
                            </div>
                            @if($isOwner)
                                <button type="button"
                                        onclick="document.getElementById('add-owner-modal').classList.remove('hidden')"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl hover:shadow-lg transition-all duration-200">
                                    <x-heroicon-m-plus class="h-4 w-4"/>
                                    Toevoegen
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @forelse($plot['owners'] as $owner)
                                <div class="flex items-center justify-between p-4 rounded-xl bg-white/70 dark:bg-gray-700/70 backdrop-blur-sm border border-gray-200/30 dark:border-gray-600/30">
                                    <div class="flex items-center gap-4">
                                        <img class="h-12 w-12 rounded-lg bg-gray-50 dark:bg-gray-800 shadow-lg"
                                             src="https://crafatar.com/avatars/{{ $owner }}?overlay=true"
                                             alt="{{ $owner }}">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ \App\Helpers\MinecraftHelper::getName($owner) }}
                                            </p>
                                            @if($owner === auth()->user()->minecraft_plain_uuid)
                                                <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">Jij</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($isOwner && $owner !== auth()->user()->minecraft_plain_uuid)
                                        <form action="{{ route('portal.plots.owners.remove', $plot['name']) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="user_uuid" value="{{ $owner }}">
                                            <button type="submit"
                                                    class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-all duration-200">
                                                <x-heroicon-m-trash class="h-5 w-5"/>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <x-heroicon-o-user class="h-12 w-12 text-gray-400 dark:text-gray-500 mx-auto"/>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mt-2">Geen eigenaren</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Er zijn geen eigenaren voor dit plot</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Members Card -->
            <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500/5 to-red-600/5"></div>
                <div class="relative">
                    <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                                    <x-heroicon-s-users class="h-5 w-5 text-white"/>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-gray-900 dark:text-white">Leden</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ count($plot['members']) }} leden</p>
                                </div>
                            </div>
                            @if($isOwner)
                                <button type="button"
                                        onclick="document.getElementById('add-member-modal').classList.remove('hidden')"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-orange-500 to-red-600 rounded-xl hover:shadow-lg transition-all duration-200">
                                    <x-heroicon-m-plus class="h-4 w-4"/>
                                    Toevoegen
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @forelse($plot['members'] as $member)
                                <div class="flex items-center justify-between p-4 rounded-xl bg-white/70 dark:bg-gray-700/70 backdrop-blur-sm border border-gray-200/30 dark:border-gray-600/30">
                                    <div class="flex items-center gap-4">
                                        <img class="h-12 w-12 rounded-lg bg-gray-50 dark:bg-gray-800 shadow-lg"
                                             src="https://crafatar.com/avatars/{{ $member }}?overlay=true"
                                             alt="{{ $member }}">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ \App\Helpers\MinecraftHelper::getName($member) }}
                                            </p>
                                        </div>
                                    </div>
                                    @if($isOwner)
                                        <form action="{{ route('portal.plots.members.remove', $plot['name']) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="user_uuid" value="{{ $member }}">
                                            <button type="submit"
                                                    class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-all duration-200">
                                                <x-heroicon-m-trash class="h-5 w-5"/>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <x-heroicon-o-users class="h-12 w-12 text-gray-400 dark:text-gray-500 mx-auto"/>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mt-2">Geen leden</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Er zijn geen leden voor dit plot</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($isOwner)
        <!-- Add Owner Modal -->
        <div id="add-owner-modal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/75 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-2xl bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                        <form action="{{ route('portal.plots.owners.add', $plot['name']) }}" method="POST">
                            @csrf
                            <div>
                                <div class="mt-3 text-center sm:mt-0 sm:text-left">
                                    <h3 class="text-xl font-black text-gray-900 dark:text-white" id="modal-title">Eigenaar toevoegen</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Selecteer een speler om als eigenaar toe te voegen aan dit plot.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <label for="user_uuid" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Speler</label>
                                <select name="user_uuid" id="user_uuid" required
                                        class="block w-full rounded-xl border-0 py-3 pl-4 pr-10 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500 sm:text-sm dark:bg-gray-700/70 backdrop-blur-sm">
                                    <option value="">Selecteer een speler</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->minecraft_plain_uuid }}">{{ $user->minecraft_username }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mt-6 flex gap-3 sm:flex-row-reverse">
                                <button type="submit"
                                        class="inline-flex w-full justify-center rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 px-4 py-3 text-sm font-semibold text-white shadow-lg hover:shadow-xl transition-all duration-200 sm:w-auto">
                                    Toevoegen
                                </button>
                                <button type="button"
                                        onclick="document.getElementById('add-owner-modal').classList.add('hidden')"
                                        class="inline-flex w-full justify-center rounded-xl bg-white/70 dark:bg-gray-700/70 backdrop-blur-sm px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white border border-gray-200/50 dark:border-gray-600/50 hover:bg-white dark:hover:bg-gray-700 transition-all duration-200 sm:w-auto">
                                    Annuleren
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Member Modal -->
        <div id="add-member-modal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/75 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-2xl bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                        <form action="{{ route('portal.plots.members.add', $plot['name']) }}" method="POST">
                            @csrf
                            <div>
                                <div class="mt-3 text-center sm:mt-0 sm:text-left">
                                    <h3 class="text-xl font-black text-gray-900 dark:text-white" id="modal-title">Lid toevoegen</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Selecteer een speler om als lid toe te voegen aan dit plot.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <label for="user_uuid" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Speler</label>
                                <select name="user_uuid" id="user_uuid" required
                                        class="block w-full rounded-xl border-0 py-3 pl-4 pr-10 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-blue-600 dark:focus:ring-blue-500 sm:text-sm dark:bg-gray-700/70 backdrop-blur-sm">
                                    <option value="">Selecteer een speler</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->minecraft_plain_uuid }}">{{ $user->minecraft_username }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mt-6 flex gap-3 sm:flex-row-reverse">
                                <button type="submit"
                                        class="inline-flex w-full justify-center rounded-xl bg-gradient-to-r from-orange-500 to-red-600 px-4 py-3 text-sm font-semibold text-white shadow-lg hover:shadow-xl transition-all duration-200 sm:w-auto">
                                    Toevoegen
                                </button>
                                <button type="button"
                                        onclick="document.getElementById('add-member-modal').classList.add('hidden')"
                                        class="inline-flex w-full justify-center rounded-xl bg-white/70 dark:bg-gray-700/70 backdrop-blur-sm px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white border border-gray-200/50 dark:border-gray-600/50 hover:bg-white dark:hover:bg-gray-700 transition-all duration-200 sm:w-auto">
                                    Annuleren
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection 