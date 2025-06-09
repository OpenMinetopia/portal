@extends('portal.layouts.v2.app')

@section('title', 'Spelersinformatie - ' . $user->name)

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="border-b border-white/10 pb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">Spelersinformatie</h1>
                <p class="text-slate-400 mt-2">Gedetailleerde informatie over {{ $user->name }}</p>
            </div>
            <a href="{{ route('portal.police.players.index') }}" 
               class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                ← Terug naar database
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Player Info Card -->
        <div class="lg:col-span-1">
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <div class="text-center">
                    <div class="w-24 h-24 bg-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-white font-bold text-2xl">{{ substr($user->name, 0, 2) }}</span>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-2">{{ $user->name }}</h2>
                    <p class="text-slate-400 mb-4">{{ $user->formatted_balance_with_currency }}</p>
                </div>

                <div class="space-y-4 border-t border-white/10 pt-6">
                    <div class="flex justify-between">
                        <span class="text-slate-400">ID:</span>
                        <span class="text-white font-medium">{{ $user->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Geregistreerd:</span>
                        <span class="text-white font-medium">{{ $user->created_at->format('d-m-Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Laatst actief:</span>
                        <span class="text-white font-medium">{{ $user->updated_at->diffForHumans() }}</span>
                    </div>
                    @if($user->minecraft_uuid)
                        <div class="flex justify-between">
                            <span class="text-slate-400">Minecraft UUID:</span>
                            <span class="text-white font-medium text-xs">{{ $user->minecraft_uuid }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Criminal Records -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white flex items-center gap-3">
                        <span class="text-2xl">📋</span>
                        Strafblad
                    </h3>
                    <span class="bg-red-500/20 text-red-300 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $user->criminalRecords->count() }} overtredingen
                    </span>
                </div>

                @if($user->criminalRecords->count() > 0)
                    <div class="space-y-4">
                        @foreach($user->criminalRecords->take(5) as $record)
                            <div class="bg-slate-800/50 border border-slate-700 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-semibold text-white">{{ $record->crime }}</h4>
                                    <span class="text-slate-400 text-sm">{{ $record->created_at->format('d-m-Y H:i') }}</span>
                                </div>
                                @if($record->description)
                                    <p class="text-slate-300 text-sm mb-3">{{ $record->description }}</p>
                                @endif
                                <div class="flex justify-between items-center">
                                    <span class="text-emerald-400 font-medium">${{ number_format($record->fine_amount) }}</span>
                                    <span class="text-slate-400 text-sm">Door: {{ $record->officer_name ?? 'Onbekend' }}</span>
                                </div>
                            </div>
                        @endforeach
                        
                        @if($user->criminalRecords->count() > 5)
                            <div class="text-center pt-4">
                                <p class="text-slate-400">En {{ $user->criminalRecords->count() - 5 }} andere overtredingen...</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">✅</div>
                        <h4 class="text-xl font-semibold text-white mb-2">Schoon strafblad</h4>
                        <p class="text-slate-400">Deze speler heeft geen geregistreerde overtredingen</p>
                    </div>
                @endif
            </div>

            <!-- Bank Accounts -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white flex items-center gap-3">
                        <span class="text-2xl">💰</span>
                        Bankrekeningen
                    </h3>
                    <span class="bg-emerald-500/20 text-emerald-300 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $user->bankAccounts->count() }} rekeningen
                    </span>
                </div>

                @if($user->bankAccounts->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($user->bankAccounts as $account)
                            <div class="bg-gradient-to-r from-emerald-500/10 to-blue-500/10 border border-emerald-400/30 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-semibold text-white">{{ $account->account_name }}</h4>
                                    <span class="text-emerald-400 font-bold">${{ number_format($account->balance) }}</span>
                                </div>
                                <p class="text-slate-400 text-sm">{{ $account->uuid }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-4xl mb-3">🏦</div>
                        <p class="text-slate-400">Geen bankrekeningen gevonden</p>
                    </div>
                @endif
            </div>

            <!-- Plots -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white flex items-center gap-3">
                        <span class="text-2xl">🗺️</span>
                        Eigendom
                    </h3>
                    <span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $user->ownedPlots->count() }} plots
                    </span>
                </div>

                @if($user->ownedPlots->count() > 0)
                    <div class="space-y-3">
                        @foreach($user->ownedPlots->take(10) as $plot)
                            <div class="bg-slate-800/50 border border-slate-700 rounded-lg p-4 flex justify-between items-center">
                                <div>
                                    <h4 class="font-semibold text-white">{{ $plot->name }}</h4>
                                    <p class="text-slate-400 text-sm">{{ $plot->world }} • {{ $plot->size }}m²</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-blue-400 font-medium">${{ number_format($plot->price ?? 0) }}</span>
                                </div>
                            </div>
                        @endforeach
                        
                        @if($user->ownedPlots->count() > 10)
                            <div class="text-center pt-4">
                                <p class="text-slate-400">En {{ $user->ownedPlots->count() - 10 }} andere plots...</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-4xl mb-3">🏠</div>
                        <p class="text-slate-400">Geen plots in eigendom</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 