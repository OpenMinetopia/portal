@extends('portal.layouts.v2.app')

@section('title', 'Gebruiker bewerken - ' . $user->name)

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="border-b border-white/10 pb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-white">Gebruiker bewerken</h1>
                <p class="text-slate-400 mt-2">Bewerk de gegevens van {{ $user->name }}</p>
            </div>
            <div class="flex items-center space-x-4 flex-shrink-0">
                <a href="{{ route('portal.admin.users.show', $user) }}" 
                   class="bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 border border-blue-400/30 px-6 py-3 rounded-lg font-medium transition-all duration-200 backdrop-blur-sm">
                    👁️ Bekijk profiel
                </a>
                <a href="{{ route('portal.admin.users.index') }}" 
                   class="bg-slate-600/20 hover:bg-slate-600/30 text-slate-300 border border-slate-500/30 px-6 py-3 rounded-lg font-medium transition-all duration-200 backdrop-blur-sm">
                    ← Terug naar overzicht
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="max-w-4xl">
        <form action="{{ route('portal.admin.users.update', $user) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            
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
                               value="{{ old('name', $user->name) }}"
                               class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
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
                               value="{{ old('email', $user->email) }}"
                               class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                               required>
                        @error('email')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Minecraft UUID -->
                    <div class="md:col-span-2">
                        <label for="minecraft_uuid" class="block text-sm font-medium text-slate-300 mb-2">
                            Minecraft UUID
                        </label>
                        <input type="text" 
                               id="minecraft_uuid" 
                               name="minecraft_uuid" 
                               value="{{ old('minecraft_uuid', $user->minecraft_uuid) }}"
                               class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                               placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
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
                            Saldo ($)
                        </label>
                        <input type="number" 
                               id="balance" 
                               name="balance" 
                               value="{{ old('balance', $user->balance) }}"
                               min="0"
                               step="1"
                               class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
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
                            <option value="USD" {{ old('currency', $user->currency ?? 'USD') === 'USD' ? 'selected' : '' }}>USD ($)</option>
                            <option value="EUR" {{ old('currency', $user->currency) === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                            <option value="GBP" {{ old('currency', $user->currency) === 'GBP' ? 'selected' : '' }}>GBP (£)</option>
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
                                   {{ old('email_verified', $user->email_verified_at ? true : false) ? 'checked' : '' }}
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
                                   {{ old('minecraft_verified', $user->minecraft_verified_at ? true : false) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Password Change -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">🔒</span>
                    Wachtwoord wijzigen
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
                            Nieuw wachtwoord
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password"
                               class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                               placeholder="Laat leeg om niet te wijzigen">
                        <p class="text-slate-400 text-sm mt-1">Laat leeg om het huidige wachtwoord te behouden</p>
                        @error('password')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">
                            Bevestig nieuw wachtwoord
                        </label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation"
                               class="w-full bg-slate-800/50 border border-slate-600 rounded-lg px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                               placeholder="Herhaal het nieuwe wachtwoord">
                    </div>
                </div>
            </div>

            <!-- Account Statistics -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">📊</span>
                    Account statistieken
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-slate-800/30 rounded-lg p-4 text-center">
                        <p class="text-slate-400 text-sm">Lid sinds</p>
                        <p class="text-white font-bold">{{ $user->created_at->format('d-m-Y') }}</p>
                    </div>
                    <div class="bg-slate-800/30 rounded-lg p-4 text-center">
                        <p class="text-slate-400 text-sm">Plots</p>
                        <p class="text-white font-bold">{{ $user->ownedPlots->count() }}</p>
                    </div>
                    <div class="bg-slate-800/30 rounded-lg p-4 text-center">
                        <p class="text-slate-400 text-sm">Bankrekeningen</p>
                        <p class="text-white font-bold">{{ $user->bankAccounts->count() }}</p>
                    </div>
                    <div class="bg-slate-800/30 rounded-lg p-4 text-center">
                        <p class="text-slate-400 text-sm">Strafblad</p>
                        <p class="text-white font-bold">{{ $user->criminalRecords->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('portal.admin.users.index') }}" 
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