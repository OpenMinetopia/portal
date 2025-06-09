    <div class="flex grow flex-col gap-y-5">
        <!-- Logo -->
        <div class="flex h-20 shrink-0 items-center justify-center pt-6">
            <div class="text-center">
                <div class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white mb-1">
                    <span class="bg-gradient-to-r from-emerald-400 to-blue-400 bg-clip-text text-transparent">Mine</span><span class="text-gray-900 dark:text-white">topia</span>
                </div>
                <div class="text-xs font-medium text-gray-600 dark:text-slate-400 uppercase tracking-widest">Portal V2</div>
            </div>
        </div>

        <!-- User Info Card -->
        <div class="glass-card glass-card-hover rounded-xl p-4 mb-2 transition-all duration-300">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0 relative">
                    <img src="https://crafatar.com/avatars/{{ auth()->user()->minecraft_uuid }}?overlay=true&size=96"
                         alt="{{ auth()->user()->minecraft_username }}"
                         class="w-12 h-12 rounded-xl shadow-lg">
                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-white dark:border-slate-800 animate-pulse"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate mb-1">{{ auth()->user()->name }}</p>
                    <div class="flex items-center space-x-1">
                        <span class="text-xs text-emerald-700 dark:text-emerald-300 font-medium">{{ auth()->user()->formatted_balance_with_currency }}</span>
                        <span class="text-xs text-gray-400 dark:text-slate-500">•</span>
                        <span class="text-xs text-gray-600 dark:text-slate-400">Online</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex flex-1 flex-col">
            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                <!-- Main Navigation -->
                <li>
                    <ul role="list" class="-mx-2 space-y-3">
                        <li>
                            <a href="{{ route('dashboard') }}"
                            @class([
                                'nav-item group flex items-center gap-x-3 rounded-xl p-4 text-sm font-medium leading-6',
                                'active text-emerald-700 dark:text-emerald-300' => request()->routeIs('dashboard'),
                                'text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white' => !request()->routeIs('dashboard'),
                            ])>
                                <span class="text-lg flex-shrink-0">🏠</span>
                                <span class="flex-1">Dashboard</span>
                                @if(request()->routeIs('dashboard'))
                                    <div class="flex-shrink-0">
                                        <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                    </div>
                                @endif
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('portal.bank-accounts.index') }}"
                            @class([
                                'nav-item group flex items-center gap-x-3 rounded-xl p-4 text-sm font-medium leading-6',
                                'active text-emerald-700 dark:text-emerald-300' => request()->routeIs('portal.bank-accounts.*'),
                                'text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white' => !request()->routeIs('portal.bank-accounts.*'),
                            ])>
                                <span class="text-lg flex-shrink-0">💰</span>
                                <span class="flex-1">Mijn bankrekeningen</span>
                                @if(request()->routeIs('portal.bank-accounts.*'))
                                    <div class="flex-shrink-0">
                                        <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                    </div>
                                @endif
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('portal.plots.index') }}"
                            @class([
                                'nav-item group flex items-center gap-x-3 rounded-xl p-4 text-sm font-medium leading-6',
                                'active text-emerald-700 dark:text-emerald-300' => request()->routeIs('portal.plots.*'),
                                'text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white' => !request()->routeIs('portal.plots.*'),
                            ])>
                                <span class="text-lg flex-shrink-0">🗺️</span>
                                <span class="flex-1">Mijn plots</span>
                                @if(request()->routeIs('portal.plots.*'))
                                    <div class="flex-shrink-0">
                                        <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                    </div>
                                @endif
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('portal.criminal-records.index') }}"
                            @class([
                                'nav-item group flex items-center gap-x-3 rounded-xl p-4 text-sm font-medium leading-6',
                                'active text-emerald-700 dark:text-emerald-300' => request()->routeIs('portal.criminal-records.*'),
                                'text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white' => !request()->routeIs('portal.criminal-records.*'),
                            ])>
                                <span class="text-lg flex-shrink-0">📋</span>
                                <span class="flex-1">Mijn strafblad</span>
                                @if(request()->routeIs('portal.criminal-records.*'))
                                    <div class="flex-shrink-0">
                                        <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                    </div>
                                @endif
                            </a>
                        </li>

                        @if(\App\Models\PortalFeature::where('key', 'broker')->where('is_enabled', true)->exists())
                            <li>
                                <a href="{{ route('portal.plots.listings.index') }}"
                                @class([
                                    'nav-item group flex items-center gap-x-3 rounded-xl p-4 text-sm font-medium leading-6',
                                    'active text-emerald-700 dark:text-emerald-300' => request()->routeIs('portal.plots.listings.*'),
                                    'text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white' => !request()->routeIs('portal.plots.listings.*'),
                                ])>
                                    <span class="text-lg flex-shrink-0">🏪</span>
                                    <span class="flex-1">Makelaar</span>
                                    @if(request()->routeIs('portal.plots.listings.*'))
                                        <div class="flex-shrink-0">
                                            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                        </div>
                                    @endif
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
                        <div class="text-xs font-semibold leading-6 text-gray-600 dark:text-slate-400 uppercase tracking-widest mb-3 px-2">🏢 Bedrijven & Vergunningen</div>
                        <ul role="list" class="-mx-2 space-y-3">
                            @if(\App\Models\PortalFeature::where('key', 'permits')->where('is_enabled', true)->exists())
                                <li>
                                    <a href="{{ route('portal.permits.index') }}"
                                    @class([
                                        'nav-item group flex items-center gap-x-3 rounded-xl p-4 text-sm font-medium leading-6',
                                        'active text-emerald-700 dark:text-emerald-300' => request()->routeIs('portal.permits.index', 'portal.permits.request', 'portal.permits.show'),
                                        'text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white' => !request()->routeIs('portal.permits.index', 'portal.permits.request', 'portal.permits.show'),
                                    ])>
                                        <span class="text-lg flex-shrink-0">📄</span>
                                        <span class="flex-1">Mijn vergunningen</span>
                                        @if(request()->routeIs('portal.permits.index', 'portal.permits.request', 'portal.permits.show'))
                                            <div class="flex-shrink-0">
                                                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                            </div>
                                        @endif
                                    </a>
                                </li>
                            @endif

                            @if(\App\Models\PortalFeature::where('key', 'companies')->where('is_enabled', true)->exists())
                                <li>
                                    <a href="{{ route('portal.companies.index') }}"
                                    @class([
                                        'nav-item group flex items-center gap-x-3 rounded-xl p-4 text-sm font-medium leading-6',
                                        'active text-emerald-700 dark:text-emerald-300' => request()->routeIs('portal.companies.index', 'portal.companies.request', 'portal.companies.show', 'portal.companies.register'),
                                        'text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white' => !request()->routeIs('portal.companies.index', 'portal.companies.request', 'portal.companies.show', 'portal.companies.register'),
                                    ])>
                                        <span class="text-lg flex-shrink-0">🏢</span>
                                        <span class="flex-1">Mijn bedrijven</span>
                                        @if(request()->routeIs('portal.companies.index', 'portal.companies.request', 'portal.companies.show', 'portal.companies.register'))
                                            <div class="flex-shrink-0">
                                                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                            </div>
                                        @endif
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('portal.companies.registry') }}"
                                    @class([
                                        'nav-item group flex items-center gap-x-3 rounded-xl p-4 text-sm font-medium leading-6',
                                        'active text-emerald-700 dark:text-emerald-300' => request()->routeIs('portal.companies.registry'),
                                        'text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white' => !request()->routeIs('portal.companies.registry'),
                                    ])>
                                        <span class="text-lg flex-shrink-0">🔍</span>
                                        <span class="flex-1">Bedrijvenregister</span>
                                        @if(request()->routeIs('portal.companies.registry'))
                                            <div class="flex-shrink-0">
                                                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                            </div>
                                        @endif
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
                        <div class="text-xs font-semibold leading-6 text-gray-600 dark:text-slate-400 uppercase tracking-widest mb-3 px-2">🏛️ Kamer van Koophandel</div>
                        <ul role="list" class="-mx-2 space-y-3">
                            <li>
                                <a href="{{ route('portal.companies.requests.index') }}"
                                @class([
                                    'nav-item group flex items-center gap-x-3 rounded-xl p-4 text-sm font-medium leading-6',
                                    'active text-emerald-700 dark:text-emerald-300' => request()->routeIs('portal.companies.requests.*'),
                                    'text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white' => !request()->routeIs('portal.companies.requests.*'),
                                ])>
                                    <span class="text-lg flex-shrink-0">📋</span>
                                    <span class="flex-1">Bedrijfsaanvragen</span>
                                    @if(request()->routeIs('portal.companies.requests.*'))
                                        <div class="flex-shrink-0">
                                            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                        </div>
                                    @endif
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('portal.companies.dissolutions.index') }}"
                                @class([
                                    'nav-item group flex items-center gap-x-3 rounded-xl p-4 text-sm font-medium leading-6',
                                    'active text-emerald-700 dark:text-emerald-300' => request()->routeIs('portal.companies.dissolutions.*'),
                                    'text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white' => !request()->routeIs('portal.companies.dissolutions.*'),
                                ])>
                                    <span class="text-lg flex-shrink-0">📦</span>
                                    <span class="flex-1">Opheffingsaanvragen</span>
                                    @if(request()->routeIs('portal.companies.dissolutions.*'))
                                        <div class="flex-shrink-0">
                                            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                        </div>
                                    @endif
                                </a>
                            </li>

                            @if(auth()->user()->isAdmin())
                                <li>
                                    <a href="{{ route('portal.admin.companies.types.index') }}"
                                    @class([
                                        'nav-item group flex items-center gap-x-3 rounded-xl p-4 text-sm font-medium leading-6',
                                        'active text-emerald-700 dark:text-emerald-300' => request()->routeIs('portal.admin.companies.*'),
                                        'text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white' => !request()->routeIs('portal.admin.companies.*'),
                                    ])>
                                        <span class="text-lg flex-shrink-0">🏛️</span>
                                        <span class="flex-1">Type bedrijven</span>
                                        @if(request()->routeIs('portal.admin.companies.*'))
                                            <div class="flex-shrink-0">
                                                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                            </div>
                                        @endif
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
                        <div class="text-xs font-semibold leading-6 text-gray-600 dark:text-slate-400 uppercase tracking-widest mb-3 px-2">📋 Vergunningen</div>
                        <ul role="list" class="-mx-2 space-y-3">
                            <li>
                                <a href="{{ route('portal.permits.manage.index') }}"
                                @class([
                                    'nav-item group flex items-center gap-x-3 rounded-xl p-4 text-sm font-medium leading-6',
                                    'active text-emerald-700 dark:text-emerald-300' => request()->routeIs('portal.permits.manage.*'),
                                    'text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white' => !request()->routeIs('portal.permits.manage.*'),
                                ])>
                                    <span class="text-lg flex-shrink-0">📥</span>
                                    <span class="flex-1">Alle aanvragen</span>
                                    @if(request()->routeIs('portal.permits.manage.*'))
                                        <div class="flex-shrink-0">
                                            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                        </div>
                                    @endif
                                </a>
                            </li>

                            @if(auth()->user()->isAdmin())
                                <li>
                                    <a href="{{ route('portal.admin.permits.types.index') }}"
                                    @class([
                                        'nav-item group flex items-center gap-x-3 rounded-xl p-4 text-sm font-medium leading-6',
                                        'active text-emerald-700 dark:text-emerald-300' => request()->routeIs('portal.admin.permits.types.*'),
                                        'text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white' => !request()->routeIs('portal.admin.permits.types.*'),
                                    ])>
                                        <span class="text-lg flex-shrink-0">🏛️</span>
                                        <span class="flex-1">Type vergunningen</span>
                                        @if(request()->routeIs('portal.admin.permits.types.*'))
                                            <div class="flex-shrink-0">
                                                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                            </div>
                                        @endif
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('manage-police'))
                    <li>
                        <div class="text-xs font-semibold leading-6 text-gray-600 dark:text-slate-400 uppercase tracking-widest mb-3 px-2">👮 Politie</div>
                        <ul role="list" class="-mx-2 space-y-3">
                            <li>
                                <a href="{{ route('portal.police.players.index') }}"
                                @class([
                                    'nav-item group flex items-center gap-x-3 rounded-xl p-4 text-sm font-medium leading-6',
                                    'active text-emerald-700 dark:text-emerald-300' => request()->routeIs('portal.police.players.*'),
                                    'text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white' => !request()->routeIs('portal.police.players.*'),
                                ])>
                                    <span class="text-lg flex-shrink-0">👥</span>
                                    <span class="flex-1">Spelersdatabase</span>
                                    @if(request()->routeIs('portal.police.players.*'))
                                        <div class="flex-shrink-0">
                                            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                        </div>
                                    @endif
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if(auth()->user()->isAdmin())
                    <li>
                        <div class="text-xs font-semibold leading-6 text-gray-600 dark:text-slate-400 uppercase tracking-widest mb-3 px-2">⚙️ Beheer</div>
                        <ul role="list" class="-mx-2 space-y-3">
                            <li>
                                <a href="{{ route('portal.admin.users.index') }}"
                                @class([
                                    'nav-item group flex items-center gap-x-3 rounded-xl p-4 text-sm font-medium leading-6',
                                    'active text-emerald-700 dark:text-emerald-300' => request()->routeIs('portal.admin.users.*'),
                                    'text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white' => !request()->routeIs('portal.admin.users.*'),
                                ])>
                                    <span class="text-lg flex-shrink-0">👥</span>
                                    <span class="flex-1">Gebruikers</span>
                                    @if(request()->routeIs('portal.admin.users.*'))
                                        <div class="flex-shrink-0">
                                            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                        </div>
                                    @endif
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('portal.admin.plots.index') }}"
                                @class([
                                    'nav-item group flex items-center gap-x-3 rounded-xl p-4 text-sm font-medium leading-6',
                                    'active text-emerald-700 dark:text-emerald-300' => request()->routeIs('portal.admin.plots.*'),
                                    'text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white' => !request()->routeIs('portal.admin.plots.*'),
                                ])>
                                    <span class="text-lg flex-shrink-0">🗺️</span>
                                    <span class="flex-1">Plots</span>
                                    @if(request()->routeIs('portal.admin.plots.*'))
                                        <div class="flex-shrink-0">
                                            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                        </div>
                                    @endif
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('portal.admin.roles.index') }}"
                                @class([
                                    'nav-item group flex items-center gap-x-3 rounded-xl p-4 text-sm font-medium leading-6',
                                    'active text-emerald-700 dark:text-emerald-300' => request()->routeIs('portal.admin.roles.*'),
                                    'text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white' => !request()->routeIs('portal.admin.roles.*'),
                                ])>
                                    <span class="text-lg flex-shrink-0">🔑</span>
                                    <span class="flex-1">Rollen</span>
                                    @if(request()->routeIs('portal.admin.roles.*'))
                                        <div class="flex-shrink-0">
                                            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                        </div>
                                    @endif
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('portal.admin.settings.index') }}"
                                @class([
                                    'nav-item group flex items-center gap-x-3 rounded-xl p-4 text-sm font-medium leading-6',
                                    'active text-emerald-700 dark:text-emerald-300' => request()->routeIs('portal.admin.settings.*'),
                                    'text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white' => !request()->routeIs('portal.admin.settings.*'),
                                ])>
                                    <span class="text-lg flex-shrink-0">⚙️</span>
                                    <span class="flex-1">Instellingen</span>
                                    @if(request()->routeIs('portal.admin.settings.*'))
                                        <div class="flex-shrink-0">
                                            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                        </div>
                                    @endif
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </nav>
    </div> 