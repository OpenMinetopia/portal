<!-- Mobile sidebar -->
<div class="lg:hidden" x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="fixed inset-0 z-50 flex">
        <div class="relative flex w-full max-w-xs flex-1 flex-col glass-card m-4 rounded-2xl" 
             x-transition:enter="transition ease-in-out duration-300 transform" 
             x-transition:enter-start="-translate-x-full" 
             x-transition:enter-end="translate-x-0" 
             x-transition:leave="transition ease-in-out duration-300 transform" 
             x-transition:leave-start="translate-x-0" 
             x-transition:leave-end="-translate-x-full">
            
            <div class="absolute right-0 top-0 -mr-12 pt-2">
                <button type="button" 
                        class="ml-1 flex h-10 w-10 items-center justify-center rounded-full glass-card hover:bg-white/10 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2 focus:ring-offset-transparent" 
                        @click="sidebarOpen = false">
                    <span class="sr-only">Close sidebar</span>
                    <x-heroicon-o-x-mark class="h-5 w-5 text-white" />
                </button>
            </div>

            <div class="flex grow flex-col gap-y-5 overflow-y-auto custom-scrollbar px-6 pb-4">
                @include('portal.layouts.v2.partials.sidebar-content')
            </div>
        </div>

        <div class="w-14 flex-shrink-0" aria-hidden="true">
            <!-- Dummy element to force sidebar to shrink to fit close icon -->
        </div>
    </div>
</div>

<!-- Desktop sidebar -->
<div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-80 lg:flex-col">
    <div class="flex grow flex-col gap-y-5 overflow-y-auto custom-scrollbar glass-card px-6 pb-4 shadow-2xl m-4 rounded-2xl">
        @include('portal.layouts.v2.partials.sidebar-content')
    </div>
</div> 