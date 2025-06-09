@extends('portal.layouts.v2.app')

@section('title', 'Vergunningaanvraag beheren - ' . $permitRequest->permitType->name)

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="border-b border-white/10 pb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">Vergunningaanvraag beheren</h1>
                <p class="text-slate-400 mt-2">Beoordeel de aanvraag voor {{ $permitRequest->permitType->name }}</p>
            </div>
            <a href="{{ route('portal.permits.manage.index') }}" 
               class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                ← Terug naar overzicht
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Request Details -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Basic Info -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">📄</span>
                    Aanvraagdetails
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Vergunningtype</label>
                        <p class="text-white font-medium">{{ $permitRequest->permitType->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Aanvrager</label>
                        <p class="text-white font-medium">{{ $permitRequest->user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Aangevraagd op</label>
                        <p class="text-white font-medium">{{ $permitRequest->created_at->format('d-m-Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Status</label>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($permitRequest->status === 'pending') bg-yellow-500/20 text-yellow-300
                            @elseif($permitRequest->status === 'approved') bg-emerald-500/20 text-emerald-300
                            @elseif($permitRequest->status === 'rejected') bg-red-500/20 text-red-300
                            @endif">
                            @if($permitRequest->status === 'pending') In behandeling
                            @elseif($permitRequest->status === 'approved') Goedgekeurd
                            @elseif($permitRequest->status === 'rejected') Afgewezen
                            @endif
                        </span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Kosten</label>
                        <p class="text-emerald-400 font-bold">${{ number_format($permitRequest->permitType->cost) }}</p>
                    </div>
                    @if($permitRequest->permitType->duration_days)
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Geldigheid</label>
                            <p class="text-white font-medium">{{ $permitRequest->permitType->duration_days }} dagen</p>
                        </div>
                    @endif
                </div>

                @if($permitRequest->reason)
                    <div class="mt-6 pt-6 border-t border-white/10">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Reden voor aanvraag</label>
                        <div class="bg-slate-800/50 rounded-lg p-4">
                            <p class="text-slate-300">{{ $permitRequest->reason }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Permit Type Info -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">ℹ️</span>
                    Vergunningtype informatie
                </h2>

                @if($permitRequest->permitType->description)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Beschrijving</label>
                        <div class="bg-slate-800/50 rounded-lg p-4">
                            <p class="text-slate-300">{{ $permitRequest->permitType->description }}</p>
                        </div>
                    </div>
                @endif

                @if($permitRequest->permitType->requirements)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Vereisten</label>
                        <div class="bg-slate-800/50 rounded-lg p-4">
                            <p class="text-slate-300 whitespace-pre-line">{{ $permitRequest->permitType->requirements }}</p>
                        </div>
                    </div>
                @endif

                @if($permitRequest->permitType->required_documents)
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Benodigde documenten</label>
                        <div class="bg-slate-800/50 rounded-lg p-4">
                            <p class="text-slate-300 whitespace-pre-line">{{ $permitRequest->permitType->required_documents }}</p>
                        </div>
                    </div>
                @endif
            </div>

            @if($permitRequest->admin_notes)
                <!-- Admin Notes -->
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                        <span class="text-2xl">📝</span>
                        Beheerder notities
                    </h2>
                    <div class="bg-slate-800/50 rounded-lg p-4">
                        <p class="text-slate-300 whitespace-pre-line">{{ $permitRequest->admin_notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Actions Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8 sticky top-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">⚙️</span>
                    Acties
                </h2>

                @if($permitRequest->status === 'pending')
                    <form action="{{ route('portal.permits.manage.handle', $permitRequest) }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <!-- Decision -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-3">Beslissing</label>
                            <div class="space-y-3">
                                <label class="flex items-center p-3 bg-emerald-500/10 border border-emerald-400/30 rounded-lg cursor-pointer hover:bg-emerald-500/20 transition-colors">
                                    <input type="radio" name="action" value="approve" class="text-emerald-500 focus:ring-emerald-500 focus:ring-2">
                                    <span class="ml-3 text-emerald-300 font-medium">Goedkeuren</span>
                                </label>
                                <label class="flex items-center p-3 bg-red-500/10 border border-red-400/30 rounded-lg cursor-pointer hover:bg-red-500/20 transition-colors">
                                    <input type="radio" name="action" value="reject" class="text-red-500 focus:ring-red-500 focus:ring-2">
                                    <span class="ml-3 text-red-300 font-medium">Afwijzen</span>
                                </label>
                            </div>
                        </div>

                        <!-- Admin Notes -->
                        <div>
                            <label for="admin_notes" class="block text-sm font-medium text-slate-300 mb-2">
                                Notities (optioneel)
                            </label>
                            <textarea id="admin_notes" 
                                      name="admin_notes" 
                                      rows="4"
                                      class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                      placeholder="Voeg eventuele notities toe...">{{ old('admin_notes', $permitRequest->admin_notes) }}</textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                            <span class="text-lg">✅</span>
                            Beslissing opslaan
                        </button>
                    </form>
                @else
                    <!-- Already Processed -->
                    <div class="text-center py-8">
                        <div class="text-4xl mb-4">
                            @if($permitRequest->status === 'approved') ✅
                            @else ❌
                            @endif
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">
                            @if($permitRequest->status === 'approved') Goedgekeurd
                            @else Afgewezen
                            @endif
                        </h3>
                        <p class="text-slate-400 text-sm">
                            @if($permitRequest->processed_at)
                                {{ $permitRequest->processed_at->format('d-m-Y H:i') }}
                            @endif
                        </p>
                        @if($permitRequest->processed_by)
                            <p class="text-slate-400 text-sm mt-1">
                                Door: {{ $permitRequest->processedBy->name ?? 'Onbekend' }}
                            </p>
                        @endif
                    </div>
                @endif

                <!-- User Info -->
                <div class="mt-8 pt-8 border-t border-white/10">
                    <h3 class="text-lg font-semibold text-white mb-4">Aanvrager informatie</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Naam:</span>
                            <span class="text-white">{{ $permitRequest->user->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Saldo:</span>
                            <span class="text-emerald-400">${{ number_format($permitRequest->user->balance) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Lid sinds:</span>
                            <span class="text-white">{{ $permitRequest->user->created_at->format('d-m-Y') }}</span>
                        </div>
                    </div>
                    
                    <a href="{{ route('portal.police.players.show', $permitRequest->user) }}" 
                       class="mt-4 w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2">
                        <span>👤</span>
                        Bekijk profiel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 