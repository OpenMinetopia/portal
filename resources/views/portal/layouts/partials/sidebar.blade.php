<!-- Sidebar for mobile -->
<div x-show="sidebarOpen"
     class="relative z-50 lg:hidden"
     x-transition:enter="transition ease-in-out duration-300 transform"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in-out duration-300 transform"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900/80" @click="sidebarOpen = false"></div>

    <div class="fixed inset-0 flex">
        <div class="relative mr-16 flex w-full max-w-xs flex-1">
            <!-- Close button -->
            <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                <button @click="sidebarOpen = false" class="-m-2.5 p-2.5 text-white hover:text-gray-200">
                    <span class="sr-only">Close sidebar</span>
                    <x-heroicon-s-x-mark class="h-6 w-6" />
                </button>
            </div>

            <!-- Sidebar content -->
            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-2">
                @include('portal.layouts.partials.sidebar-content')
                @include('portal.layouts.partials.user-profile')
            </div>
        </div>
    </div>
</div>

<!-- Static sidebar for desktop -->
<div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-2 border-r border-gray-900">
        @include('portal.layouts.partials.sidebar-content')
        @include('portal.layouts.partials.user-profile')
    </div>
</div>
