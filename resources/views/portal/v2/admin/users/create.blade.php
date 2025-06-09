@extends('portal.layouts.v2.app')

@section('title', 'Nieuwe gebruiker toevoegen')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="border-b border-white/10 pb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">Nieuwe gebruiker</h1>
                <p class="text-slate-400 mt-2">Voeg een nieuwe gebruiker toe aan het systeem</p>
            </div>
            <a href="{{ route('portal.admin.users.index') }}" 
               class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                ← Terug naar overzicht
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-4xl">
        <form action="{{ route('portal.admin.users.store') }}" method="POST" class="space-y-8">
            @csrf
            
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">👤</span>
                    Gebruikersinformatie
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-300 mb-2">
                            Naam *
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                               placeholder="Voer de naam in..."
                               required>
                        @error('name')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-300 mb-2">
                            E-mailadres *
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                               placeholder="gebruiker@example.com"
                               required>
                        @error('email')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
                            Wachtwoord *
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password"
                               class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                               placeholder="Minimaal 8 karakters"
                               required>
                        @error('password')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">
                            Bevestig wachtwoord *
                        </label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation"
                               class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                               placeholder="Herhaal het wachtwoord"
                               required>
                    </div>

                    <!-- Minecraft UUID -->
                    <div class="md:col-span-2">
                        <label for="minecraft_uuid" class="block text-sm font-medium text-slate-300 mb-2">
                            Minecraft UUID
                        </label>
                        <input type="text" 
                               id="minecraft_uuid" 
                               name="minecraft_uuid" 
                               value="{{ old('minecraft_uuid') }}"
                               class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                               placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                        <p class="text-slate-400 text-sm mt-1">Optioneel - kan later worden toegevoegd</p>
                        @error('minecraft_uuid')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Account Settings -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">💰</span>
                    Account instellingen
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Balance -->
                    <div>
                        <label for="balance" class="block text-sm font-medium text-slate-300 mb-2">
                            Startsaldo ($)
                        </label>
                        <input type="number" 
                               id="balance" 
                               name="balance" 
                               value="{{ old('balance', 0) }}"
                               min="0"
                               step="1"
                               class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                               placeholder="0">
                        @error('balance')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Currency -->
                    <div>
                        <label for="currency" class="block text-sm font-medium text-slate-300 mb-2">
                            Valuta
                        </label>
                        <select id="currency" 
                                name="currency"
                                class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                            <option value="USD" {{ old('currency', 'USD') === 'USD' ? 'selected' : '' }}>USD ($)</option>
                            <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                            <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                        </select>
                        @error('currency')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Account Status -->
                <div class="mt-6 space-y-4">
                    <div class="flex items-center justify-between p-4 bg-slate-800/30 rounded-lg">
                        <div>
                            <h3 class="text-white font-medium">E-mail geverifieerd</h3>
                            <p class="text-slate-400 text-sm">Markeer het e-mailadres als geverifieerd</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="email_verified" 
                                   value="1"
                                   {{ old('email_verified', true) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-slate-800/30 rounded-lg">
                        <div>
                            <h3 class="text-white font-medium">Minecraft geverifieerd</h3>
                            <p class="text-slate-400 text-sm">Markeer het Minecraft account als geverifieerd</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   name="minecraft_verified" 
                                   value="1"
                                   {{ old('minecraft_verified') ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Roles -->
            @if(isset($roles) && $roles->count() > 0)
                <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                        <span class="text-2xl">🔑</span>
                        Rollen
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($roles as $role)
                            <label class="flex items-center p-4 bg-slate-800/30 rounded-lg cursor-pointer hover:bg-slate-800/50 transition-colors">
                                <input type="checkbox" 
                                       name="roles[]" 
                                       value="{{ $role->id }}"
                                       {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
                                       class="text-emerald-500 focus:ring-emerald-500 focus:ring-2 rounded">
                                <div class="ml-3">
                                    <div class="text-white font-medium">{{ $role->name }}</div>
                                    @if($role->description)
                                        <div class="text-slate-400 text-sm">{{ $role->description }}</div>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('portal.admin.users.index') }}" 
                   class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    Annuleren
                </a>
                <button type="submit" 
                        class="bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-3 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <span class="text-lg">💾</span>
                    Gebruiker aanmaken
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 