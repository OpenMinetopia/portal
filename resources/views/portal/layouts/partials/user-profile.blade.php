<div class="sticky bottom-0 -mx-4 mt-6 border-t border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
    <div class="flex items-center gap-3">
        <img class="omt-pixel h-8 w-8 rounded-md bg-gray-100 dark:bg-gray-800"
             src="https://mc-heads.net/avatar/{{ auth()->user()->minecraft_username }}"
             alt="">
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
            <p class="truncate font-mono text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->minecraft_username }}</p>
        </div>
        <button @click="toggleDarkMode" type="button" class="rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                :aria-label="darkMode ? 'Licht thema' : 'Donker thema'">
            <x-heroicon-o-moon x-show="!darkMode" class="h-5 w-5" />
            <x-heroicon-o-sun x-show="darkMode" x-cloak class="h-5 w-5" />
        </button>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white" aria-label="Uitloggen" title="Uitloggen">
                <x-heroicon-o-arrow-right-on-rectangle class="h-5 w-5" />
            </button>
        </form>
    </div>
</div>
