function initMinecraftPreview() {
    const usernameInput = document.getElementById('minecraft_username');
    const previewContainer = document.getElementById('minecraft-preview');
    let debounceTimer;

    if (!usernameInput || !previewContainer) return;

    const handleInput = (event) => {
        clearTimeout(debounceTimer);
        
        const username = event.target.value.trim();
        
        if (!username) {
            previewContainer.innerHTML = '';
            return;
        }

        // Show loading state
        previewContainer.innerHTML = `
            <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="animate-pulse flex space-x-3 w-full">
                    <div class="rounded-md bg-gray-300 dark:bg-gray-600 h-12 w-12"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-24"></div>
                        <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-32"></div>
                    </div>
                </div>
            </div>`;

        debounceTimer = setTimeout(() => {
            if (username.length >= 3) {
                fetchPlayerData(username);
            }
        }, 500);
    };

    async function fetchPlayerData(username) {
        try {
            const response = await fetch(`/api/minecraft/player/${encodeURIComponent(username)}`);
            const data = await response.json();

            if (data.success) {
                previewContainer.innerHTML = `
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <img src="${data.skin_url}" alt="${data.name}'s skin" 
                            class="w-12 h-12 rounded-md shadow-sm"
                            onerror="this.src='https://crafatar.com/avatars/steve'"/>
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                ${data.name}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                ${data.uuid}
                            </div>
                        </div>
                        <div class="ml-auto">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>`;
            } else {
                throw new Error(data.message || 'Player not found');
            }
        } catch (error) {
            previewContainer.innerHTML = `
                <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    <p class="text-sm text-red-600 dark:text-red-400">
                        Could not find Minecraft account with this username
                    </p>
                </div>`;
        }
    }

    usernameInput.addEventListener('input', handleInput);
}

// Initialize when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMinecraftPreview);
} else {
    initMinecraftPreview();
}
