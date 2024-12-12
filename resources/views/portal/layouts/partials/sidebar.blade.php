<!-- Sidebar for mobile -->
<div x-show="sidebarOpen"
     class="relative z-50 lg:hidden"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900/80 dark:bg-gray-900/90" @click="sidebarOpen = false"></div>

    <div class="fixed inset-0 flex">
        <div class="relative mr-16 flex w-full max-w-xs flex-1">
            <!-- Close button -->
            <div class="absolute right-0 top-0 -mr-12 pt-4">
                <button @click="sidebarOpen = false" class="ml-1 flex h-10 w-10 items-center justify-center focus:outline-none">
                    <span class="sr-only">Close sidebar</span>
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mobile sidebar content -->
            <div class="flex h-full w-full flex-col bg-white dark:bg-gray-900 px-6 ring-1 ring-gray-200/10 dark:ring-white/5">
                <div class="flex-1 overflow-y-auto">
                    @include('portal.layouts.partials.sidebar-content')
                </div>
                @include('portal.layouts.partials.user-profile')
            </div>
        </div>
    </div>
</div>

<!-- Static sidebar for desktop -->
<div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
    <div class="flex h-full flex-col bg-white dark:bg-gray-900 px-6 border-r border-gray-200 dark:border-gray-800">
        <div class="flex-1 overflow-y-auto">
            @include('portal.layouts.partials.sidebar-content')
        </div>
        @include('portal.layouts.partials.user-profile')
    </div>
</div>
