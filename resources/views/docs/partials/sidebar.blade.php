<div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 px-6">
    <div class="flex h-16 shrink-0 items-center">
        <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">API Documentation</h1>
    </div>
    <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <!-- Getting Started Section -->
            <li>
                <div class="text-xs font-semibold leading-6 text-gray-500 dark:text-gray-400">Getting Started</div>
                <ul role="list" class="-mx-2 mt-2 space-y-1">
                    <li>
                        <a href="{{ route('api-docs.index') }}" 
                           class="{{ request()->routeIs('api-docs.index') ? 'bg-gray-50 dark:bg-gray-800 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Introduction
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('api-docs.authentication') }}" 
                           class="{{ request()->routeIs('api-docs.authentication') ? 'bg-gray-50 dark:bg-gray-800 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Authentication
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Player Management -->
            <li>
                <div class="text-xs font-semibold leading-6 text-gray-500 dark:text-gray-400">Player Management</div>
                <ul role="list" class="-mx-2 mt-2 space-y-1">
                    <li>
                        <a href="{{ route('api-docs.player') }}" 
                           class="{{ request()->routeIs('api-docs.player') ? 'bg-gray-50 dark:bg-gray-800 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Player Status
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('api-docs.level') }}" 
                           class="{{ request()->routeIs('api-docs.level') ? 'bg-gray-50 dark:bg-gray-800 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Level & Progress
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('api-docs.fitness') }}" 
                           class="{{ request()->routeIs('api-docs.fitness') ? 'bg-gray-50 dark:bg-gray-800 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Fitness System
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Economy & Properties -->
            <li>
                <div class="text-xs font-semibold leading-6 text-gray-500 dark:text-gray-400">Economy & Properties</div>
                <ul role="list" class="-mx-2 mt-2 space-y-1">
                    <li>
                        <a href="{{ route('api-docs.economy') }}" 
                           class="{{ request()->routeIs('api-docs.economy') ? 'bg-gray-50 dark:bg-gray-800 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Economy
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('api-docs.plots') }}" 
                           class="{{ request()->routeIs('api-docs.plots') ? 'bg-gray-50 dark:bg-gray-800 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Plots
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('api-docs.vehicles') }}" 
                           class="{{ request()->routeIs('api-docs.vehicles') ? 'bg-gray-50 dark:bg-gray-800 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Vehicles
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Law Enforcement -->
            <li>
                <div class="text-xs font-semibold leading-6 text-gray-500 dark:text-gray-400">Law Enforcement</div>
                <ul role="list" class="-mx-2 mt-2 space-y-1">
                    <li>
                        <a href="{{ route('api-docs.police') }}" 
                           class="{{ request()->routeIs('api-docs.police') ? 'bg-gray-50 dark:bg-gray-800 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Police System
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('api-docs.detection-gates') }}" 
                           class="{{ request()->routeIs('api-docs.detection-gates') ? 'bg-gray-50 dark:bg-gray-800 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Detection Gates
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Communication -->
            <li>
                <div class="text-xs font-semibold leading-6 text-gray-500 dark:text-gray-400">Communication</div>
                <ul role="list" class="-mx-2 mt-2 space-y-1">
                    <li>
                        <a href="{{ route('api-docs.chat') }}" 
                           class="{{ request()->routeIs('api-docs.chat') ? 'bg-gray-50 dark:bg-gray-800 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Chat System
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('api-docs.emergency') }}" 
                           class="{{ request()->routeIs('api-docs.emergency') ? 'bg-gray-50 dark:bg-gray-800 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Emergency Calls
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('api-docs.walkie-talkie') }}" 
                           class="{{ request()->routeIs('api-docs.walkie-talkie') ? 'bg-gray-50 dark:bg-gray-800 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Walkie Talkie
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Infrastructure -->
            <li>
                <div class="text-xs font-semibold leading-6 text-gray-500 dark:text-gray-400">Infrastructure</div>
                <ul role="list" class="-mx-2 mt-2 space-y-1">
                    <li>
                        <a href="{{ route('api-docs.teleporters') }}" 
                           class="{{ request()->routeIs('api-docs.teleporters') ? 'bg-gray-50 dark:bg-gray-800 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                            Teleporters
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</div> 