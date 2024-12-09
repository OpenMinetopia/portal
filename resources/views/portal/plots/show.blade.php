@extends('portal.layouts.app')

@section('title', $plot['name'])
@section('header')
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('portal.plots.index') }}"
               class="group flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                <x-heroicon-s-arrow-left class="w-5 h-5"/>
                Terug naar overzicht
            </a>
        </div>
        <span @class([
            'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
            'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20' => $plot['permission'] === 'OWNER',
            'bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-500/10 dark:text-gray-400 dark:ring-gray-500/20' => $plot['permission'] === 'MEMBER',
        ])>
            {{ $plot['permission'] === 'OWNER' ? 'Eigenaar' : 'Lid' }}
        </span>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Plot Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $plot['name'] }}</h1>
                        <div class="flex items-center gap-2 mt-1 text-gray-500 dark:text-gray-400">
                            <x-heroicon-s-map-pin class="w-4 h-4"/>
                            <span>{{ $plot['location']['min']['x'] }}, {{ $plot['location']['min']['z'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Plot Description -->
                @if(isset($plot['description']))
                    <div class="mt-6 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <x-heroicon-s-document-text class="w-5 h-5 text-gray-400 dark:text-gray-500"/>
                            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Beschrijving</h2>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $plot['description'] }}</p>
                    </div>
                @endif

                <!-- Plot Coordinates -->
                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <x-heroicon-s-arrow-down-left class="w-5 h-5 text-gray-400 dark:text-gray-500"/>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Minimale coördinaten</h3>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">X</span>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $plot['location']['min']['x'] }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Y</span>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $plot['location']['min']['y'] }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Z</span>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $plot['location']['min']['z'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <x-heroicon-s-arrow-up-right class="w-5 h-5 text-gray-400 dark:text-gray-500"/>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Maximale coördinaten</h3>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">X</span>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $plot['location']['max']['x'] }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Y</span>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $plot['location']['max']['y'] }}</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Z</span>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $plot['location']['max']['z'] }}</p>
                            </div>
                        </div>
                    </div>
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
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <x-heroicon-s-currency-euro class="h-5 w-5 text-gray-400 dark:text-gray-500"/>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Te Koop</h3>
                            </div>
                            <form action="{{ route('portal.plots.listings.destroy', $activeListing) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800">
                                    <x-heroicon-s-x-mark class="w-4 h-4 mr-1.5"/>
                                    Van de markt halen
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Vraagprijs</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $activeListing->formatted_price }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20">
                                        Te koop
                                    </span>
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Beschrijving</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $activeListing->description }}
                                </dd>
                            </div>
                            @if($activeListing->image_path)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Afbeelding</dt>
                                    <dd class="mt-2">
                                        <img src="{{ Storage::url($activeListing->image_path) }}"
                                             alt="Afbeelding van {{ $plot['name'] }}"
                                             class="rounded-lg max-h-64 object-cover">
                                    </dd>
                                </div>
                            @endif
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Instellingen</dt>
                                <dd class="mt-2">
                                    <div class="flex items-center gap-2">
                                        <span @class([
                                            'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                            'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20' => $activeListing->instant_buy,
                                            'bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-500/10 dark:text-gray-400 dark:ring-gray-500/20' => !$activeListing->instant_buy,
                                        ])>
                                            {{ $activeListing->instant_buy ? 'Direct kopen mogelijk' : 'Contact verkoper' }}
                                        </span>
                                    </div>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            @else
                <div class="flex justify-end">
                    <a href="{{ route('portal.plots.listings.create', $plot['name']) }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                        <x-heroicon-s-currency-euro class="w-4 h-4 mr-2"/>
                        Te koop zetten
                    </a>
                </div>
            @endif
        @endif

        <!-- Users Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Owners Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <x-heroicon-s-user class="h-5 w-5 text-gray-400 dark:text-gray-500"/>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Eigenaren</h3>
                    </div>
                </div>
                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($plot['owners'] as $owner)
                        <li class="flex items-center gap-x-6 px-4 py-5 sm:px-6 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <div class="flex min-w-0 gap-x-4">
                                <img class="h-12 w-12 flex-none rounded-lg bg-gray-50 dark:bg-gray-800"
                                     src="{{ \App\Helpers\MinecraftHelper::getAvatar($owner) }}"
                                     alt="{{ $owner }}">
                                <div class="min-w-0 flex-auto">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ \App\Helpers\MinecraftHelper::class::getName($owner) }}</p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Members Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <x-heroicon-s-users class="h-5 w-5 text-gray-400 dark:text-gray-500"/>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Leden</h3>
                    </div>
                </div>
                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($plot['members'] as $member)
                        <li class="flex items-center gap-x-6 px-4 py-5 sm:px-6 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <div class="flex min-w-0 gap-x-4">
                                <img class="h-12 w-12 flex-none rounded-lg bg-gray-50 dark:bg-gray-800"
                                     src="{{ \App\Helpers\MinecraftHelper::getAvatar($member) }}"
                                     alt="{{ $member }}">
                                <div class="min-w-0 flex-auto">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ \App\Helpers\MinecraftHelper::getName($member) }}</p>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-5 sm:px-6">
                            <div class="text-center">
                                <x-heroicon-o-users class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"/>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen leden</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Dit plot heeft nog geen leden.</p>
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
