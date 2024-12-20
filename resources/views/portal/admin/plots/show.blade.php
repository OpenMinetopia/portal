@extends('portal.layouts.app')

@section('title', 'Plot Details')
@section('header', 'Plot Details')

@section('content')
    <div class="space-y-6">
        <!-- Plot Details -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Plot Informatie</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $plot['name'] }}</p>
                    </div>
                </div>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Beschrijving</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $plot['description'] ?? 'Geen beschrijving' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Prioriteit</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $plot['priority'] }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Locatie</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            Min: {{ $plot['location']['min']['x'] ?? 0 }}, {{ $plot['location']['min']['y'] ?? 0 }}, {{ $plot['location']['min']['z'] ?? 0 }}<br>
                            Max: {{ $plot['location']['max']['x'] ?? 0 }}, {{ $plot['location']['max']['y'] ?? 0 }}, {{ $plot['location']['max']['z'] ?? 0 }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Add this section after the basic plot info -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mt-6">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Plot Flags & Details</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <!-- Location Details -->
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Locatie</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="font-medium">Minimum:</span><br>
                                    X: {{ $plot['location']['min']['x'] }}<br>
                                    Y: {{ $plot['location']['min']['y'] }}<br>
                                    Z: {{ $plot['location']['min']['z'] }}
                                </div>
                                <div>
                                    <span class="font-medium">Maximum:</span><br>
                                    X: {{ $plot['location']['max']['x'] }}<br>
                                    Y: {{ $plot['location']['max']['y'] }}<br>
                                    Z: {{ $plot['location']['max']['z'] }}
                                </div>
                            </div>
                        </dd>
                    </div>

                    <!-- Flags -->
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Flags</dt>
                        <dd class="mt-2 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($plot['flags'] as $flag => $value)
                                <div class="flex items-center justify-between rounded-lg bg-gray-50 dark:bg-gray-700/50 p-3">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $flag }}</span>
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ 
                                        $value === 'ALLOW' ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' : 
                                        ($value === 'DENY' ? 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20' : 
                                        'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20') 
                                    }}">
                                        {{ $value }}
                                    </span>
                                </div>
                            @endforeach
                        </dd>
                    </div>

                    <!-- Priority -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Prioriteit</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $plot['priority'] }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Users Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Owners Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-heroicon-s-user class="h-5 w-5 text-gray-400 dark:text-gray-500"/>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Eigenaren</h3>
                        </div>
                        <button type="button" 
                                onclick="document.getElementById('add-owner-modal').classList.remove('hidden')"
                                class="inline-flex items-center gap-x-1.5 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <x-heroicon-m-plus class="h-4 w-4"/>
                            Toevoegen
                        </button>
                    </div>
                </div>
                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($plot['owners'] as $owner)
                        <li class="flex items-center justify-between gap-x-6 px-4 py-5 sm:px-6 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <div class="flex min-w-0 gap-x-4">
                                <img class="h-12 w-12 flex-none rounded-lg bg-gray-50 dark:bg-gray-800"
                                     src="https://crafatar.com/avatars/{{ $owner }}?overlay=true"
                                     alt="{{ $owner }}">
                                <div class="min-w-0 flex-auto">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ \App\Helpers\MinecraftHelper::getName($owner) }}
                                    </p>
                                </div>
                            </div>
                            <form action="{{ route('portal.admin.plots.owners.remove', $plot['name']) }}" method="POST" class="flex-shrink-0">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="user_uuid" value="{{ $owner }}">
                                <button type="submit" 
                                        class="rounded-md bg-red-50 dark:bg-red-500/10 p-2 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-500/20">
                                    <x-heroicon-m-trash class="h-5 w-5"/>
                                </button>
                            </form>
                        </li>
                    @empty
                        <li class="px-4 py-5 sm:px-6">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Geen eigenaren</p>
                        </li>
                    @endforelse
                </ul>
            </div>

            <!-- Members Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-heroicon-s-users class="h-5 w-5 text-gray-400 dark:text-gray-500"/>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Leden</h3>
                        </div>
                        <button type="button"
                                onclick="document.getElementById('add-member-modal').classList.remove('hidden')"
                                class="inline-flex items-center gap-x-1.5 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <x-heroicon-m-plus class="h-4 w-4"/>
                            Toevoegen
                        </button>
                    </div>
                </div>
                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($plot['members'] as $member)
                        <li class="flex items-center justify-between gap-x-6 px-4 py-5 sm:px-6 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <div class="flex min-w-0 gap-x-4">
                                <img class="h-12 w-12 flex-none rounded-lg bg-gray-50 dark:bg-gray-800"
                                     src="https://crafatar.com/avatars/{{ $member }}?overlay=true"
                                     alt="{{ $member }}">
                                <div class="min-w-0 flex-auto">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ \App\Helpers\MinecraftHelper::getName($member) }}
                                    </p>
                                </div>
                            </div>
                            <form action="{{ route('portal.admin.plots.members.remove', $plot['name']) }}" method="POST" class="flex-shrink-0">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="user_uuid" value="{{ $member }}">
                                <button type="submit"
                                        class="rounded-md bg-red-50 dark:bg-red-500/10 p-2 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-500/20">
                                    <x-heroicon-m-trash class="h-5 w-5"/>
                                </button>
                            </form>
                        </li>
                    @empty
                        <li class="px-4 py-5 sm:px-6">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Geen leden</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Add Owner Modal -->
    <div id="add-owner-modal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                    <form action="{{ route('portal.admin.plots.owners.add', $plot['name']) }}" method="POST">
                        @csrf
                        <div>
                            <div class="mt-3 text-center sm:mt-0 sm:text-left">
                                <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">Eigenaar toevoegen</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Selecteer een speler om als eigenaar toe te voegen aan dit plot.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="user_uuid" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Speler</label>
                            <select name="user_uuid" id="user_uuid" required
                                    class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6 dark:bg-gray-700">
                                <option value="">Selecteer een speler</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->minecraft_plain_uuid }}">{{ $user->minecraft_username }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                    class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">
                                Toevoegen
                            </button>
                            <button type="button"
                                    onclick="document.getElementById('add-owner-modal').classList.add('hidden')"
                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:mt-0 sm:w-auto">
                                Annuleren
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div id="add-member-modal" class="hidden relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                    <form action="{{ route('portal.admin.plots.members.add', $plot['name']) }}" method="POST">
                        @csrf
                        <div>
                            <div class="mt-3 text-center sm:mt-0 sm:text-left">
                                <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">Lid toevoegen</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Selecteer een speler om als lid toe te voegen aan dit plot.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="user_uuid" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Speler</label>
                            <select name="user_uuid" id="user_uuid" required
                                    class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 dark:text-white ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6 dark:bg-gray-700">
                                <option value="">Selecteer een speler</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->minecraft_plain_uuid }}">{{ $user->minecraft_username }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                    class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">
                                Toevoegen
                            </button>
                            <button type="button"
                                    onclick="document.getElementById('add-member-modal').classList.add('hidden')"
                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:mt-0 sm:w-auto">
                                Annuleren
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 