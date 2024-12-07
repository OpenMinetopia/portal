<div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
    <div class="flex items-center flex-shrink-0 px-4">
        <img class="h-8 w-auto" src="{{ asset('images/logo.png') }}" alt="Logo">
    </div>
    <nav class="mt-5 flex-1 px-2 space-y-1">
        <x-sidebar-link 
            :href="route('dashboard')" 
            :active="request()->routeIs('dashboard')">
            <x-slot name="icon">
                <!-- Dashboard Icon -->
                <svg class="mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </x-slot>
            Dashboard
        </x-sidebar-link>

        <x-sidebar-link 
            :href="route('portal.plots.index')" 
            :active="request()->routeIs('portal.plots.*')">
            <x-slot name="icon">
                <!-- Plots Icon -->
                <svg class="mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </x-slot>
            Plots
        </x-sidebar-link>

        <x-sidebar-link 
            :href="route('portal.bank.index')" 
            :active="request()->routeIs('portal.bank.*')">
            <x-slot name="icon">
                <!-- Bank Icon -->
                <svg class="mr-3 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </x-slot>
            Bank
        </x-sidebar-link>

        <!-- Add other sidebar links as needed -->
    </nav>
</div> 