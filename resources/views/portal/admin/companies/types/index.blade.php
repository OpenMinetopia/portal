@extends('portal.layouts.v2.app')

@section('title', 'Bedrijfstypes')
@section('header', 'Bedrijfstypes')

@section('content')
<div class="space-y-8">
    <!-- Page Header Card -->
    <div class="relative overflow-hidden glass-card rounded-2xl p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-r from-orange-500/10 to-red-500/10"></div>
        <div class="relative flex items-center justify-between">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <x-heroicon-s-building-office class="h-8 w-8 text-white"/>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Bedrijfstypes Beheer</h1>
                    <p class="text-gray-600 dark:text-slate-400 mt-1">Beheer alle beschikbare bedrijfstypes voor aanvragen</p>
                </div>
            </div>
            <a href="{{ route('portal.admin.companies.types.create') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-xl font-bold shadow-lg hover:from-orange-600 hover:to-red-700 transition-all duration-200 transform hover:scale-105">
                <x-heroicon-s-plus class="h-5 w-5"/>
                Nieuw Type
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
        <!-- Total Types -->
        <div class="glass-card rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <x-heroicon-s-building-office class="h-6 w-6 text-white"/>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-600 dark:text-slate-400 uppercase tracking-wider mb-1">Totaal Types</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $companyTypes->total() }}</p>
                </div>
            </div>
        </div>

        <!-- Active Types -->
        <div class="glass-card rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                    <x-heroicon-s-check-badge class="h-6 w-6 text-white"/>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-600 dark:text-slate-400 uppercase tracking-wider mb-1">Actieve Types</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $companyTypes->where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Total Companies -->
        <div class="glass-card rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 float-animation">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <x-heroicon-s-building-library class="h-6 w-6 text-white"/>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-600 dark:text-slate-400 uppercase tracking-wider mb-1">Totaal Bedrijven</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Models\Company::count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Types Table -->
    <div class="glass-card rounded-2xl shadow-lg float-animation">
        <div class="p-6 border-b border-gray-200/50 dark:border-white/10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-gray-600 to-gray-700 rounded-xl flex items-center justify-center shadow-lg">
                        <x-heroicon-s-table-cells class="h-6 w-6 text-white"/>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Alle Bedrijfstypes</h3>
                        <p class="text-gray-600 dark:text-slate-400">Overzicht van alle beschikbare bedrijfstypes</p>
                    </div>
                </div>
                <a href="{{ route('portal.admin.companies.types.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 glass-card hover:bg-gray-100/70 dark:hover:bg-white/10 text-gray-700 dark:text-slate-300 rounded-xl font-medium transition-all duration-200">
                    <x-heroicon-s-plus class="h-4 w-4"/>
                    Type Toevoegen
                </a>
            </div>
        </div>

        <div class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-700/30">
                            <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-slate-400">Bedrijfstype</th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-slate-400">Prijs</th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-slate-400">Status</th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-slate-400">Velden</th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-slate-400">Bedrijven</th>
                            <th scope="col" class="relative py-4 pl-3 pr-6">
                                <span class="sr-only">Acties</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200/50 dark:divide-gray-700/50">
                        @forelse($companyTypes as $type)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-all duration-200">
                                <td class="whitespace-nowrap py-4 pl-6 pr-3">
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $type->name }}</div>
                                        <div class="text-sm text-gray-600 dark:text-slate-400">{{ Str::limit($type->description, 50) }}</div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                    €{{ number_format($type->price, 2) }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <span class="inline-flex items-center rounded-xl px-3 py-1 text-xs font-bold {{ $type->is_active
                                        ? 'bg-green-500/20 text-green-700 dark:text-green-300'
                                        : 'bg-red-500/20 text-red-700 dark:text-red-300'
                                    }}">
                                        {{ $type->is_active ? 'Actief' : 'Inactief' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <span class="inline-flex items-center rounded-xl bg-blue-500/20 px-3 py-1 text-xs font-bold text-blue-700 dark:text-blue-300">
                                        {{ count($type->form_fields) }} velden
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <span class="inline-flex items-center rounded-xl bg-gray-500/20 px-3 py-1 text-xs font-bold text-gray-700 dark:text-gray-300">
                                        {{ $type->companies()->count() }} bedrijven
                                    </span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-medium">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('portal.admin.companies.types.edit', $type) }}"
                                           class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-bold transition-colors">
                                            Bewerken
                                        </a>
                                        <form action="{{ route('portal.admin.companies.types.destroy', $type) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Weet je zeker dat je dit bedrijfstype wilt verwijderen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-bold transition-colors">
                                                Verwijderen
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-2xl flex items-center justify-center mb-4">
                                            <x-heroicon-o-building-office class="h-8 w-8 text-gray-400"/>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Geen bedrijfstypes</h3>
                                        <p class="text-gray-600 dark:text-slate-400 mb-6">
                                            Er zijn nog geen bedrijfstypes aangemaakt.
                                        </p>
                                        <a href="{{ route('portal.admin.companies.types.create') }}"
                                           class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-xl font-bold shadow-lg hover:from-orange-600 hover:to-red-700 transition-all duration-200 transform hover:scale-105">
                                            <x-heroicon-s-plus class="h-4 w-4"/>
                                            Nieuw Type
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($companyTypes->hasPages())
            <div class="px-6 py-4 border-t border-gray-200/50 dark:border-white/10">
                {{ $companyTypes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection 