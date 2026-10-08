<!-- Sidebar for mobile -->
<div x-show="sidebarOpen" x-cloak class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-gray-950/60" @click="sidebarOpen = false"></div>

    <div class="pointer-events-none fixed inset-0 flex">
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="pointer-events-auto relative flex w-full max-w-xs flex-1 flex-col overflow-y-auto bg-white px-4 dark:bg-gray-900">
            <button @click="sidebarOpen = false" class="absolute right-3 top-4 rounded-md p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800">
                <span class="sr-only">Menu sluiten</span>
                <x-heroicon-o-x-mark class="h-5 w-5" />
            </button>
            @include('portal.layouts.partials.sidebar-content')
            @include('portal.layouts.partials.user-profile')
        </div>
    </div>
</div>

<!-- Static sidebar for desktop -->
<div class="hidden lg:fixed lg:inset-y-0 lg:z-40 lg:flex lg:w-64 lg:flex-col">
    <div class="flex grow flex-col overflow-y-auto border-r border-gray-200 bg-white px-4 dark:border-gray-800 dark:bg-gray-900">
        @include('portal.layouts.partials.sidebar-content')
        @include('portal.layouts.partials.user-profile')
    </div>
</div>
