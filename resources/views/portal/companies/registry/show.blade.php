@extends('portal.layouts.app')

@section('title', $company->name)
@section('header', $company->name)

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ route('portal.companies.registry') }}"
               class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <x-heroicon-s-arrow-left class="w-5 h-5 mr-1"/>
                Terug naar bedrijvenregister
            </a>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Bedrijfsinformatie</h3>
                            <span @class([
                                'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' => $company->is_active,
                                'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20' => !$company->is_active,
                            ])>
                                {{ $company->is_active ? 'Actief' : 'Inactief' }}
                            </span>
                        </div>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Bedrijfsnaam</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $company->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">KvK Nummer</dt>
                                <dd class="mt-1 text-sm font-mono text-gray-900 dark:text-white">{{ $company->kvk_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $company->type->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Eigenaar</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $company->owner->minecraft_username }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Opgericht op</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $company->created_at->format('d-m-Y') }}</dd>
                            </div>
                            @if($company->description)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Beschrijving</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $company->description }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Company Data -->
                @if(is_array($company->data) && count($company->data) > 0)
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Extra Gegevens</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                                @foreach($company->data as $label => $value)
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
                @endif
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Timeline -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Tijdlijn</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flow-root">
                            <ul role="list" class="-mb-8">
                                @php
                                    $events = collect();

                                    // Add company creation
                                    $events->push([
                                        'type' => 'created',
                                        'date' => $company->created_at,
                                        'title' => 'Bedrijf opgericht'
                                    ]);

                                    // Add dissolution request if exists
                                    if ($company->dissolution_requested_at) {
                                        $events->push([
                                            'type' => 'dissolution_requested',
                                            'date' => $company->dissolution_requested_at,
                                            'title' => 'Opheffing aangevraagd'
                                        ]);
                                    }

                                    // Add dissolution status if exists
                                    if ($company->dissolutionRequest) {
                                        if ($company->dissolutionRequest->status === 'approved') {
                                            $events->push([
                                                'type' => 'dissolution_approved',
                                                'date' => $company->dissolutionRequest->updated_at,
                                                'title' => 'Opheffing goedgekeurd'
                                            ]);
                                            $events->push([
                                                'type' => 'dissolved',
                                                'date' => $company->dissolutionRequest->updated_at->addSecond(),
                                                'title' => 'Bedrijf opgeheven'
                                            ]);
                                        } elseif ($company->dissolutionRequest->status === 'rejected') {
                                            $events->push([
                                                'type' => 'dissolution_rejected',
                                                'date' => $company->dissolutionRequest->updated_at,
                                                'title' => 'Opheffing afgekeurd'
                                            ]);
                                            $events->push([
                                                'type' => 'continued',
                                                'date' => $company->dissolutionRequest->updated_at->addSecond(),
                                                'title' => 'Bedrijf voortgezet'
                                            ]);
                                        }
                                    }

                                    // Sort events by date (newest first)
                                    $events = $events->sortByDesc('date');
                                @endphp

                                @foreach($events as $event)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white dark:ring-gray-800"
                                                          @class([
                                                              'bg-green-50 dark:bg-green-500/10' => in_array($event['type'], ['created', 'continued']),
                                                              'bg-red-50 dark:bg-red-500/10' => in_array($event['type'], ['dissolved', 'dissolution_rejected']),
                                                              'bg-yellow-50 dark:bg-yellow-500/10' => $event['type'] === 'dissolution_requested',
                                                              'bg-blue-50 dark:bg-blue-500/10' => $event['type'] === 'dissolution_approved',
                                                          ])>
                                                        @switch($event['type'])
                                                            @case('created')
                                                                <x-heroicon-s-building-office class="h-5 w-5 text-green-600 dark:text-green-400"/>
                                                                @break
                                                            @case('dissolution_requested')
                                                                <x-heroicon-s-archive-box-x-mark class="h-5 w-5 text-yellow-600 dark:text-yellow-400"/>
                                                                @break
                                                            @case('dissolution_approved')
                                                                <x-heroicon-s-check-circle class="h-5 w-5 text-blue-600 dark:text-blue-400"/>
                                                                @break
                                                            @case('dissolved')
                                                                <x-heroicon-s-x-circle class="h-5 w-5 text-red-600 dark:text-red-400"/>
                                                                @break
                                                            @case('dissolution_rejected')
                                                                <x-heroicon-s-x-circle class="h-5 w-5 text-red-600 dark:text-red-400"/>
                                                                @break
                                                            @case('continued')
                                                                <x-heroicon-s-arrow-path class="h-5 w-5 text-green-600 dark:text-green-400"/>
                                                                @break
                                                        @endswitch
                                                    </span>
                                                </div>
                                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                    <div>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $event['title'] }}</p>
                                                    </div>
                                                    <div class="whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $event['date']->format('d-m-Y') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
