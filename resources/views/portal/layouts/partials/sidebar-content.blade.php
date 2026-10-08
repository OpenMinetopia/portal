@php
    $features = \App\Models\PortalFeature::where('is_enabled', true)->pluck('key');
    $user = auth()->user();
    $heading = 'px-2 pb-1 pt-6 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400';
@endphp

<a href="{{ route('dashboard') }}" class="omt-hop flex h-16 shrink-0 items-center gap-3 text-base font-bold tracking-tight text-gray-900 dark:text-white">
    <img src="{{ asset('images/brand/logo.svg') }}" alt="" class="omt-pixel h-7 w-7 animate-omt-land">
    <span class="truncate">{{ config('app.name') }}</span>
</a>

<nav class="flex flex-1 flex-col" aria-label="Portaal">
    <ul role="list" class="space-y-0.5">
        <x-portal.nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">Overzicht</x-portal.nav-link>
        <x-portal.nav-link :href="route('portal.bank-accounts.index')" :active="request()->routeIs('portal.bank-accounts.*')" icon="credit-card">Mijn bankrekeningen</x-portal.nav-link>
        <x-portal.nav-link :href="route('portal.plots.index')" :active="request()->routeIs('portal.plots.index', 'portal.plots.show')" icon="map">Mijn plots</x-portal.nav-link>
        <x-portal.nav-link :href="route('portal.criminal-records.index')" :active="request()->routeIs('portal.criminal-records.*')" icon="document-text">Mijn strafblad</x-portal.nav-link>
        @if ($features->contains('broker'))
            <x-portal.nav-link :href="route('portal.plots.listings.index')" :active="request()->routeIs('portal.plots.listings.*')" icon="currency-dollar">Makelaar</x-portal.nav-link>
        @endif
    </ul>

    @if ($features->contains('permits') || $features->contains('companies'))
        <div class="{{ $heading }}">Bedrijven & vergunningen</div>
        <ul role="list" class="space-y-0.5">
            @if ($features->contains('permits'))
                <x-portal.nav-link :href="route('portal.permits.index')" :active="request()->routeIs('portal.permits.index', 'portal.permits.request', 'portal.permits.show')" icon="document-check">Mijn vergunningen</x-portal.nav-link>
            @endif
            @if ($features->contains('companies'))
                <x-portal.nav-link :href="route('portal.companies.index')" :active="request()->routeIs('portal.companies.index', 'portal.companies.request', 'portal.companies.show', 'portal.companies.register')" icon="building-office">Mijn bedrijven</x-portal.nav-link>
                <x-portal.nav-link :href="route('portal.companies.registry')" :active="request()->routeIs('portal.companies.registry')" icon="magnifying-glass">Bedrijvenregister</x-portal.nav-link>
            @endif
        </ul>
    @endif

    @if (($user->isAdmin() || $user->hasPermission('manage-companies')) && $features->contains('companies'))
        <div class="{{ $heading }}">Kamer van Koophandel</div>
        <ul role="list" class="space-y-0.5">
            <x-portal.nav-link :href="route('portal.companies.requests.index')" :active="request()->routeIs('portal.companies.requests.*')" icon="inbox-stack">Bedrijfsaanvragen</x-portal.nav-link>
            <x-portal.nav-link :href="route('portal.companies.dissolutions.index')" :active="request()->routeIs('portal.companies.dissolutions.*')" icon="archive-box-x-mark">Opheffingsaanvragen</x-portal.nav-link>
            @if ($user->isAdmin())
                <x-portal.nav-link :href="route('portal.admin.companies.types.index')" :active="request()->routeIs('portal.admin.companies.*')" icon="building-library">Type bedrijven</x-portal.nav-link>
            @endif
        </ul>
    @endif

    @if (($user->isAdmin() || $user->hasPermission('manage-permits')) && $features->contains('permits'))
        <div class="{{ $heading }}">Vergunningen</div>
        <ul role="list" class="space-y-0.5">
            <x-portal.nav-link :href="route('portal.permits.manage.index')" :active="request()->routeIs('portal.permits.manage.*')" icon="inbox-stack">Alle aanvragen</x-portal.nav-link>
            @if ($user->isAdmin())
                <x-portal.nav-link :href="route('portal.admin.permits.types.index')" :active="request()->routeIs('portal.admin.permits.types.*')" icon="building-library">Type vergunningen</x-portal.nav-link>
            @endif
        </ul>
    @endif

    @if ($user->isAdmin() || $user->hasPermission('manage-police'))
        <div class="{{ $heading }}">Politie</div>
        <ul role="list" class="space-y-0.5">
            <x-portal.nav-link :href="route('portal.police.players.index')" :active="request()->routeIs('portal.police.players.*')" icon="users">Spelersdatabase</x-portal.nav-link>
        </ul>
    @endif

    @if ($user->isAdmin())
        <div class="{{ $heading }}">Beheer</div>
        <ul role="list" class="space-y-0.5">
            <x-portal.nav-link :href="route('portal.admin.users.index')" :active="request()->routeIs('portal.admin.users.*')" icon="users">Gebruikers</x-portal.nav-link>
            <x-portal.nav-link :href="route('portal.admin.plots.index')" :active="request()->routeIs('portal.admin.plots.*')" icon="map">Plots</x-portal.nav-link>
            <x-portal.nav-link :href="route('portal.admin.roles.index')" :active="request()->routeIs('portal.admin.roles.*')" icon="key">Rollen</x-portal.nav-link>
            <x-portal.nav-link :href="route('portal.admin.settings.index')" :active="request()->routeIs('portal.admin.settings.*')" icon="cog-6-tooth">Instellingen</x-portal.nav-link>
        </ul>
    @endif
</nav>
