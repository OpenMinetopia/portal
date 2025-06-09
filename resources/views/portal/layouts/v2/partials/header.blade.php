<header class="sticky top-4 z-40 glass-card glass-card-hover shadow-2xl mx-4 rounded-2xl">
    <div class="flex h-20 items-center gap-x-6 px-6 sm:px-8 lg:px-10">
        <!-- Mobile menu button -->
        <button type="button" 
                @click="sidebarOpen = true" 
                class="-m-2.5 p-3 text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100/70 dark:hover:bg-white/10 rounded-xl transition-all duration-300 lg:hidden backdrop-blur-sm border border-transparent hover:border-gray-300/50 dark:hover:border-white/20">
            <span class="sr-only">Open sidebar</span>
            <x-heroicon-s-bars-3 class="h-6 w-6" />
        </button>

        <!-- Page Title with Breadcrumb Style -->
        @hasSection('header')
            <div class="flex-1">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full"></div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                        @yield('header')
                    </h1>
                </div>
                <div class="mt-1 text-sm text-gray-600 dark:text-slate-400">
                    {{ now()->format('l, F j, Y') }}
                </div>
            </div>
        @endif

        <div class="flex items-center gap-3">
            <!-- Dark Mode Toggle -->
            <button @click="toggleDarkMode()" 
                    class="relative rounded-xl p-3 text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100/70 dark:hover:bg-white/10 transition-all duration-300 backdrop-blur-sm border border-transparent hover:border-gray-300/50 dark:hover:border-white/20">
                <span class="sr-only">Toggle dark mode</span>
                <x-heroicon-s-sun class="h-5 w-5 dark:hidden" />
                <x-heroicon-s-moon class="h-5 w-5 hidden dark:block" />
            </button>

            <!-- Notifications -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="relative rounded-xl p-3 text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100/70 dark:hover:bg-white/10 transition-all duration-300 backdrop-blur-sm border border-transparent hover:border-gray-300/50 dark:hover:border-white/20">
                    <span class="sr-only">View notifications</span>
                    <x-heroicon-s-bell class="h-5 w-5" />
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="absolute -top-1 -right-1 h-5 w-5 rounded-full bg-gradient-to-r from-red-500 to-pink-600 flex items-center justify-center">
                            <span class="text-xs font-bold text-white">{{ auth()->user()->unreadNotifications->count() }}</span>
                        </span>
                    @endif
                </button>

                <!-- Notifications Panel -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                     class="absolute right-0 z-10 mt-4 w-96 origin-top-right rounded-2xl glass-card shadow-2xl">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Notificaties</h2>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                                        Alles gelezen
                                    </button>
                                </form>
                            @endif
                        </div>
                        
                        <div class="space-y-4 max-h-96 overflow-y-auto">
                            @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notification)
                                <div @class([
                                    'flex gap-4 p-4 rounded-xl transition-all duration-200 border',
                                    'bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-blue-200/50 dark:border-blue-700/50' => !$notification->read_at,
                                    'bg-gray-50/50 dark:bg-gray-700/30 border-gray-200/50 dark:border-gray-600/50 hover:bg-gray-100/50 dark:hover:bg-gray-700/50' => $notification->read_at,
                                ])>
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-r from-green-500 to-emerald-600 flex items-center justify-center">
                                            @if($notification->data['type'] === 'bought')
                                                <x-heroicon-s-shopping-cart class="h-5 w-5 text-white"/>
                                            @else
                                                <x-heroicon-s-banknotes class="h-5 w-5 text-white"/>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $notification->data['title'] }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-slate-300 mt-1">
                                            {{ $notification->data['message'] }}
                                        </p>
                                        <p class="mt-2 text-xs font-medium text-gray-500 dark:text-slate-400">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    @if(!$notification->read_at)
                                        <div class="flex-shrink-0">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 mx-auto bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-2xl flex items-center justify-center mb-4">
                                        <x-heroicon-o-bell class="h-8 w-8 text-gray-400"/>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Geen notificaties</h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-slate-400">Je hebt nog geen notificaties ontvangen.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Profile Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="flex items-center gap-3 rounded-xl p-3 text-gray-700 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100/70 dark:hover:bg-white/10 transition-all duration-300 backdrop-blur-sm border border-transparent hover:border-gray-300/50 dark:hover:border-white/20">
                    <img src="https://crafatar.com/avatars/{{ auth()->user()->minecraft_uuid }}?overlay=true&size=64"
                         alt="{{ auth()->user()->minecraft_username }}"
                         class="w-8 h-8 rounded-lg shadow-sm">
                    <div class="hidden sm:block text-left">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-gray-600 dark:text-slate-400">{{ auth()->user()->getPrefixAttribute() }}</div>
                    </div>
                    <x-heroicon-s-chevron-down class="h-4 w-4" />
                </button>

                <!-- Profile Dropdown -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                     class="absolute right-0 z-10 mt-4 w-64 origin-top-right rounded-2xl glass-card shadow-2xl">
                    <div class="p-4">
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 mb-4">
                            <img src="https://crafatar.com/avatars/{{ auth()->user()->minecraft_uuid }}?overlay=true&size=96"
                                 alt="{{ auth()->user()->minecraft_username }}"
                                 class="w-12 h-12 rounded-xl shadow-lg">
                            <div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</div>
                                <div class="text-sm text-gray-600 dark:text-slate-300">{{ auth()->user()->getPrefixAttribute() }}</div>
                                <div class="text-xs font-medium text-emerald-600 dark:text-emerald-400">{{ auth()->user()->formatted_balance_with_currency }}</div>
                            </div>
                        </div>
                        
                        <div class="space-y-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full flex items-center gap-3 px-3 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-200">
                                    <x-heroicon-s-arrow-right-on-rectangle class="h-4 w-4" />
                                    Uitloggen
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header> 