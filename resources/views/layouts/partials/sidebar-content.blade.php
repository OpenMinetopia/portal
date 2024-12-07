<div class="flex-1 flex flex-col pt-8 pb-4 overflow-y-auto">
    <!-- Logo -->
    <div class="flex items-center flex-shrink-0 px-6">
        <span class="text-2xl font-bold bg-gradient-to-r from-indigo-500 to-purple-500 bg-clip-text text-transparent">
            Minetopia
        </span>
    </div>

    <!-- Navigation -->
    <nav class="mt-8 flex-1 px-4 space-y-1">
        <!-- Main Navigation -->
        <div class="space-y-2">
            <a href="{{ route('dashboard') }}" 
                class="{{ request()->routeIs('dashboard') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }} 
                    group flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-indigo-600 dark:text-indigo-300' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400' }}" 
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <a href="#" 
                class="text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 group flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400" 
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Plots
            </a>

            <a href="#" 
                class="text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 group flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400" 
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Companies
            </a>
        </div>

        <!-- Admin Section -->
        @if(auth()->user()->isAdmin())
            <div class="mt-10 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="px-4 mb-4">
                    <p class="px-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                        Administration
                    </p>
                </div>
                
                <div class="space-y-2">
                    <a href="{{ route('admin.roles.index') }}" 
                        class="{{ request()->routeIs('admin.roles.*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }} 
                            group flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.roles.*') ? 'text-indigo-600 dark:text-indigo-300' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400' }}" 
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Roles & Permissions
                    </a>

                    <a href="{{ route('admin.users.index') }}" 
                        class="{{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }} 
                            group flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-indigo-600 dark:text-indigo-300' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400' }}" 
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Users
                    </a>
                </div>
            </div>
        @endif
    </nav>
</div>

<!-- User Profile -->
<div class="flex-shrink-0 border-t border-gray-200 dark:border-gray-700">
    <div class="px-4 py-4">
        <div class="flex items-center">
            <img src="https://crafatar.com/avatars/{{ auth()->user()->minecraft_uuid }}?overlay=true" 
                alt="{{ auth()->user()->minecraft_username }}"
                class="h-8 w-8 rounded-lg shadow-md">
            <div class="ml-3 min-w-0 flex-1">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                    {{ auth()->user()->name }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                    {{ auth()->user()->minecraft_username }}
                </p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="ml-2 p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div> 