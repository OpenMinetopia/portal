{{-- Admin and default role switches; $role is null on create. --}}
<div class="space-y-4">
    @if(auth()->user()->isAdmin())
        <div class="relative flex items-start">
            <div class="flex h-6 items-center">
                <input type="checkbox" name="is_admin" id="is_admin" value="1"
                       @checked(old('is_admin', $role?->is_admin))
                       @disabled($role?->slug === 'admin')
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800">
            </div>
            <div class="ml-3">
                <label for="is_admin" class="text-sm font-medium text-gray-700 dark:text-gray-300">Admin Rol</label>
                <p class="text-sm text-gray-500 dark:text-gray-400">Mag alles in elke feature die aanstaat, los van de permissies hieronder.</p>
            </div>
        </div>
    @endif

    <div class="relative flex items-start">
        <div class="flex h-6 items-center">
            <input type="checkbox" name="is_default" id="is_default" value="1"
                   @checked(old('is_default', $role?->is_default))
                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800">
        </div>
        <div class="ml-3">
            <label for="is_default" class="text-sm font-medium text-gray-700 dark:text-gray-300">Standaardrol</label>
            <p class="text-sm text-gray-500 dark:text-gray-400">Nieuwe gebruikers krijgen deze rol automatisch.</p>
        </div>
    </div>
</div>
