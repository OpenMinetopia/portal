<header class="sticky top-0 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm border-b border-gray-100 dark:border-gray-800/80 shadow-sm dark:shadow-gray-950/50">
    <div class="flex h-16 items-center gap-x-4 px-4 sm:px-6 lg:px-8">
        <!-- Mobile menu button -->
        <button type="button" 
                @click="sidebarOpen = true" 
                class="-m-2.5 p-2.5 text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-100 hover:bg-gray-50/90 dark:hover:bg-gray-800/90 rounded-lg transition-all duration-150 lg:hidden">
            <span class="sr-only">Open sidebar</span>
            <x-heroicon-s-bars-3 class="h-6 w-6" />
        </button>

        <!-- Page Title -->
        @hasSection('header')
            <div class="flex-1">
                <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                    @yield('header')
                </h1>
            </div>
        @endif

        <div class="flex justify-end gap-2">
            <!-- Notifications -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="relative rounded-lg p-2.5 text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-100 hover:bg-gray-50/90 dark:hover:bg-gray-800/90 transition-all duration-150">
                    <span class="sr-only">View notifications</span>
                    <x-heroicon-s-bell class="h-6 w-6" />
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="absolute top-2 right-2 h-2 w-2 rounded-full bg-red-500"></span>
                    @endif
                </button>

                <!-- Notifications Panel -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 z-10 mt-2 w-80 origin-top-right rounded-md bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Notificaties</h2>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                                        Alles gelezen
                                    </button>
                                </form>
                            @endif
                        </div>
                        
                        <div class="space-y-4 max-h-96 overflow-y-auto">
                            @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notification)
                                <div @class([
                                    'flex gap-4 p-3 rounded-lg transition-colors',
                                    'bg-gray-50 dark:bg-gray-700/50' => !$notification->read_at,
                                    'hover:bg-gray-50 dark:hover:bg-gray-700/50' => $notification->read_at,
                                ])>
                                    <div class="flex-shrink-0">
                                        @if($notification->data['type'] === 'bought')
                                            <x-heroicon-s-shopping-cart class="h-6 w-6 text-green-500"/>
                                        @else
                                            <x-heroicon-s-banknotes class="h-6 w-6 text-green-500"/>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $notification->data['title'] }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $notification->data['message'] }}
                                        </p>
                                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <x-heroicon-o-bell class="mx-auto h-12 w-12 text-gray-400"/>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen notificaties</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Je hebt nog geen notificaties ontvangen.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="rounded-lg p-2.5 text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-100 hover:bg-gray-50/90 dark:hover:bg-gray-800/90 transition-all duration-150">
                    <span class="sr-only">Log out</span>
                    <x-heroicon-s-arrow-right-on-rectangle class="h-6 w-6" />
                </button>
            </form>
        </div>
    </div>
</header> 