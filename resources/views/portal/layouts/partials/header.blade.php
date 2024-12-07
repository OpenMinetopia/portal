<header class="sticky top-0 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm border-b border-gray-100 dark:border-gray-800/80 shadow-sm dark:shadow-gray-950/50">
    <div class="flex h-16 items-center gap-x-4 px-4 sm:px-6 lg:px-8">
        <!-- Mobile menu button -->
        <button type="button" 
                @click="sidebarOpen = true" 
                class="-m-2.5 p-2.5 text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-100 hover:bg-gray-50/90 dark:hover:bg-gray-800/90 rounded-lg transition-all duration-150 lg:hidden">
            <span class="sr-only">Open sidebar</span>
            <x-heroicon-s-bars-3 class="h-6 w-6" />
        </button>

        <div class="flex flex-1 justify-end">
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