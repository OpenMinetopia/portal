<!-- Logo -->
<div class="flex h-16 shrink-0 items-center">
    <span class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">
        <span
            class="bg-gradient-to-r from-indigo-500 to-indigo-600 dark:from-indigo-400 dark:to-indigo-500 bg-clip-text text-transparent">Open</span>Minetopia
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
                       class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('dashboard')
                            ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-indigo-600 dark:hover:text-indigo-400'
                        }}">
                        <x-heroicon-s-home class="h-6 w-6 shrink-0"/>
                        Dashboard
                    </a>
                </li>

                <li>
                    <a href="#"
                       class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('portal.plots.*')
                            ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-indigo-600 dark:hover:text-indigo-400'
                        }}">
                        <x-heroicon-s-building-office-2 class="h-6 w-6 shrink-0"/>
                        Plots
                    </a>
                </li>

                <li>
                    <a href="#"
                       class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('portal.bank.*')
                            ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-indigo-600 dark:hover:text-indigo-400'
                        }}">
                        <x-heroicon-s-banknotes class="h-6 w-6 shrink-0"/>
                        Bank
                    </a>
                </li>

                @if(\App\Models\PortalFeature::where('key', 'permits')->where('is_enabled', true)->exists())
                    <li>
                        <a href="{{ route('portal.permits.index') }}"
                           class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('portal.permits.index', 'portal.permits.request', 'portal.permits.show')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-indigo-600 dark:hover:text-indigo-400'
                            }}">
                            <x-heroicon-s-document-text class="h-6 w-6 shrink-0"/>
                            Vergunningen
                        </a>
                    </li>

                    @if(auth()->user()->isAdmin() || auth()->user()->roles->pluck('id')->intersect(App\Models\PermitType::pluck('authorized_roles')->flatten()->unique())->isNotEmpty())
                        <li>
                            <div class="text-xs font-semibold leading-6 text-gray-500 dark:text-gray-400">VERGUNNINGEN</div>
                            <ul role="list" class="-mx-2 mt-2 space-y-1">
                                <li>
                                    <a href="{{ route('portal.permits.manage.index') }}"
                                       class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('portal.permits.manage.*')
                                            ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
                                            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-indigo-600 dark:hover:text-indigo-400'
                                        }}">
                                        <x-heroicon-s-inbox-stack class="h-6 w-6 shrink-0"/>
                                        Aanvragen Beheren
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    @if(auth()->user()->isAdmin())
                        <!-- Admin Permits Menu Item -->
                        <li>
                            <a href="{{ route('portal.admin.permits.types.index') }}"
                               class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('portal.admin.permits.*')
                                    ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-indigo-600 dark:hover:text-indigo-400'
                                }}">
                                <x-heroicon-s-document-duplicate class="h-6 w-6 shrink-0"/>
                                Vergunningen
                            </a>
                        </li>
                    @endif
                @endif

                @if(\App\Models\PortalFeature::where('key', 'companies')->where('is_enabled', true)->exists())
                    <li>
                        <a href="{{ route('portal.companies.index') }}"
                           class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('portal.companies.index', 'portal.companies.request', 'portal.companies.show', 'portal.companies.register')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-indigo-600 dark:hover:text-indigo-400'
                            }}">
                            <x-heroicon-s-building-office class="h-6 w-6 shrink-0"/>
                            Bedrijven
                        </a>
                    </li>
                @endif
            </ul>
        </li>

        @if(auth()->user()->isAdmin() || auth()->user()->roles->contains('id', 3))
            <!-- Police Section -->
            <li>
                <div class="text-xs font-semibold leading-6 text-gray-500 dark:text-gray-400">POLITIE</div>
                <ul role="list" class="-mx-2 mt-2 space-y-1">
                    <li>
                        <a href="#"
                           class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('portal.emergency-calls.*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-indigo-600 dark:hover:text-indigo-400'
                            }}">
                            <x-heroicon-s-phone class="h-6 w-6 shrink-0"/>
                            Noodoproepen
                        </a>
                    </li>

                    <li>
                        <a href="#"
                           class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('portal.arrests.*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-indigo-600 dark:hover:text-indigo-400'
                            }}">
                            <x-heroicon-s-lock-closed class="h-6 w-6 shrink-0"/>
                            Arrestaties
                        </a>
                    </li>

                    <li>
                        <a href="#"
                           class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('portal.detection-gates.*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-indigo-600 dark:hover:text-indigo-400'
                            }}">
                            <x-heroicon-s-shield-check class="h-6 w-6 shrink-0"/>
                            Detectiepoorten
                        </a>
                    </li>

                    <li>
                        <a href="#"
                           class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('portal.fines.*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-indigo-600 dark:hover:text-indigo-400'
                            }}">
                            <x-heroicon-s-document-text class="h-6 w-6 shrink-0"/>
                            Boetes
                        </a>
                    </li>
                </ul>
            </li>
        @endif

        @if(auth()->user()->isAdmin())
            <!-- Admin Section -->
            <li>
                <div class="text-xs font-semibold leading-6 text-gray-500 dark:text-gray-400">BEHEER</div>
                <ul role="list" class="-mx-2 mt-2 space-y-1">
                    <li>
                        <a href="{{ route('portal.admin.users.index') }}"
                           class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('portal.admin.users.*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-indigo-600 dark:hover:text-indigo-400'
                            }}">
                            <x-heroicon-o-users class="h-6 w-6 shrink-0"/>
                            Gebruikers
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('portal.admin.roles.index') }}"
                           class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('portal.admin.roles.*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-indigo-600 dark:hover:text-indigo-400'
                            }}">
                            <x-heroicon-o-key class="h-6 w-6 shrink-0"/>
                            Rollen
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('portal.admin.settings.index') }}"
                           class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('portal.admin.settings.*')
                                ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-indigo-600 dark:hover:text-indigo-400'
                            }}">
                            <x-heroicon-o-cog-6-tooth class="h-6 w-6 shrink-0"/>
                            Instellingen
                        </a>
                    </li>
                </ul>
            </li>
        @endif
        @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('manage-companies'))
            <li>
                <div class="text-xs font-semibold leading-6 text-gray-400 dark:text-gray-500">Bedrijven</div>
                <ul role="list" class="mt-2 space-y-1">
                    <li>
                        <a href="{{ route('portal.companies.requests.index') }}"
                            @class(['group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6',
                                'bg-gray-50 dark:bg-gray-800 text-indigo-600 dark:text-indigo-400' => request()->routeIs('portal.companies.requests.*'),
                                'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-indigo-400 dark:hover:bg-gray-800' => !request()->routeIs('portal.companies.requests.*')])>
                            <x-heroicon-o-inbox-stack class="h-6 w-6 shrink-0"/>
                            Bedrijfsaanvragen
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('portal.companies.dissolutions.index') }}"
                            @class(['group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6',
                                'bg-gray-50 dark:bg-gray-800 text-indigo-600 dark:text-indigo-400' => request()->routeIs('portal.companies.dissolutions.*'),
                                'text-gray-700 hover:text-indigo-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-indigo-400 dark:hover:bg-gray-800' => !request()->routeIs('portal.companies.dissolutions.*')])>
                            <x-heroicon-o-archive-box-x-mark class="h-6 w-6 shrink-0"/>
                            Opheffingsaanvragen
                        </a>
                    </li>
                    @if(auth()->user()->isAdmin())
                        <li>
                            <a href="{{ route('portal.admin.companies.types.index') }}"
                               class="group flex gap-x-3 rounded-md p-2 text-sm font-semibold transition-all duration-150 {{ request()->routeIs('portal.admin.companies.*')
                                    ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
                                    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-indigo-600 dark:hover:text-indigo-400'
                                }}">
                                <x-heroicon-o-building-library class="h-6 w-6 shrink-0"/>
                                Type bedrijven
                            </a>
                        </li>
                    @endif
                </ul>
            </li>
        @endif
        <!-- User Profile Section -->
        <li class="mt-auto">
            <div class="border-t border-gray-100 dark:border-gray-800 pt-4">
                <div class="flex items-center gap-x-4 px-2 py-3 text-sm">
                    <img src="https://crafatar.com/avatars/{{ auth()->user()->minecraft_uuid }}?overlay=true&size=128"
                         alt="{{ auth()->user()->minecraft_username }}"
                         class="h-10 w-10 rounded-full bg-gray-50 dark:bg-gray-800 ring-2 ring-white dark:ring-gray-900">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                            {{ auth()->user()->minecraft_username }} ({{ auth()->user()->getLevelAttribute() }})
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            {{ auth()->user()->getPrefixAttribute() }}
                        </p>
                    </div>

                    <!-- Dark Mode Toggle -->
                    <button @click="toggleDarkMode()"
                            class="rounded-lg p-2 text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-100 hover:bg-gray-50/90 dark:hover:bg-gray-800/90 transition-all duration-150">
                        <x-heroicon-s-moon x-show="!darkMode" class="h-5 w-5"/>
                        <x-heroicon-s-sun x-show="darkMode" class="h-5 w-5"/>
                    </button>
                </div>
            </div>
        </li>
    </ul>
</nav>
