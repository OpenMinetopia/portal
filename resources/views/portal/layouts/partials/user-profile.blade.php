<div class="flex flex-shrink-0 border-t border-gray-800">
    <div class="flex w-full items-center gap-x-4 px-6 py-3">
        <img class="h-8 w-8 rounded-full bg-gray-800" 
             src="https://mc-heads.net/avatar/{{ auth()->user()->minecraft_username }}" 
             alt="{{ auth()->user()->name }}">
        <span class="flex flex-col">
            <span class="text-white">{{ auth()->user()->name }}</span>
            <span class="text-sm text-gray-400">{{ auth()->user()->minecraft_username }}</span>
        </span>
        <button @click="toggleDarkMode" type="button" class="ml-auto rounded-lg p-2.5 text-gray-400 hover:text-white focus:outline-none">
            <svg x-show="!darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
            </svg>
            <svg x-show="darkMode" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"></path>
            </svg>
        </button>
    </div>
</div> 