@extends('portal.layouts.v2.app')

@section('title', 'Type vergunningen beheren')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="border-b border-white/10 pb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">Type vergunningen</h1>
                <p class="text-slate-400 mt-2">Beheer alle beschikbare vergunningtypes</p>
            </div>
            <a href="{{ route('portal.admin.permits.types.create') }}" 
               class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center gap-2">
                <span class="text-lg">➕</span>
                Nieuw type toevoegen
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm font-medium">Totaal types</p>
                    <p class="text-3xl font-bold text-white">{{ $permitTypes->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">📄</span>
                </div>
            </div>
        </div>

        <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm font-medium">Actieve types</p>
                    <p class="text-3xl font-bold text-emerald-400">{{ $permitTypes->where('is_active', true)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">✅</span>
                </div>
            </div>
        </div>

        <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm font-medium">Inactieve types</p>
                    <p class="text-3xl font-bold text-red-400">{{ $permitTypes->where('is_active', false)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center">
                    <span class="text-2xl">❌</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Permit Types Table -->
    <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl overflow-hidden">
        <div class="p-6 border-b border-white/10">
            <h2 class="text-xl font-bold text-white">Alle vergunningtypes</h2>
        </div>

        @if($permitTypes->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Kosten</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Duur</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Aanvragen</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Acties</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($permitTypes as $permitType)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-white font-medium">{{ $permitType->name }}</div>
                                        @if($permitType->description)
                                            <div class="text-slate-400 text-sm mt-1">{{ Str::limit($permitType->description, 60) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-emerald-400 font-medium">${{ number_format($permitType->cost) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-300">
                                        @if($permitType->duration_days)
                                            {{ $permitType->duration_days }} dagen
                                        @else
                                            Permanent
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($permitType->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-300">
                                            Actief
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-300">
                                            Inactief
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-slate-300">{{ $permitType->permitRequests->count() }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('portal.admin.permits.types.edit', $permitType) }}" 
                                           class="text-blue-400 hover:text-blue-300 transition-colors">
                                            <span class="text-lg">✏️</span>
                                        </a>
                                        <form action="{{ route('portal.admin.permits.types.destroy', $permitType) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Weet je zeker dat je dit vergunningtype wilt verwijderen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300 transition-colors">
                                                <span class="text-lg">🗑️</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-16">
                <div class="text-6xl mb-4">📄</div>
                <h3 class="text-xl font-semibold text-white mb-2">Geen vergunningtypes</h3>
                <p class="text-slate-400 mb-6">Er zijn nog geen vergunningtypes aangemaakt.</p>
                <a href="{{ route('portal.admin.permits.types.create') }}" 
                   class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-lg font-medium transition-colors inline-flex items-center gap-2">
                    <span class="text-lg">➕</span>
                    Eerste type toevoegen
                </a>
            </div>
        @endif
    </div>
</div>
@endsection 