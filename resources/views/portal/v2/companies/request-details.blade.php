@extends('portal.layouts.v2.app')

@section('title', 'Bedrijfsaanvraag - ' . $companyRequest->company_name)

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="border-b border-white/10 pb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">Bedrijfsaanvraag</h1>
                <p class="text-slate-400 mt-2">Details van je aanvraag voor "{{ $companyRequest->company_name }}"</p>
            </div>
            <a href="{{ route('portal.companies.index') }}" 
               class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                ← Terug naar overzicht
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Request Status -->
        <div class="lg:col-span-1">
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">📋</span>
                    Status
                </h2>

                <div class="text-center mb-6">
                    <div class="text-6xl mb-4">
                        @if($companyRequest->status === 'pending') ⏳
                        @elseif($companyRequest->status === 'approved') ✅
                        @elseif($companyRequest->status === 'rejected') ❌
                        @endif
                    </div>
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-medium
                        @if($companyRequest->status === 'pending') bg-yellow-500/20 text-yellow-300
                        @elseif($companyRequest->status === 'approved') bg-emerald-500/20 text-emerald-300
                        @elseif($companyRequest->status === 'rejected') bg-red-500/20 text-red-300
                        @endif">
                        @if($companyRequest->status === 'pending') In behandeling
                        @elseif($companyRequest->status === 'approved') Goedgekeurd
                        @elseif($companyRequest->status === 'rejected') Afgewezen
                        @endif
                    </span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Aangevraagd op</label>
                        <p class="text-white font-medium">{{ $companyRequest->created_at->format('d-m-Y H:i') }}</p>
                    </div>

                    @if($companyRequest->processed_at)
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">
                                @if($companyRequest->status === 'approved') Goedgekeurd op
                                @else Afgewezen op
                                @endif
                            </label>
                            <p class="text-white font-medium">{{ $companyRequest->processed_at->format('d-m-Y H:i') }}</p>
                        </div>
                    @endif

                    @if($companyRequest->processedBy)
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Behandeld door</label>
                            <p class="text-white font-medium">{{ $companyRequest->processedBy->name }}</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Registratiekosten</label>
                        <p class="text-emerald-400 font-bold">${{ number_format($companyRequest->companyType->registration_fee) }}</p>
                    </div>
                </div>

                @if($companyRequest->status === 'approved' && $companyRequest->company)
                    <div class="mt-6 pt-6 border-t border-white/10">
                        <a href="{{ route('portal.companies.show', $companyRequest->company) }}" 
                           class="w-full bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                            <span class="text-lg">🏢</span>
                            Bekijk bedrijf
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Request Details -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Company Information -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">🏢</span>
                    Bedrijfsinformatie
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Bedrijfsnaam</label>
                        <p class="text-white font-medium">{{ $companyRequest->company_name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Type bedrijf</label>
                        <p class="text-white font-medium">{{ $companyRequest->companyType->name }}</p>
                    </div>

                    @if($companyRequest->company_email)
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">E-mailadres</label>
                            <p class="text-white font-medium">{{ $companyRequest->company_email }}</p>
                        </div>
                    @endif

                    @if($companyRequest->company_phone)
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Telefoonnummer</label>
                            <p class="text-white font-medium">{{ $companyRequest->company_phone }}</p>
                        </div>
                    @endif

                    @if($companyRequest->company_address)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-400 mb-1">Adres</label>
                            <p class="text-white font-medium">{{ $companyRequest->company_address }}</p>
                        </div>
                    @endif
                </div>

                @if($companyRequest->company_description)
                    <div class="mt-6 pt-6 border-t border-white/10">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Beschrijving</label>
                        <div class="bg-slate-800/50 rounded-lg p-4">
                            <p class="text-slate-300 whitespace-pre-line">{{ $companyRequest->company_description }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Company Type Details -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">ℹ️</span>
                    Type informatie
                </h2>

                @if($companyRequest->companyType->description)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Beschrijving</label>
                        <div class="bg-slate-800/50 rounded-lg p-4">
                            <p class="text-slate-300">{{ $companyRequest->companyType->description }}</p>
                        </div>
                    </div>
                @endif

                @if($companyRequest->companyType->requirements)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Vereisten</label>
                        <div class="bg-slate-800/50 rounded-lg p-4">
                            <p class="text-slate-300 whitespace-pre-line">{{ $companyRequest->companyType->requirements }}</p>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-slate-800/30 rounded-lg p-4 text-center">
                        <p class="text-slate-400 text-sm">Registratiekosten</p>
                        <p class="text-emerald-400 font-bold">${{ number_format($companyRequest->companyType->registration_fee) }}</p>
                    </div>
                    @if($companyRequest->companyType->annual_fee)
                        <div class="bg-slate-800/30 rounded-lg p-4 text-center">
                            <p class="text-slate-400 text-sm">Jaarlijkse kosten</p>
                            <p class="text-blue-400 font-bold">${{ number_format($companyRequest->companyType->annual_fee) }}</p>
                        </div>
                    @endif
                    @if($companyRequest->companyType->max_employees)
                        <div class="bg-slate-800/30 rounded-lg p-4 text-center">
                            <p class="text-slate-400 text-sm">Max. werknemers</p>
                            <p class="text-white font-bold">{{ $companyRequest->companyType->max_employees }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($companyRequest->admin_notes)
                <!-- Admin Notes -->
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                        <span class="text-2xl">📝</span>
                        Notities van behandelaar
                    </h2>
                    <div class="bg-slate-800/50 rounded-lg p-4">
                        <p class="text-slate-300 whitespace-pre-line">{{ $companyRequest->admin_notes }}</p>
                    </div>
                </div>
            @endif

            @if($companyRequest->status === 'pending')
                <!-- Pending Actions -->
                <div class="bg-yellow-500/10 border border-yellow-400/30 rounded-xl p-8">
                    <h2 class="text-xl font-bold text-yellow-300 mb-4 flex items-center gap-3">
                        <span class="text-2xl">⏳</span>
                        In behandeling
                    </h2>
                    <p class="text-yellow-200 mb-4">
                        Je aanvraag wordt momenteel beoordeeld door onze medewerkers. 
                        Je ontvangt een notificatie zodra er een beslissing is genomen.
                    </p>
                    <div class="text-sm text-yellow-300">
                        <p>• Controleer regelmatig je notificaties</p>
                        <p>• Gemiddelde behandeltijd: 1-3 werkdagen</p>
                        <p>• Bij vragen kun je contact opnemen via Discord</p>
                    </div>
                </div>
            @elseif($companyRequest->status === 'approved')
                <!-- Approved Success -->
                <div class="bg-emerald-500/10 border border-emerald-400/30 rounded-xl p-8">
                    <h2 class="text-xl font-bold text-emerald-300 mb-4 flex items-center gap-3">
                        <span class="text-2xl">🎉</span>
                        Gefeliciteerd!
                    </h2>
                    <p class="text-emerald-200 mb-4">
                        Je bedrijfsaanvraag is goedgekeurd! Je bedrijf "{{ $companyRequest->company_name }}" is nu officieel geregistreerd.
                    </p>
                    <div class="text-sm text-emerald-300">
                        <p>• Je bedrijf is nu zichtbaar in het bedrijvenregister</p>
                        <p>• Je kunt nu werknemers aannemen en contracten afsluiten</p>
                        <p>• Vergeet niet je jaarlijkse kosten bij te houden</p>
                    </div>
                </div>
            @elseif($companyRequest->status === 'rejected')
                <!-- Rejected Info -->
                <div class="bg-red-500/10 border border-red-400/30 rounded-xl p-8">
                    <h2 class="text-xl font-bold text-red-300 mb-4 flex items-center gap-3">
                        <span class="text-2xl">❌</span>
                        Aanvraag afgewezen
                    </h2>
                    <p class="text-red-200 mb-4">
                        Helaas is je aanvraag voor "{{ $companyRequest->company_name }}" afgewezen.
                    </p>
                    <div class="text-sm text-red-300">
                        <p>• Bekijk de notities hierboven voor de reden</p>
                        <p>• Je kunt een nieuwe aanvraag indienen</p>
                        <p>• Bij vragen kun je contact opnemen via Discord</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 