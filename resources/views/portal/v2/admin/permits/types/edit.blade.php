@extends('portal.layouts.v2.app')

@section('title', 'Vergunningtype bewerken - ' . $permitType->name)

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="border-b border-white/10 pb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">Vergunningtype bewerken</h1>
                <p class="text-slate-400 mt-2">Bewerk de instellingen van "{{ $permitType->name }}"</p>
            </div>
            <a href="{{ route('portal.admin.permits.types.index') }}" 
               class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                ← Terug naar overzicht
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-4xl">
        <form action="{{ route('portal.admin.permits.types.update', $permitType) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">📄</span>
                    Basisinformatie
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-slate-300 mb-2">
                            Naam van het vergunningtype *
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $permitType->name) }}"
                               class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                               placeholder="Bijv. Bouwvergunning, Evenementenvergunning..."
                               required>
                        @error('name')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cost -->
                    <div>
                        <label for="cost" class="block text-sm font-medium text-slate-300 mb-2">
                            Kosten ($) *
                        </label>
                        <input type="number" 
                               id="cost" 
                               name="cost" 
                               value="{{ old('cost', $permitType->cost) }}"
                               min="0"
                               step="1"
                               class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                               placeholder="0"
                               required>
                        @error('cost')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Duration -->
                    <div>
                        <label for="duration_days" class="block text-sm font-medium text-slate-300 mb-2">
                            Geldigheid (dagen)
                        </label>
                        <input type="number" 
                               id="duration_days" 
                               name="duration_days" 
                               value="{{ old('duration_days', $permitType->duration_days) }}"
                               min="1"
                               class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                               placeholder="Laat leeg voor permanente vergunning">
                        <p class="text-slate-400 text-sm mt-1">Laat leeg voor een permanente vergunning</p>
                        @error('duration_days')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-slate-300 mb-2">
                        Beschrijving
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                              placeholder="Beschrijf waarvoor deze vergunning gebruikt wordt...">{{ old('description', $permitType->description) }}</textarea>
                    @error('description')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Requirements -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">📋</span>
                    Vereisten
                </h2>

                <div class="space-y-6">
                    <!-- Requirements -->
                    <div>
                        <label for="requirements" class="block text-sm font-medium text-slate-300 mb-2">
                            Vereisten voor aanvraag
                        </label>
                        <textarea id="requirements" 
                                  name="requirements" 
                                  rows="4"
                                  class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                  placeholder="Lijst de vereisten op die nodig zijn voor deze vergunning...">{{ old('requirements', $permitType->requirements) }}</textarea>
                        <p class="text-slate-400 text-sm mt-1">Deze informatie wordt getoond aan gebruikers bij het aanvragen</p>
                        @error('requirements')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Required Documents -->
                    <div>
                        <label for="required_documents" class="block text-sm font-medium text-slate-300 mb-2">
                            Benodigde documenten
                        </label>
                        <textarea id="required_documents" 
                                  name="required_documents" 
                                  rows="3"
                                  class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                  placeholder="Lijst de documenten op die meegestuurd moeten worden...">{{ old('required_documents', $permitType->required_documents) }}</textarea>
                        @error('required_documents')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">⚙️</span>
                    Instellingen
                </h2>

                <div class="space-y-6">
                    <!-- Active Status -->
                    <div class="flex items-center justify-between p-4 bg-slate-800/30 rounded-lg">
                        <div>
                            <h3 class="text-white font-medium">Vergunningtype actief</h3>
                            <p class="text-slate-400 text-sm">Wanneer actief kunnen gebruikers dit type vergunning aanvragen</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', $permitType->is_active) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>

                    <!-- Auto Approval -->
                    <div class="flex items-center justify-between p-4 bg-slate-800/30 rounded-lg">
                        <div>
                            <h3 class="text-white font-medium">Automatische goedkeuring</h3>
                            <p class="text-slate-400 text-sm">Vergunningen van dit type worden automatisch goedgekeurd</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="auto_approve" 
                                   value="1"
                                   {{ old('auto_approve', $permitType->auto_approve) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">📊</span>
                    Statistieken
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-slate-800/30 rounded-lg p-4 text-center">
                        <p class="text-slate-400 text-sm">Totaal aanvragen</p>
                        <p class="text-2xl font-bold text-white">{{ $permitType->permitRequests->count() }}</p>
                    </div>
                    <div class="bg-slate-800/30 rounded-lg p-4 text-center">
                        <p class="text-slate-400 text-sm">Goedgekeurd</p>
                        <p class="text-2xl font-bold text-emerald-400">{{ $permitType->permitRequests->where('status', 'approved')->count() }}</p>
                    </div>
                    <div class="bg-slate-800/30 rounded-lg p-4 text-center">
                        <p class="text-slate-400 text-sm">Afgewezen</p>
                        <p class="text-2xl font-bold text-red-400">{{ $permitType->permitRequests->where('status', 'rejected')->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('portal.admin.permits.types.index') }}" 
                   class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    Annuleren
                </a>
                <button type="submit" 
                        class="bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-3 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <span class="text-lg">💾</span>
                    Wijzigingen opslaan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 