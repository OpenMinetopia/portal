<div class="mt-auto border-t border-gray-100 dark:border-gray-800 pt-4">
    <div class="flex items-center gap-x-4 px-2 py-3 text-sm">
        <img src="https://crafatar.com/avatars/{{ auth()->user()->minecraft_uuid }}?overlay=true&size=128"
             alt="{{ auth()->user()->minecraft_username }}"
             class="h-10 w-10 rounded-full bg-gray-50 dark:bg-gray-800 ring-2 ring-white dark:ring-gray-900">
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                {{ auth()->user()->minecraft_username }} ({{ auth()->user()->level }})
            </p>
            <div class="group relative">
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                    {{ auth()->user()->prefix }}
                </p>
                @if(count(auth()->user()->available_prefixes) > 0)
                    <div class="hidden group-hover:block absolute left-0 bottom-full mb-2 w-64 rounded-lg bg-white dark:bg-gray-700 shadow-lg ring-1 ring-gray-200 dark:ring-gray-600 z-50">
                        <div class="p-2">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Beschikbare Prefixes</p>
                            <div class="space-y-1">
                                @foreach(auth()->user()->available_prefixes as $prefix)
                                    <div class="flex items-center justify-between px-2 py-1 rounded-md {{ $prefix['prefix'] === auth()->user()->prefix ? 'bg-indigo-50 dark:bg-indigo-500/10' : '' }}">
                                        <span class="text-sm text-gray-900 dark:text-white">
                                            {{ $prefix['prefix'] }}
                                            @if($prefix['prefix'] === auth()->user()->prefix)
                                                <span class="ml-1 text-xs text-indigo-500 dark:text-indigo-400">(Actief)</span>
                                            @endif
                                        </span>
                                        @if($prefix['expires_at'] !== -1)
                                            <span class="text-xs text-gray-500">
                                                Verloopt: {{ \Carbon\Carbon::createFromTimestamp($prefix['expires_at'])->format('d-m-Y') }}
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Dark Mode Toggle -->
        <button @click="toggleDarkMode()"
                class="rounded-lg p-2 text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-100 hover:bg-gray-50/90 dark:hover:bg-gray-800/90 transition-all duration-150">
            <x-heroicon-o-moon x-show="!darkMode" class="h-5 w-5"/>
            <x-heroicon-o-sun x-show="darkMode" class="h-5 w-5"/>
        </button>
    </div>
</div> 