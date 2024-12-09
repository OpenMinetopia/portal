<div class="flow-root">
    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800/50">
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:pl-3">Speler</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Level</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Baan</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Overtredingen</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-3">
                            <span class="sr-only">Acties</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-3">
                                <div class="flex items-center gap-x-4">
                                    <img src="https://crafatar.com/avatars/{{ $user->minecraft_uuid }}?overlay=true&size=128"
                                         alt="{{ $user->minecraft_username }}"
                                         class="h-8 w-8 rounded-full bg-gray-50 dark:bg-gray-800">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $user->minecraft_username }}</div>
                                        <div class="text-gray-500 dark:text-gray-400">{{ $user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $user->level }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $user->prefix }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                <span @class([
                                    'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                    'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' => count($user->criminal_records) === 0,
                                    'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20' => count($user->criminal_records) > 0,
                                ])>
                                    {{ count($user->criminal_records) }}
                                </span>
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-3">
                                <a href="{{ route('portal.police.players.show', $user) }}"
                                   class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                    Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-8 text-center">
                                <div class="flex flex-col items-center">
                                    <x-heroicon-o-users class="h-12 w-12 text-gray-400 dark:text-gray-500"/>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen spelers gevonden</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Er zijn geen spelers gevonden met de opgegeven zoekcriteria.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($users->hasPages())
    <div class="px-4 py-5 sm:px-6 border-t border-gray-200 dark:border-gray-700">
        {{ $users->links() }}
    </div>
@endif 