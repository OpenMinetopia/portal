@extends('portal.layouts.app')

@section('title', 'Bedrijfs Aanvraag')
@section('header', 'Bedrijfs Aanvraag')

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ route('portal.companies.manage.index') }}"
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
                                'bg-yellow-50 text-yellow-700 ring-yellow-600/20 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/20' => $companyRequest->status === 'pending',
                                'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' => $companyRequest->status === 'approved',
                                'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20' => $companyRequest->status === 'denied',
                            ])>
                                {{ $companyRequest->getStatusText() }}
                            </span>
                        </div>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Aanvrager</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $companyRequest->user->minecraft_username }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $companyRequest->type->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Bedrijfsnaam</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $companyRequest->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Kosten</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">€{{ number_format($companyRequest->type->price, 2) }}</dd>
                            </div>
                            @if($companyRequest->handled_at)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Verwerkt door</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $companyRequest->handler->minecraft_username }}</dd>
                                </div>
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
                            @foreach($companyRequest->type->form_fields as $field)
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
                                                <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $companyRequest->form_data[$field['label']] }}</p>
                                                @break
                                            @case('checkbox')
                                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $companyRequest->form_data[$field['label']]
                                                    ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20'
                                                    : 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20'
                                                }}">
                                                    {{ $companyRequest->form_data[$field['label']] ? 'Ja' : 'Nee' }}
                                                </span>
                                                @break
                                            @default
                                                <p class="text-sm text-gray-900 dark:text-white">{{ $companyRequest->form_data[$field['label']] }}</p>
                                        @endswitch
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                @if($companyRequest->isPending())
                    <!-- Preview Card -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">KvK Nummer Preview</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <div class="text-center">
                                <span class="text-lg font-mono font-semibold text-gray-900 dark:text-white">
                                    {{ $companyRequest->getGeneratedCoCNumber() }}
                                </span>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    Dit KvK nummer wordt toegewezen bij goedkeuring
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Handle Request Form -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Aanvraag Verwerken</h3>
                        </div>
                        <form action="{{ route('portal.companies.manage.handle', $companyRequest) }}" method="POST">
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

                                    <!-- Refund Option (shows when denying) -->
                                    <div x-show="status === 'denied'" x-transition class="mt-4">
                                        <div class="bg-white dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                            <div class="flex items-start">
                                                <div class="flex h-5 items-center">
                                                    <input type="checkbox" 
                                                           name="should_refund" 
                                                           id="should_refund" 
                                                           value="1"
                                                           checked
                                                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800">
                                                </div>
                                                <div class="ml-3">
                                                    <label for="should_refund" class="text-sm font-medium text-gray-900 dark:text-white">
                                                        Bedrag terugstorten
                                                    </label>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        Stort € {{ number_format($companyRequest->price, 2, ',', '.') }} terug naar de bankrekening van de aanvrager
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Selected Status Indicator -->
                                    <div class="text-sm" x-show="status !== ''">
                                        <p class="font-medium" :class="{
                                            'text-green-600 dark:text-green-400': status === 'approved',
                                            'text-red-600 dark:text-red-400': status === 'denied'
                                        }">
                                            <span x-text="status === 'approved' ? 'Deze aanvraag wordt goedgekeurd' : 'Deze aanvraag wordt afgewezen'"></span>
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
                                @if($companyRequest->isApproved())
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">KvK Nummer</dt>
                                        <dd class="mt-1">
                                            <span class="text-lg font-mono font-semibold text-gray-900 dark:text-white">
                                                {{ $companyRequest->getGeneratedCoCNumber() }}
                                            </span>
                                        </dd>
                                    </div>
                                @endif
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Toelichting</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $companyRequest->admin_notes }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection 