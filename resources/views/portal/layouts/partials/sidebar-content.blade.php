<div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4">
    <!-- Logo -->
    <div class="flex h-16 shrink-0 items-center">
        <span class="text-xl font-bold tracking-tight text-white">
            <span class="bg-gradient-to-r from-indigo-400 to-indigo-500 bg-clip-text text-transparent">Open</span>Minetopia
        </span>
    </div>

    <!-- Navigation -->
    <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <!-- Main Navigation -->
            <li>
                <ul role="list" class="-mx-2 space-y-1">
                    <li>
                        <a href="{{ route('dashboard') }}"
                           @class([
                               'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold',
                               'bg-gray-800 text-white' => request()->routeIs('dashboard'),
                               'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('dashboard'),
                           ])>
                            <x-heroicon-o-home class="size-6 shrink-0" />
                            Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('portal.bank-accounts.index') }}"
                           @class([
                               'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold',
                               'bg-gray-800 text-white' => request()->routeIs('portal.bank-accounts.*'),
                               'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('portal.bank-accounts.*'),
                           ])>
                            <x-heroicon-o-credit-card class="size-6 shrink-0" />
                            Mijn bankrekeningen
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('portal.plots.index') }}"
                           @class([
                               'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold',
                               'bg-gray-800 text-white' => request()->routeIs('portal.plots.*'),
                               'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('portal.plots.*'),
                           ])>
                            <x-heroicon-o-map class="size-6 shrink-0" />
                            Mijn plots
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('portal.criminal-records.index') }}"
                           @class([
                               'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold',
                               'bg-gray-800 text-white' => request()->routeIs('portal.criminal-records.*'),
                               'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('portal.criminal-records.*'),
                           ])>
                            <x-heroicon-o-document-text class="size-6 shrink-0" />
                            Mijn strafblad
                        </a>
                    </li>

                    @if(\App\Models\PortalFeature::where('key', 'broker')->where('is_enabled', true)->exists())
                        <li>
                            <a href="{{ route('portal.plots.listings.index') }}"
                               @class([
                                   'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold',
                                   'bg-gray-800 text-white' => request()->routeIs('portal.plots.listings.*'),
                                   'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('portal.plots.listings.*'),
                               ])>
                                <x-heroicon-o-currency-dollar class="size-6 shrink-0" />
                                Makelaar
                            </a>
                        </li>
                    @endif
                </ul>
            </li>

            @if(
                \App\Models\PortalFeature::where('key', 'permits')->where('is_enabled', true)->exists() ||
                \App\Models\PortalFeature::where('key', 'companies')->where('is_enabled', true)->exists()
            )
                <li>
                    <div class="text-xs/6 font-semibold text-gray-400">Bedrijven & Vergunningen</div>
                    <ul role="list" class="-mx-2 mt-2 space-y-1">
                        @if(\App\Models\PortalFeature::where('key', 'permits')->where('is_enabled', true)->exists())
                            <li>
                                <a href="{{ route('portal.permits.index') }}"
                                   @class([
                                       'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold',
                                       'bg-gray-800 text-white' => request()->routeIs('portal.permits.index', 'portal.permits.request', 'portal.permits.show'),
                                       'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('portal.permits.index', 'portal.permits.request', 'portal.permits.show'),
                                   ])>
                                    <x-heroicon-o-document-text class="size-6 shrink-0" />
                                    Mijn vergunningen
                                </a>
                            </li>
                        @endif

                        @if(\App\Models\PortalFeature::where('key', 'companies')->where('is_enabled', true)->exists())
                            <li>
                                <a href="{{ route('portal.companies.index') }}"
                                   @class([
                                       'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold',
                                       'bg-gray-800 text-white' => request()->routeIs('portal.companies.index', 'portal.companies.request', 'portal.companies.show', 'portal.companies.register'),
                                       'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('portal.companies.index', 'portal.companies.request', 'portal.companies.show', 'portal.companies.register'),
                                   ])>
                                    <x-heroicon-o-building-office class="size-6 shrink-0" />
                                    Mijn bedrijven
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('portal.companies.registry') }}"
                                   @class([
                                       'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold',
                                       'bg-gray-800 text-white' => request()->routeIs('portal.companies.registry'),
                                       'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('portal.companies.registry'),
                                   ])>
                                    <x-heroicon-o-magnifying-glass class="size-6 shrink-0" />
                                    Bedrijvenregister
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if(
                (auth()->user()->isAdmin() || auth()->user()->hasPermission('manage-companies'))
                && App\Models\PortalFeature::where('key', 'companies')->where('is_enabled', true)->exists()
            )
                <li>
                    <div class="text-xs/6 font-semibold text-gray-400">Kamer van Koophandel</div>
                    <ul role="list" class="-mx-2 mt-2 space-y-1">
                        <li>
                            <a href="{{ route('portal.companies.requests.index') }}"
                               @class([
                                   'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold',
                                   'bg-gray-800 text-white' => request()->routeIs('portal.companies.requests.*'),
                                   'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('portal.companies.requests.*'),
                               ])>
                                <x-heroicon-o-inbox-stack class="size-6 shrink-0" />
                                Bedrijfsaanvragen
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('portal.companies.dissolutions.index') }}"
                               @class([
                                   'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold',
                                   'bg-gray-800 text-white' => request()->routeIs('portal.companies.dissolutions.*'),
                                   'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('portal.companies.dissolutions.*'),
                               ])>
                                <x-heroicon-o-archive-box-x-mark class="size-6 shrink-0" />
                                Opheffingsaanvragen
                            </a>
                        </li>

                        @if(auth()->user()->isAdmin())
                            <li>
                                <a href="{{ route('portal.admin.companies.types.index') }}"
                                   @class([
                                       'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold',
                                       'bg-gray-800 text-white' => request()->routeIs('portal.admin.companies.*'),
                                       'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('portal.admin.companies.*'),
                                   ])>
                                    <x-heroicon-o-building-library class="size-6 shrink-0" />
                                    Type bedrijven
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if(
                (auth()->user()->isAdmin() || auth()->user()->hasPermission('manage-permits'))
                && App\Models\PortalFeature::where('key', 'permits')->where('is_enabled', true)->exists()
            )
                <li>
                    <div class="text-xs/6 font-semibold text-gray-400">Vergunningen</div>
                    <ul role="list" class="-mx-2 mt-2 space-y-1">
                        <li>
                            <a href="{{ route('portal.permits.manage.index') }}"
                               @class([
                                   'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold',
                                   'bg-gray-800 text-white' => request()->routeIs('portal.permits.manage.*'),
                                   'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('portal.permits.manage.*'),
                               ])>
                                <x-heroicon-o-inbox-stack class="size-6 shrink-0" />
                                Alle aanvragen
                            </a>
                        </li>

                        @if(auth()->user()->isAdmin())
                            <li>
                                <a href="{{ route('portal.admin.permits.types.index') }}"
                                   @class([
                                       'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold',
                                       'bg-gray-800 text-white' => request()->routeIs('portal.admin.permits.types.*'),
                                       'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('portal.admin.permits.types.*'),
                                   ])>
                                    <x-heroicon-o-building-library class="size-6 shrink-0" />
                                    Type vergunningen
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('manage-police'))
                <li>
                    <div class="text-xs/6 font-semibold text-gray-400">Politie</div>
                    <ul role="list" class="-mx-2 mt-2 space-y-1">
                        <li>
                            <a href="{{ route('portal.police.players.index') }}"
                               @class([
                                   'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold',
                                   'bg-gray-800 text-white' => request()->routeIs('portal.police.players.*'),
                                   'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('portal.police.players.*'),
                               ])>
                                <x-heroicon-o-users class="size-6 shrink-0" />
                                Spelersdatabase
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if(auth()->user()->isAdmin())
                <li>
                    <div class="text-xs/6 font-semibold text-gray-400">Beheer</div>
                    <ul role="list" class="-mx-2 mt-2 space-y-1">
                        <li>
                            <a href="{{ route('portal.admin.users.index') }}"
                               @class([
                                   'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold',
                                   'bg-gray-800 text-white' => request()->routeIs('portal.admin.users.*'),
                                   'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('portal.admin.users.*'),
                               ])>
                                <x-heroicon-o-users class="size-6 shrink-0" />
                                Gebruikers
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('portal.admin.plots.index') }}"
                               @class([
                                   'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold',
                                   'bg-gray-800 text-white' => request()->routeIs('portal.admin.plots.*'),
                                   'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('portal.admin.plots.*'),
                               ])>
                                <x-heroicon-o-map class="size-6 shrink-0" />
                                Plots
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('portal.admin.roles.index') }}"
                               @class([
                                   'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold',
                                   'bg-gray-800 text-white' => request()->routeIs('portal.admin.roles.*'),
                                   'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('portal.admin.roles.*'),
                               ])>
                                <x-heroicon-o-key class="size-6 shrink-0" />
                                Rollen
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('portal.admin.settings.index') }}"
                               @class([
                                   'group flex gap-x-3 rounded-md p-2 text-sm/6 font-semibold',
                                   'bg-gray-800 text-white' => request()->routeIs('portal.admin.settings.*'),
                                   'text-gray-400 hover:text-white hover:bg-gray-800' => !request()->routeIs('portal.admin.settings.*'),
                               ])>
                                <x-heroicon-o-cog-6-tooth class="size-6 shrink-0" />
                                Instellingen
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>
    </nav>
</div>
