<div x-data="{ open: false }" 
     @keydown.window.escape="open = false">
    <!-- Trigger Button -->
    <button @click="open = true" 
            type="button" 
            class="relative rounded-md p-2 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800">
        <span class="sr-only">View notifications</span>
        <x-heroicon-s-bell class="h-6 w-6" />
        <!-- Notification badge -->
        <span class="absolute -top-1.5 -right-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-medium text-white">3</span>
    </button>

    <!-- Slide Over Panel -->
    <div x-show="open" 
         x-cloak
         class="fixed inset-0 z-50 overflow-hidden"
         role="dialog" 
         aria-modal="true">
        <!-- Background backdrop -->
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="open = false"
             class="fixed inset-0 bg-gray-900/75 dark:bg-black/75 backdrop-blur-sm"></div>

        <!-- Panel -->
        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div x-show="open"
                         x-transition:enter="transform transition ease-in-out duration-500"
                         x-transition:enter-start="translate-x-full"
                         x-transition:enter-end="translate-x-0"
                         x-transition:leave="transform transition ease-in-out duration-500"
                         x-transition:leave-start="translate-x-0"
                         x-transition:leave-end="translate-x-full"
                         class="pointer-events-auto relative w-screen max-w-md">
                        
                        <div class="flex h-full flex-col overflow-hidden bg-white dark:bg-gray-900 shadow-xl">
                            <!-- Header -->
                            <div class="border-b border-gray-200 dark:border-gray-800">
                                <div class="flex items-center justify-between px-4 py-6 sm:px-6">
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Notifications</h2>
                                    <button @click="open = false" 
                                            class="rounded-md text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400">
                                        <span class="sr-only">Close panel</span>
                                        <x-heroicon-s-x-mark class="h-6 w-6" />
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Empty State -->
                            <div class="flex-1 overflow-y-auto">
                                <div class="flex h-full flex-col items-center justify-center px-4 py-12 sm:px-6">
                                    <div class="flex h-24 w-24 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                                        <x-heroicon-o-bell-slash class="h-12 w-12 text-gray-400 dark:text-gray-500" />
                                    </div>
                                    <h3 class="mt-6 text-base font-semibold text-gray-900 dark:text-white">No notifications</h3>
                                    <p class="mt-2 text-center text-sm text-gray-500 dark:text-gray-400">
                                        You're all caught up! Check back later for new notifications.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 