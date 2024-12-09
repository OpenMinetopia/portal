@extends('portal.layouts.app')

@section('title', 'Opheffings Aanvraag')
@section('header', 'Opheffings Aanvraag')

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ route('portal.companies.dissolutions.index') }}"
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
                                    Ingediend op {{ $dissolutionRequest->created_at->format('d-m-Y H:i') }}
                                </p>
                            </div>
                            <span @class([
                                'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                'bg-yellow-50 text-yellow-700 ring-yellow-600/20 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/20' => $dissolutionRequest->isPending(),
                                'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' => $dissolutionRequest->isApproved(),
                                'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20' => $dissolutionRequest->isDenied(),
                            ])>
                                {{ $dissolutionRequest->getStatusText() }}
                            </span>
                        </div>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Aanvrager</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $dissolutionRequest->user->minecraft_username }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Reden</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $dissolutionRequest->reason }}</dd>
                            </div>
                            @if($dissolutionRequest->handled_at)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Verwerkt door</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $dissolutionRequest->handler->minecraft_username }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Verwerkt op</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $dissolutionRequest->handled_at->format('d-m-Y H:i') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Company Info -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Bedrijfsgegevens</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Bedrijfsnaam</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $dissolutionRequest->company->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">KvK Nummer</dt>
                                <dd class="mt-1 text-sm font-mono text-gray-900 dark:text-white">{{ $dissolutionRequest->company->kvk_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $dissolutionRequest->company->type->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Opgericht op</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $dissolutionRequest->company->created_at->format('d-m-Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                @if($dissolutionRequest->isPending())
                    <!-- Handle Request Form -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Aanvraag Verwerken</h3>
                        </div>
                        <form action="{{ route('portal.companies.dissolutions.handle', $dissolutionRequest) }}" method="POST">
                            @csrf
                            <div class="px-4 py-5 sm:p-6 space-y-6">
                                <!-- Status Selection -->
                                <div x-data="{ status: '' }" class="space-y-4">
                                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <!-- Approve Button -->
                                        <label>
                                            <input type="radio" name="status" value="approved" x-model="status" class="peer sr-only" required>
                                            <div class="relative px-4 py-3 border-2 rounded-lg cursor-pointer transition-all duration-150
                                                        peer-checked:border-green-500 peer-checked:ring-1 peer-checked:ring-green-500
                                                        peer-checked:bg-green-50 dark:peer-checked:bg-green-500/10
                                                        hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <x-heroicon-s-check-circle class="w-5 h-5 text-green-500 dark:text-green-400"/>
                                                        <div class="ml-3">
                                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Goedkeuren</span>
                                                        </div>
                                                    </div>
                                                    <div class="shrink-0 text-green-500 dark:text-green-400" x-show="status === 'approved'">
                                                        <x-heroicon-s-check class="w-6 h-6"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>

                                        <!-- Deny Button -->
                                        <label>
                                            <input type="radio" name="status" value="denied" x-model="status" class="peer sr-only" required>
                                            <div class="relative px-4 py-3 border-2 rounded-lg cursor-pointer transition-all duration-150
                                                        peer-checked:border-red-500 peer-checked:ring-1 peer-checked:ring-red-500
                                                        peer-checked:bg-red-50 dark:peer-checked:bg-red-500/10
                                                        hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <x-heroicon-s-x-circle class="w-5 h-5 text-red-500 dark:text-red-400"/>
                                                        <div class="ml-3">
                                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Afwijzen</span>
                                                        </div>
                                                    </div>
                                                    <div class="shrink-0 text-red-500 dark:text-red-400" x-show="status === 'denied'">
                                                        <x-heroicon-s-check class="w-6 h-6"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                    <!-- Selected Status Indicator -->
                                    <div class="text-sm" x-show="status !== ''">
                                        <p class="font-medium" :class="{
                                            'text-green-600 dark:text-green-400': status === 'approved',
                                            'text-red-600 dark:text-red-400': status === 'denied'
                                        }">
                                            <span x-text="status === 'approved' ? 'Het bedrijf wordt opgeheven' : 'De opheffing wordt afgewezen'"></span>
                                        </p>
                                    </div>

                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Notes -->
                                <div>
                                    <label for="admin_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Toelichting <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-2">
                                        <textarea name="admin_notes" id="admin_notes" rows="4" required
                                                  class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-white"
                                                  placeholder="Geef een toelichting op je beslissing...">{{ old('admin_notes') }}</textarea>
                                    </div>
                                    @error('admin_notes')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <div class="pt-4">
                                    <button type="submit"
                                            class="w-full inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                        <x-heroicon-s-check class="h-4 w-4 mr-2"/>
                                        Verwerken
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @else
                    <!-- Decision Info -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Beslissing</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Toelichting</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $dissolutionRequest->admin_notes }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                @endif
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
