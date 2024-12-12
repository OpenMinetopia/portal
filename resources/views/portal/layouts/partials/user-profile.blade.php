<div class="mt-auto border-t border-gray-100 dark:border-gray-800 pt-4">
    <div class="flex items-center gap-x-4 px-2 py-3 text-sm">
        <img src="https://crafatar.com/avatars/{{ auth()->user()->minecraft_uuid }}?overlay=true&size=128"
             alt="{{ auth()->user()->minecraft_username }}"
             class="h-10 w-10 rounded-full bg-gray-50 dark:bg-gray-800 ring-2 ring-white dark:ring-gray-900">
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                {{ auth()->user()->minecraft_username }} ({{ auth()->user()->getLevelAttribute() }})
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                {{ auth()->user()->getPrefixAttribute() }}
            </p>
        </div>

        <!-- Dark Mode Toggle -->
        <button @click="toggleDarkMode()"
                class="rounded-lg p-2 text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-100 hover:bg-gray-50/90 dark:hover:bg-gray-800/90 transition-all duration-150">
            <x-heroicon-o-moon x-show="!darkMode" class="h-5 w-5"/>
            <x-heroicon-o-sun x-show="darkMode" class="h-5 w-5"/>
        </button>
    </div>
</div> 