@extends('portal.layouts.v2.app')

@section('title', 'Nieuwe rol toevoegen')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="border-b border-white/10 pb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">Nieuwe rol</h1>
                <p class="text-slate-400 mt-2">Voeg een nieuwe rol toe aan het systeem</p>
            </div>
            <a href="{{ route('portal.admin.roles.index') }}" 
               class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                ← Terug naar overzicht
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-4xl">
        <form action="{{ route('portal.admin.roles.store') }}" method="POST" class="space-y-8">
            @csrf
            
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">🔑</span>
                    Rol informatie
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-300 mb-2">
                            Rol naam *
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                               placeholder="Bijv. Moderator, Admin, Manager..."
                               required>
                        @error('name')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Display Name -->
                    <div>
                        <label for="display_name" class="block text-sm font-medium text-slate-300 mb-2">
                            Weergave naam
                        </label>
                        <input type="text" 
                               id="display_name" 
                               name="display_name" 
                               value="{{ old('display_name') }}"
                               class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                               placeholder="Optioneel - voor mooiere weergave">
                        @error('display_name')
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
                              rows="3"
                              class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                              placeholder="Beschrijf wat deze rol inhoudt...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Permissions -->
            @if(isset($permissions) && $permissions->count() > 0)
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                        <span class="text-2xl">🛡️</span>
                        Rechten
                    </h2>

                    <div class="space-y-6">
                        @php
                            $groupedPermissions = $permissions->groupBy(function($permission) {
                                return explode('-', $permission->name)[0] ?? 'general';
                            });
                        @endphp

                        @foreach($groupedPermissions as $group => $groupPermissions)
                            <div class="bg-slate-800/30 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-white mb-4 capitalize">
                                    {{ ucfirst(str_replace('-', ' ', $group)) }} rechten
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($groupPermissions as $permission)
                                        <label class="flex items-center p-3 bg-slate-700/30 rounded-lg cursor-pointer hover:bg-slate-700/50 transition-colors">
                                            <input type="checkbox" 
                                                   name="permissions[]" 
                                                   value="{{ $permission->id }}"
                                                   {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                                   class="text-emerald-500 focus:ring-emerald-500 focus:ring-2 rounded">
                                            <div class="ml-3">
                                                <div class="text-white font-medium">{{ $permission->display_name ?? $permission->name }}</div>
                                                @if($permission->description)
                                                    <div class="text-slate-400 text-sm">{{ $permission->description }}</div>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Select All/None -->
                    <div class="mt-6 flex items-center space-x-4">
                        <button type="button" 
                                onclick="selectAllPermissions()"
                                class="bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Alles selecteren
                        </button>
                        <button type="button" 
                                onclick="selectNoPermissions()"
                                class="bg-red-500/20 hover:bg-red-500/30 text-red-300 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Niets selecteren
                        </button>
                    </div>
                </div>
            @endif

            <!-- Settings -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">⚙️</span>
                    Instellingen
                </h2>

                <div class="space-y-4">
                    <!-- Is Admin -->
                    <div class="flex items-center justify-between p-4 bg-slate-800/30 rounded-lg">
                        <div>
                            <h3 class="text-white font-medium">Administrator rol</h3>
                            <p class="text-slate-400 text-sm">Geeft volledige toegang tot alle functies</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="is_admin" 
                                   value="1"
                                   {{ old('is_admin') ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>

                    <!-- Is Default -->
                    <div class="flex items-center justify-between p-4 bg-slate-800/30 rounded-lg">
                        <div>
                            <h3 class="text-white font-medium">Standaard rol</h3>
                            <p class="text-slate-400 text-sm">Wordt automatisch toegewezen aan nieuwe gebruikers</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="is_default" 
                                   value="1"
                                   {{ old('is_default') ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('portal.admin.roles.index') }}" 
                   class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    Annuleren
                </a>
                <button type="submit" 
                        class="bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-3 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <span class="text-lg">💾</span>
                    Rol aanmaken
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function selectAllPermissions() {
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
    checkboxes.forEach(checkbox => checkbox.checked = true);
}

function selectNoPermissions() {
    const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
    checkboxes.forEach(checkbox => checkbox.checked = false);
}
</script>
@endsection 