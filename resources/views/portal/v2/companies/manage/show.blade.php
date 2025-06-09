@extends('portal.layouts.v2.app')

@section('title', 'Bedrijfs Aanvraag')
@section('header', 'Bedrijfs Aanvraag')

@section('content')
    <div class="space-y-8">
        <!-- Back Button -->
        <div>
            <a href="{{ route('portal.companies.manage.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-100/80 dark:bg-gray-800/80 text-gray-700 dark:text-gray-300 hover:bg-gray-200/80 dark:hover:bg-gray-700/80 transition-all duration-200 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50">
                <x-heroicon-s-arrow-left class="w-4 h-4"/>
                Terug naar overzicht
            </a>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Left Column - Request Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Request Info -->
                <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-indigo-600/5"></div>
                    <div class="relative">
                        <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                                        <x-heroicon-s-document-text class="h-5 w-5 text-white" />
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Aanvraag Details</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Ingediend op {{ $companyRequest->created_at->format('d-m-Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <span @class([
                                        'inline-flex items-center rounded-xl px-4 py-2 text-sm font-bold shadow-sm backdrop-blur-sm border',
                                        'bg-yellow-500/20 text-yellow-700 dark:text-yellow-300 border-yellow-500/30' => $companyRequest->status === 'pending',
                                        'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-500/30' => $companyRequest->status === 'approved',
                                        'bg-red-500/20 text-red-700 dark:text-red-300 border-red-500/30' => $companyRequest->status === 'denied',
                                    ])>
                                        @if($companyRequest->status === 'pending')
                                            <div class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse mr-2"></div>
                                        @elseif($companyRequest->status === 'approved')
                                            <div class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></div>
                                        @else
                                            <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                        @endif
                                        {{ $companyRequest->getStatusText() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Aanvrager</dt>
                                    <dd class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $companyRequest->user->minecraft_username }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Type</dt>
                                    <dd class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $companyRequest->type->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Bedrijfsnaam</dt>
                                    <dd class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $companyRequest->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Kosten</dt>
                                    <dd class="mt-1 text-lg font-bold text-emerald-600 dark:text-emerald-400">€{{ number_format($companyRequest->type->price, 2) }}</dd>
                                </div>
                                @if($companyRequest->handled_at)
                                    <div>
                                        <dt class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Verwerkt door</dt>
                                        <dd class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $companyRequest->handler->minecraft_username }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Verwerkt op</dt>
                                        <dd class="mt-1 text-lg font-bold text-gray-900 dark:text-white font-mono">{{ $companyRequest->handled_at->format('d-m-Y H:i') }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Form Data -->
                <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-green-600/5"></div>
                    <div class="relative">
                        <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-green-600 rounded-xl flex items-center justify-center">
                                    <x-heroicon-s-clipboard-document-list class="h-5 w-5 text-white" />
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Formulier Gegevens</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Ingediende formulier data</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 gap-x-6 gap-y-6">
                                @foreach($companyRequest->type->form_fields as $field)
                                    <div>
                                        <dt class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">
                                            {{ $field['label'] }}
                                            @if($field['required'])
                                                <span class="text-red-500 ml-1">*</span>
                                            @endif
                                        </dt>
                                        <dd>
                                            @switch($field['type'])
                                                @case('textarea')
                                                    <div class="bg-gray-50/50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200/50 dark:border-gray-600/50">
                                                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap leading-relaxed">{{ $companyRequest->form_data[$field['label']] }}</p>
                                                    </div>
                                                    @break
                                                @case('checkbox')
                                                    <span @class([
                                                        'inline-flex items-center rounded-xl px-3 py-1.5 text-sm font-bold shadow-sm backdrop-blur-sm border',
                                                        'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-500/30' => $companyRequest->form_data[$field['label']],
                                                        'bg-red-500/20 text-red-700 dark:text-red-300 border-red-500/30' => !$companyRequest->form_data[$field['label']],
                                                    ])>
                                                        @if($companyRequest->form_data[$field['label']])
                                                            <x-heroicon-s-check class="w-4 h-4 mr-1" />
                                                            Ja
                                                        @else
                                                            <x-heroicon-s-x-mark class="w-4 h-4 mr-1" />
                                                            Nee
                                                        @endif
                                                    </span>
                                                    @break
                                                @default
                                                    <div class="bg-gray-50/50 dark:bg-gray-700/50 rounded-xl px-4 py-3 border border-gray-200/50 dark:border-gray-600/50">
                                                        <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $companyRequest->form_data[$field['label']] }}</p>
                                                    </div>
                                            @endswitch
                                        </dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-8">
                @if($companyRequest->isPending())
                    <!-- Preview Card -->
                    <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-600/5"></div>
                        <div class="relative">
                            <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                                        <x-heroicon-s-eye class="h-5 w-5 text-white" />
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">KvK Nummer Preview</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Voorvertoning</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 text-center">
                                <div class="bg-gradient-to-r from-indigo-500/10 to-purple-600/10 rounded-2xl p-6 border border-indigo-500/20">
                                    <span class="text-2xl font-black font-mono text-gray-900 dark:text-white">
                                        {{ $companyRequest->getGeneratedCoCNumber() }}
                                    </span>
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        Dit KvK nummer wordt toegewezen bij goedkeuring
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Handle Request Form -->
                    <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                        <div class="absolute inset-0 bg-gradient-to-br from-orange-500/5 to-red-600/5"></div>
                        <div class="relative">
                            <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center">
                                        <x-heroicon-s-cog-6-tooth class="h-5 w-5 text-white" />
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Aanvraag Verwerken</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Kies een actie</p>
                                    </div>
                                </div>
                            </div>
                            <form action="{{ route('portal.companies.manage.handle', $companyRequest) }}" method="POST">
                                @csrf
                                <div class="p-6 space-y-6">
                                    <!-- Status Selection -->
                                    <div x-data="{ status: '' }" class="space-y-6">
                                        <label class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status Keuze</label>
                                        <div class="grid grid-cols-1 gap-4">
                                            <!-- Approve Button -->
                                            <label class="cursor-pointer">
                                                <input type="radio" name="status" value="approved" x-model="status" class="peer sr-only" required>
                                                <div class="relative px-6 py-4 rounded-xl border-2 border-gray-200 dark:border-gray-600 transition-all duration-200
                                                            peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50 dark:peer-checked:bg-emerald-500/10 
                                                            hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center">
                                                                <x-heroicon-s-check-circle class="w-5 h-5 text-white"/>
                                                            </div>
                                                            <div>
                                                                <span class="text-sm font-bold text-gray-900 dark:text-white">Goedkeuren</span>
                                                                <p class="text-xs text-gray-600 dark:text-gray-400">Bedrijf wordt aangemaakt</p>
                                                            </div>
                                                        </div>
                                                        <div class="text-emerald-500 dark:text-emerald-400" x-show="status === 'approved'">
                                                            <x-heroicon-s-check class="w-6 h-6"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>

                                            <!-- Deny Button -->
                                            <label class="cursor-pointer">
                                                <input type="radio" name="status" value="denied" x-model="status" class="peer sr-only" required>
                                                <div class="relative px-6 py-4 rounded-xl border-2 border-gray-200 dark:border-gray-600 transition-all duration-200
                                                            peer-checked:border-red-500 peer-checked:bg-red-50/50 dark:peer-checked:bg-red-500/10 
                                                            hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center">
                                                                <x-heroicon-s-x-circle class="w-5 h-5 text-white"/>
                                                            </div>
                                                            <div>
                                                                <span class="text-sm font-bold text-gray-900 dark:text-white">Afwijzen</span>
                                                                <p class="text-xs text-gray-600 dark:text-gray-400">Aanvraag wordt geweigerd</p>
                                                            </div>
                                                        </div>
                                                        <div class="text-red-500 dark:text-red-400" x-show="status === 'denied'">
                                                            <x-heroicon-s-check class="w-6 h-6"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>

                                        <!-- Refund Option (shows when denying) -->
                                        <div x-show="status === 'denied'" x-transition class="space-y-4">
                                            <div class="bg-red-50/50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30 rounded-xl p-4">
                                                <div class="flex items-start gap-3">
                                                    <div class="flex h-5 items-center">
                                                        <input type="checkbox" 
                                                               name="should_refund" 
                                                               id="should_refund" 
                                                               value="1"
                                                               checked
                                                               class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500 dark:border-gray-600 dark:bg-gray-700">
                                                    </div>
                                                    <div>
                                                        <label for="should_refund" class="text-sm font-bold text-gray-900 dark:text-white">
                                                            Bedrag terugstorten
                                                        </label>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                            Stort € {{ number_format($companyRequest->price, 2, ',', '.') }} terug naar de bankrekening van de aanvrager
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @error('status')
                                            <p class="text-sm text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Notes -->
                                    <div>
                                        <label for="admin_notes" class="block text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                                            Toelichting <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="admin_notes" id="admin_notes" rows="4" required
                                                  class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm transition-all duration-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:text-white resize-none"
                                                  placeholder="Geef een toelichting op je beslissing...">{{ old('admin_notes') }}</textarea>
                                        @error('admin_notes')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit"
                                            class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-3 text-sm font-bold text-white shadow-lg hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 transform hover:scale-105">
                                        <x-heroicon-s-check class="h-4 w-4"/>
                                        Beslissing Verwerken
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Decision Info -->
                    <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
                        <div class="absolute inset-0 bg-gradient-to-br from-gray-500/5 to-slate-600/5"></div>
                        <div class="relative">
                            <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-gray-500 to-slate-600 rounded-xl flex items-center justify-center">
                                        <x-heroicon-s-clipboard-document-check class="h-5 w-5 text-white" />
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Beslissing</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Genomen besluit</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                <dl class="space-y-6">
                                    @if($companyRequest->isApproved())
                                        <div>
                                            <dt class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">KvK Nummer</dt>
                                            <dd>
                                                <div class="bg-emerald-50/50 dark:bg-emerald-500/10 rounded-xl p-4 border border-emerald-200/50 dark:border-emerald-500/30 text-center">
                                                    <span class="text-xl font-black font-mono text-emerald-700 dark:text-emerald-300">
                                                        {{ $companyRequest->getGeneratedCoCNumber() }}
                                                    </span>
                                                </div>
                                            </dd>
                                        </div>
                                    @endif
                                    <div>
                                        <dt class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Toelichting</dt>
                                        <dd>
                                            <div class="bg-gray-50/50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200/50 dark:border-gray-600/50">
                                                <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap leading-relaxed">{{ $companyRequest->admin_notes }}</p>
                                            </div>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection 