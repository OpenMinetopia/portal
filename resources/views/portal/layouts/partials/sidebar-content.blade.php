@php
    $user = auth()->user();
    $heading = 'px-2 pb-1 pt-6 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400';
    // can() also checks the permission's feature, see AppServiceProvider.
    $can = fn (string ...$permissions) => collect($permissions)->contains(fn ($permission) => $user->can($permission));
@endphp

<a href="{{ route('dashboard') }}" class="omt-hop flex h-16 shrink-0 items-center gap-3 text-base font-bold tracking-tight text-gray-900 dark:text-white">
    <x-portal.logo />
    <span class="omt-heading truncate">{{ config('app.name') }}</span>
</a>

<nav class="flex flex-1 flex-col" aria-label="Portaal">
    <ul role="list" class="space-y-0.5">
        <x-portal.nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">Overzicht</x-portal.nav-link>
        @if ($can('use-bank'))
            <x-portal.nav-link :href="route('portal.bank-accounts.index')" :active="request()->routeIs('portal.bank-accounts.*')" icon="credit-card">Mijn bankrekeningen</x-portal.nav-link>
        @endif
        @if ($can('use-plots'))
            <x-portal.nav-link :href="route('portal.plots.index')" :active="request()->routeIs('portal.plots.index', 'portal.plots.show')" icon="map">Mijn plots</x-portal.nav-link>
        @endif
        @if ($can('view-criminal-record'))
            <x-portal.nav-link :href="route('portal.criminal-records.index')" :active="request()->routeIs('portal.criminal-records.*')" icon="document-text">Mijn strafblad</x-portal.nav-link>
        @endif
        @if ($can('use-broker'))
            <x-portal.nav-link :href="route('portal.plots.listings.index')" :active="request()->routeIs('portal.plots.listings.*', 'portal.plots.rentals.rent.*')" icon="currency-dollar">Makelaar</x-portal.nav-link>
        @endif
        {{-- A running rental can always be seen and ended, also with the makelaar off. --}}
        @if ($can('use-broker') || \App\Models\PlotRental::active()->where(fn ($query) => $query->where('renter_id', $user->id)->orWhere('landlord_id', $user->id))->exists())
            <x-portal.nav-link :href="route('portal.plots.rentals.index')" :active="request()->routeIs('portal.plots.rentals.index')" icon="key">Mijn huur</x-portal.nav-link>
        @endif
    </ul>

    @if ($can('request-permits', 'use-companies', 'view-company-registry'))
        <div class="{{ $heading }}">Bedrijven & vergunningen</div>
        <ul role="list" class="space-y-0.5">
            @if ($can('request-permits'))
                <x-portal.nav-link :href="route('portal.permits.index')" :active="request()->routeIs('portal.permits.index', 'portal.permits.request', 'portal.permits.show')" icon="document-check">Mijn vergunningen</x-portal.nav-link>
            @endif
            @if ($can('use-companies'))
                <x-portal.nav-link :href="route('portal.companies.index')" :active="request()->routeIs('portal.companies.index', 'portal.companies.request', 'portal.companies.show', 'portal.companies.register')" icon="building-office">Mijn bedrijven</x-portal.nav-link>
            @endif
            @if ($can('view-company-registry'))
                <x-portal.nav-link :href="route('portal.companies.registry')" :active="request()->routeIs('portal.companies.registry*')" icon="magnifying-glass">Bedrijvenregister</x-portal.nav-link>
            @endif
        </ul>
    @endif

    @if ($can('manage-companies', 'manage-company-types'))
        <div class="{{ $heading }}">Kamer van Koophandel</div>
        <ul role="list" class="space-y-0.5">
            @if ($can('manage-companies'))
                <x-portal.nav-link :href="route('portal.companies.manage.index')" :active="request()->routeIs('portal.companies.manage.*')" icon="inbox-stack">Bedrijfsaanvragen</x-portal.nav-link>
                <x-portal.nav-link :href="route('portal.companies.dissolutions.index')" :active="request()->routeIs('portal.companies.dissolutions.*')" icon="archive-box-x-mark">Opheffingsaanvragen</x-portal.nav-link>
            @endif
            @if ($can('manage-company-types'))
                <x-portal.nav-link :href="route('portal.admin.companies.types.index')" :active="request()->routeIs('portal.admin.companies.*')" icon="building-library">Type bedrijven</x-portal.nav-link>
            @endif
        </ul>
    @endif

    @if ($can('manage-permits', 'manage-permit-types'))
        <div class="{{ $heading }}">Vergunningen</div>
        <ul role="list" class="space-y-0.5">
            @if ($can('manage-permits'))
                <x-portal.nav-link :href="route('portal.permits.manage.index')" :active="request()->routeIs('portal.permits.manage.*')" icon="inbox-stack">Alle aanvragen</x-portal.nav-link>
            @endif
            @if ($can('manage-permit-types'))
                <x-portal.nav-link :href="route('portal.admin.permits.types.index')" :active="request()->routeIs('portal.admin.permits.types.*')" icon="building-library">Type vergunningen</x-portal.nav-link>
            @endif
        </ul>
    @endif

    @if ($can('manage-police'))
        <div class="{{ $heading }}">Politie</div>
        <ul role="list" class="space-y-0.5">
            <x-portal.nav-link :href="route('portal.police.players.index')" :active="request()->routeIs('portal.police.players.*')" icon="users">Spelersdatabase</x-portal.nav-link>
        </ul>
    @endif

    @if ($can('manage-users', 'manage-plots', 'manage-roles', 'manage-settings', 'manage-appearance'))
        <div class="{{ $heading }}">Beheer</div>
        <ul role="list" class="space-y-0.5">
            @if ($can('manage-users'))
                <x-portal.nav-link :href="route('portal.admin.users.index')" :active="request()->routeIs('portal.admin.users.*')" icon="users">Gebruikers</x-portal.nav-link>
            @endif
            @if ($can('manage-plots'))
                <x-portal.nav-link :href="route('portal.admin.plots.index')" :active="request()->routeIs('portal.admin.plots.*')" icon="map">Plots</x-portal.nav-link>
            @endif
            @if ($can('manage-roles'))
                <x-portal.nav-link :href="route('portal.admin.roles.index')" :active="request()->routeIs('portal.admin.roles.*')" icon="key">Rollen</x-portal.nav-link>
            @endif
            @if ($can('manage-settings'))
                <x-portal.nav-link :href="route('portal.admin.settings.index')" :active="request()->routeIs('portal.admin.settings.*')" icon="cog-6-tooth">Instellingen</x-portal.nav-link>
            @endif
            @if ($can('manage-appearance'))
                <x-portal.nav-link :href="route('portal.admin.branding.edit')" :active="request()->routeIs('portal.admin.branding.*')" icon="paint-brush">Uiterlijk & logo</x-portal.nav-link>
            @endif
        </ul>
    @endif
</nav>
