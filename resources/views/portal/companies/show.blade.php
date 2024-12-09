@extends('portal.layouts.app')

@section('title', $company->name)
@section('header', $company->name)

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
            <!-- Left Column - Company Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Company Info -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Bedrijfsgegevens</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Opgericht op {{ $company->created_at->format('d-m-Y') }}
                                </p>
                            </div>
                            <span @class([
                                'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' => $company->is_active && !$company->hasPendingDissolution(),
                                'bg-yellow-50 text-yellow-700 ring-yellow-600/20 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/20' => $company->hasPendingDissolution(),
                                'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20' => !$company->is_active,
                            ])>
                                @if($company->hasPendingDissolution())
                                    Opheffing in behandeling
                                @else
                                    {{ $company->is_active ? 'Actief' : 'Inactief' }}
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $company->type->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">KvK Nummer</dt>
                                <dd class="mt-1 text-sm font-mono text-gray-900 dark:text-white">{{ $company->kvk_number }}</dd>
                            </div>
                            @if($company->description)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Beschrijving</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $company->description }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Company Data -->
                @if(is_array($company->data) && count($company->data) > 0)
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Bedrijfsgegevens</h3>
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
                <!-- Actions Card -->
                @if($company->is_active && !$company->hasPendingDissolution())
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Acties</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <form action="{{ route('portal.companies.dissolve', $company) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Weet je zeker dat je dit bedrijf wilt opheffen? Dit kan niet ongedaan worden gemaakt.');">
                                @csrf
                                <button type="submit"
                                        class="w-full inline-flex justify-center items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 dark:bg-red-500 dark:hover:bg-red-400">
                                    <x-heroicon-s-archive-box-x-mark class="h-4 w-4 mr-2"/>
                                    Bedrijf Opheffen
                                </button>
                            </form>
                        </div>
                    </div>
                @elseif($company->hasPendingDissolution())
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Opheffing Status</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <div class="flex items-center gap-x-3">
                                <div class="flex-shrink-0">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-500/10">
                                        <x-heroicon-s-clock class="h-6 w-6 text-yellow-600 dark:text-yellow-400"/>
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">In Behandeling</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Je opheffingsverzoek wordt beoordeeld</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Help Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Hulp nodig?</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Heb je vragen over je bedrijf? Neem dan contact op met een medewerker van de gemeente.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }"
             x-show="show"
             x-transition
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed bottom-0 right-0 m-6 w-96 max-w-full">
            <div class="rounded-lg bg-green-50 p-4 shadow-lg dark:bg-green-500/10">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-s-check-circle class="h-5 w-5 text-green-400 dark:text-green-500"/>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                            {{ session('success') }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button @click="show = false" type="button"
                                    class="inline-flex rounded-md p-1.5 text-green-500 hover:bg-green-100 dark:text-green-400 dark:hover:bg-green-500/20">
                                <span class="sr-only">Sluiten</span>
                                <x-heroicon-s-x-mark class="h-5 w-5"/>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection 