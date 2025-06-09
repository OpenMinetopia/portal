@extends('portal.layouts.v2.app')

@section('title', 'Plot beheren - ' . $plot->name)

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="border-b border-white/10 pb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">Plot beheren</h1>
                <p class="text-slate-400 mt-2">Beheer plot "{{ $plot->name }}" in {{ $plot->world }}</p>
            </div>
            <a href="{{ route('portal.admin.plots.index') }}" 
               class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                ← Terug naar overzicht
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Plot Info -->
        <div class="lg:col-span-1">
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">🗺️</span>
                    Plot informatie
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Naam</label>
                        <p class="text-white font-medium">{{ $plot->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Wereld</label>
                        <p class="text-white font-medium">{{ $plot->world }}</p>
                    </div>

                    @if($plot->size)
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Grootte</label>
                            <p class="text-white font-medium">{{ $plot->size }}m²</p>
                        </div>
                    @endif

                    @if($plot->price)
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Prijs</label>
                            <p class="text-emerald-400 font-bold">${{ number_format($plot->price) }}</p>
                        </div>
                    @endif

                    @if($plot->coordinates)
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Coördinaten</label>
                            <p class="text-white font-medium">{{ $plot->coordinates }}</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Aangemaakt</label>
                        <p class="text-white font-medium">{{ $plot->created_at->format('d-m-Y H:i') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Laatst bijgewerkt</label>
                        <p class="text-white font-medium">{{ $plot->updated_at->format('d-m-Y H:i') }}</p>
                    </div>
                </div>

                @if($plot->description)
                    <div class="mt-6 pt-6 border-t border-white/10">
                        <label class="block text-sm font-medium text-slate-400 mb-2">Beschrijving</label>
                        <div class="bg-slate-800/50 rounded-lg p-4">
                            <p class="text-slate-300">{{ $plot->description }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Statistics -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8 mt-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">📊</span>
                    Statistieken
                </h2>

                <div class="space-y-4">
                    <div class="bg-slate-800/30 rounded-lg p-4 text-center">
                        <p class="text-slate-400 text-sm">Eigenaren</p>
                        <p class="text-2xl font-bold text-white">{{ $plot->owners->count() }}</p>
                    </div>
                    <div class="bg-slate-800/30 rounded-lg p-4 text-center">
                        <p class="text-slate-400 text-sm">Leden</p>
                        <p class="text-2xl font-bold text-blue-400">{{ $plot->members->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Owners -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-white flex items-center gap-3">
                        <span class="text-2xl">👑</span>
                        Eigenaren
                    </h2>
                </div>

                @if($plot->owners->count() > 0)
                    <div class="space-y-3 mb-6">
                        @foreach($plot->owners as $owner)
                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-yellow-500/10 to-orange-500/10 border border-yellow-400/30 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">{{ substr($owner->name, 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-white font-medium">{{ $owner->name }}</div>
                                        <div class="text-slate-400 text-sm">{{ $owner->email }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('portal.admin.users.show', $owner) }}" 
                                       class="text-blue-400 hover:text-blue-300 transition-colors">
                                        <span class="text-lg">👁️</span>
                                    </a>
                                    <form action="{{ route('portal.admin.plots.owners.remove', $plot->name) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Weet je zeker dat je {{ $owner->name }} als eigenaar wilt verwijderen?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="user_id" value="{{ $owner->id }}">
                                        <button type="submit" class="text-red-400 hover:text-red-300 transition-colors">
                                            <span class="text-lg">🗑️</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 mb-6">
                        <div class="text-4xl mb-3">👑</div>
                        <p class="text-slate-400">Geen eigenaren gevonden</p>
                    </div>
                @endif

                <!-- Add Owner Form -->
                <div class="border-t border-white/10 pt-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Eigenaar toevoegen</h3>
                    <form action="{{ route('portal.admin.plots.owners.add', $plot->name) }}" method="POST" class="flex space-x-4">
                        @csrf
                        <div class="flex-1">
                            <input type="text" 
                                   name="username" 
                                   placeholder="Gebruikersnaam..."
                                   class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                   required>
                        </div>
                        <button type="submit" 
                                class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            Toevoegen
                        </button>
                    </form>
                </div>
            </div>

            <!-- Members -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-white flex items-center gap-3">
                        <span class="text-2xl">👥</span>
                        Leden
                    </h2>
                </div>

                @if($plot->members->count() > 0)
                    <div class="space-y-3 mb-6">
                        @foreach($plot->members as $member)
                            <div class="flex items-center justify-between p-4 bg-slate-800/30 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">{{ substr($member->name, 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-white font-medium">{{ $member->name }}</div>
                                        <div class="text-slate-400 text-sm">{{ $member->email }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('portal.admin.users.show', $member) }}" 
                                       class="text-blue-400 hover:text-blue-300 transition-colors">
                                        <span class="text-lg">👁️</span>
                                    </a>
                                    <form action="{{ route('portal.admin.plots.members.remove', $plot->name) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Weet je zeker dat je {{ $member->name }} als lid wilt verwijderen?')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="user_id" value="{{ $member->id }}">
                                        <button type="submit" class="text-red-400 hover:text-red-300 transition-colors">
                                            <span class="text-lg">🗑️</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 mb-6">
                        <div class="text-4xl mb-3">👥</div>
                        <p class="text-slate-400">Geen leden gevonden</p>
                    </div>
                @endif

                <!-- Add Member Form -->
                <div class="border-t border-white/10 pt-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Lid toevoegen</h3>
                    <form action="{{ route('portal.admin.plots.members.add', $plot->name) }}" method="POST" class="flex space-x-4">
                        @csrf
                        <div class="flex-1">
                            <input type="text" 
                                   name="username" 
                                   placeholder="Gebruikersnaam..."
                                   class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                   required>
                        </div>
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            Toevoegen
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 