@extends('portal.layouts.v2.app')

@section('title', 'Rol details - ' . $role->name)

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="border-b border-white/10 pb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white">Rol details</h1>
                <p class="text-slate-400 mt-2">Bekijk de details van de rol "{{ $role->display_name ?? $role->name }}"</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('portal.admin.roles.edit', $role) }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    ✏️ Bewerken
                </a>
                <a href="{{ route('portal.admin.roles.index') }}" 
                   class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    ← Terug naar overzicht
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Role Info -->
        <div class="lg:col-span-1">
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">🔑</span>
                    Rol informatie
                </h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Naam</label>
                        <p class="text-white font-medium">{{ $role->name }}</p>
                    </div>

                    @if($role->display_name)
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Weergave naam</label>
                            <p class="text-white font-medium">{{ $role->display_name }}</p>
                        </div>
                    @endif

                    @if($role->description)
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Beschrijving</label>
                            <p class="text-slate-300">{{ $role->description }}</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Aangemaakt</label>
                        <p class="text-white font-medium">{{ $role->created_at->format('d-m-Y H:i') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Laatst bijgewerkt</label>
                        <p class="text-white font-medium">{{ $role->updated_at->format('d-m-Y H:i') }}</p>
                    </div>
                </div>

                <!-- Role Badges -->
                <div class="mt-6 pt-6 border-t border-white/10">
                    <div class="space-y-3">
                        @if($role->is_admin)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-500/20 text-red-300">
                                🛡️ Administrator
                            </span>
                        @endif

                        @if($role->is_default)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-500/20 text-blue-300">
                                ⭐ Standaard rol
                            </span>
                        @endif

                        @if(!$role->is_admin && !$role->is_default)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-slate-500/20 text-slate-300">
                                👤 Gebruikersrol
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8 mt-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">📊</span>
                    Statistieken
                </h2>

                <div class="space-y-4">
                    <div class="bg-slate-800/30 rounded-lg p-4 text-center">
                        <p class="text-slate-400 text-sm">Gebruikers met deze rol</p>
                        <p class="text-2xl font-bold text-white">{{ $role->users->count() }}</p>
                    </div>
                    <div class="bg-slate-800/30 rounded-lg p-4 text-center">
                        <p class="text-slate-400 text-sm">Toegewezen rechten</p>
                        <p class="text-2xl font-bold text-emerald-400">{{ $role->permissions->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Permissions -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">🛡️</span>
                    Rechten
                </h2>

                @if($role->permissions->count() > 0)
                    @php
                        $groupedPermissions = $role->permissions->groupBy(function($permission) {
                            return explode('-', $permission->name)[0] ?? 'general';
                        });
                    @endphp

                    <div class="space-y-6">
                        @foreach($groupedPermissions as $group => $groupPermissions)
                            <div class="bg-slate-800/30 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-white mb-4 capitalize">
                                    {{ ucfirst(str_replace('-', ' ', $group)) }} rechten
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($groupPermissions as $permission)
                                        <div class="flex items-center p-3 bg-emerald-500/10 border border-emerald-400/30 rounded-lg">
                                            <span class="text-emerald-400 mr-3">✓</span>
                                            <div>
                                                <div class="text-white font-medium">{{ $permission->display_name ?? $permission->name }}</div>
                                                @if($permission->description)
                                                    <div class="text-slate-400 text-sm">{{ $permission->description }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">🛡️</div>
                        <h3 class="text-xl font-semibold text-white mb-2">Geen rechten toegewezen</h3>
                        <p class="text-slate-400 mb-6">Deze rol heeft nog geen specifieke rechten.</p>
                        <a href="{{ route('portal.admin.roles.edit', $role) }}" 
                           class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-lg font-medium transition-colors inline-flex items-center gap-2">
                            <span class="text-lg">✏️</span>
                            Rechten toewijzen
                        </a>
                    </div>
                @endif
            </div>

            <!-- Users with this role -->
            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-8">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-3">
                    <span class="text-2xl">👥</span>
                    Gebruikers met deze rol
                </h2>

                @if($role->users->count() > 0)
                    <div class="space-y-3">
                        @foreach($role->users->take(10) as $user)
                            <div class="flex items-center justify-between p-4 bg-slate-800/30 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">{{ substr($user->name, 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-white font-medium">{{ $user->name }}</div>
                                        <div class="text-slate-400 text-sm">{{ $user->email }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-slate-400 text-sm">{{ $user->created_at->format('d-m-Y') }}</span>
                                    <a href="{{ route('portal.admin.users.show', $user) }}" 
                                       class="text-blue-400 hover:text-blue-300 transition-colors">
                                        <span class="text-lg">👁️</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach

                        @if($role->users->count() > 10)
                            <div class="text-center pt-4">
                                <p class="text-slate-400">En {{ $role->users->count() - 10 }} andere gebruikers...</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">👥</div>
                        <h3 class="text-xl font-semibold text-white mb-2">Geen gebruikers</h3>
                        <p class="text-slate-400">Er zijn nog geen gebruikers met deze rol.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 