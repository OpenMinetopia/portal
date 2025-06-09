@extends('portal.layouts.v2.app')

@section('title', 'Gebruiker Bewerken')
@section('header', $user->minecraft_username . ' Bewerken')

@section('content')
<div class="space-y-8">
    <!-- Back Button -->
    <div>
        <a href="{{ route('portal.admin.users.show', $user) }}" class="inline-flex items-center text-sm font-medium text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white transition-colors">
            <x-heroicon-s-arrow-left class="w-5 h-5 mr-2" />
            Terug naar details
        </a>
    </div>

    <!-- Page Header Card -->
    <div class="relative overflow-hidden glass-card rounded-2xl p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-purple-500/10"></div>
        <div class="relative flex items-center justify-between">
            <div class="flex items-center gap-6">
                <img src="https://crafatar.com/avatars/{{ $user->minecraft_uuid }}?overlay=true&size=128"
                     alt="{{ $user->minecraft_username }}"
                     class="w-20 h-20 rounded-2xl shadow-lg">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $user->minecraft_username }}</h1>
                    <p class="text-gray-600 dark:text-slate-400 mt-1">Bewerk de gebruikersgegevens</p>
                </div>
            </div>
            <a href="{{ route('portal.admin.users.show', $user) }}" 
               class="inline-flex items-center gap-2 px-6 py-3 glass-card hover:bg-gray-100/70 dark:hover:bg-white/10 text-gray-700 dark:text-slate-300 rounded-xl font-medium transition-all duration-200">
                <x-heroicon-s-x-mark class="h-4 w-4"/>
                Annuleren
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Left Column - Main Info -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Basic Info Form -->
            <div class="glass-card rounded-2xl shadow-lg">
                <div class="p-6 border-b border-gray-200/50 dark:border-white/10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-heroicon-s-user class="h-6 w-6 text-white"/>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Basis Informatie</h3>
                            <p class="text-gray-600 dark:text-slate-400">Bewerk de basis gegevens van de gebruiker</p>
                        </div>
                    </div>
                </div>
                <form action="{{ route('portal.admin.users.update', $user) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-bold text-gray-900 dark:text-white mb-2">Naam</label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $user->name) }}"
                                   class="w-full bg-gray-100/50 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-bold text-gray-900 dark:text-white mb-2">Email</label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email', $user->email) }}"
                                   class="w-full bg-gray-100/50 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Minecraft Username -->
                        <div>
                            <label for="minecraft_username" class="block text-sm font-bold text-gray-900 dark:text-white mb-2">Minecraft Gebruikersnaam</label>
                            <input type="text" 
                                   name="minecraft_username" 
                                   id="minecraft_username" 
                                   value="{{ old('minecraft_username', $user->minecraft_username) }}"
                                   class="w-full bg-gray-100/50 dark:bg-slate-800/50 border border-gray-200 dark:border-slate-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            @error('minecraft_username')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl font-bold shadow-lg hover:from-blue-600 hover:to-purple-700 transition-all duration-200 transform hover:scale-105">
                                <x-heroicon-s-check class="h-4 w-4"/>
                                Opslaan
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Current Stats -->
            <div class="glass-card rounded-2xl shadow-lg">
                <div class="p-6 border-b border-gray-200/50 dark:border-white/10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-heroicon-s-chart-bar class="h-6 w-6 text-white"/>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Huidige Statistieken</h3>
                            <p class="text-gray-600 dark:text-slate-400">Overzicht van spelerstatistieken</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="bg-gray-50/50 dark:bg-gray-700/30 rounded-xl p-4">
                            <dt class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Huidige baan</dt>
                            <dd class="text-lg font-bold text-gray-900 dark:text-white">{{ $user->prefix }}</dd>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-gray-700/30 rounded-xl p-4">
                            <dt class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Level</dt>
                            <dd class="text-lg font-bold text-gray-900 dark:text-white">{{ $user->level }}</dd>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-gray-700/30 rounded-xl p-4">
                            <dt class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Fitheid</dt>
                            <dd class="text-lg font-bold text-gray-900 dark:text-white">{{ $user->fitness }}</dd>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-gray-700/30 rounded-xl p-4">
                            <dt class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-1">Bank Saldo</dt>
                            <dd class="text-lg font-bold text-gray-900 dark:text-white">{{ $user->formatted_balance_with_currency }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-8">
            <!-- Role Assignment -->
            <div class="glass-card rounded-2xl shadow-lg">
                <div class="p-6 border-b border-gray-200/50 dark:border-white/10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-heroicon-s-key class="h-6 w-6 text-white"/>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Rollen Toewijzen</h3>
                            <p class="text-gray-600 dark:text-slate-400">Beheer gebruikersrollen</p>
                        </div>
                    </div>
                </div>
                <form action="{{ route('portal.admin.users.roles.update', $user) }}" method="POST" class="p-6">
                    @csrf

                    <div class="space-y-4">
                        @foreach($roles as $role)
                            <div class="relative flex items-start p-4 rounded-xl bg-gray-50/50 dark:bg-gray-700/30 hover:bg-gray-100/50 dark:hover:bg-gray-700/50 transition-all duration-200">
                                <div class="flex h-6 items-center">
                                    <input type="checkbox" 
                                           id="role_{{ $role->id }}" 
                                           name="roles[]" 
                                           value="{{ $role->id }}"
                                           {{ $user->roles->contains($role) ? 'checked' : '' }}
                                           class="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700">
                                </div>
                                <div class="ml-3 flex-1">
                                    <label for="role_{{ $role->id }}" class="text-sm font-bold text-gray-900 dark:text-white">{{ $role->name }}</label>
                                    @if($role->description)
                                        <p class="mt-1 text-xs text-gray-600 dark:text-slate-400">{{ $role->description }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-xl font-bold shadow-lg hover:from-orange-600 hover:to-red-700 transition-all duration-200 transform hover:scale-105">
                            <x-heroicon-s-check class="h-4 w-4"/>
                            Rollen Opslaan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Stats Overview -->
            <div class="glass-card rounded-2xl shadow-lg">
                <div class="p-6 border-b border-gray-200/50 dark:border-white/10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-heroicon-s-information-circle class="h-6 w-6 text-white"/>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Statistieken</h3>
                            <p class="text-gray-600 dark:text-slate-400">Account informatie</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <dl class="space-y-4">
                        <div class="bg-gray-50/50 dark:bg-gray-700/30 rounded-xl p-4">
                            <dt class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-2">Account Status</dt>
                            <dd>
                                <span @class([
                                    'inline-flex items-center rounded-xl px-3 py-1 text-xs font-bold',
                                    'bg-green-500/20 text-green-700 dark:text-green-300' => $user->is_online,
                                    'bg-gray-500/20 text-gray-700 dark:text-gray-300' => !$user->is_online,
                                ])>
                                    {{ $user->is_online ? 'Online' : 'Offline' }}
                                </span>
                            </dd>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-gray-700/30 rounded-xl p-4">
                            <dt class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-2">Speeltijd</dt>
                            <dd class="text-sm font-bold text-gray-900 dark:text-white">{{ $user->playtime }}</dd>
                        </div>
                        <div class="bg-gray-50/50 dark:bg-gray-700/30 rounded-xl p-4">
                            <dt class="text-sm font-medium text-gray-600 dark:text-slate-400 mb-2">Minecraft UUID</dt>
                            <dd class="text-xs font-mono text-gray-900 dark:text-white break-all">{{ $user->minecraft_uuid }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 