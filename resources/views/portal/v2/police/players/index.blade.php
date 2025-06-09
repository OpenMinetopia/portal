@extends('portal.layouts.v2.app')

@section('title', 'Politiedatabase')
@section('header', 'Politiedatabase')

@section('content')
    <div class="space-y-8">
        <!-- Search & Filters -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-indigo-600/5"></div>
            <div class="relative">
                <div class="p-6 border-b border-gray-200/50 dark:border-gray-700/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                            <x-heroicon-s-magnifying-glass class="h-5 w-5 text-white" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Zoeken & Filteren</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Doorzoek de spelersdatabase</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <!-- Search input -->
                        <div>
                            <label for="search" class="block text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Zoeken</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 pl-4 flex items-center">
                                    <x-heroicon-m-magnifying-glass class="h-5 w-5 text-gray-400 dark:text-gray-500"/>
                                </div>
                                <input type="text" 
                                       name="search" 
                                       id="search"
                                       class="block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm py-3 pl-12 pr-4 text-gray-900 dark:text-white placeholder:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                       placeholder="Zoek op naam of minecraft username"
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="has_records" class="block text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">Strafblad Filter</label>
                                <select name="has_records" 
                                        id="has_records"
                                        class="block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white/50 dark:bg-gray-700/50 backdrop-blur-sm py-3 px-4 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                    <option value="">Alle spelers</option>
                                    <option value="1" {{ request('has_records') === '1' ? 'selected' : '' }}>Met strafblad</option>
                                    <option value="0" {{ request('has_records') === '0' ? 'selected' : '' }}>Zonder strafblad</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div id="players-table" class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
            @include('portal.police.players.partials.table')
        </div>
    </div>

    @push('scripts')
        <script>
            const searchInput = document.getElementById('search')
            const hasRecordsSelect = document.getElementById('has_records')
            let searchTimeout

            function updateResults() {
                const searchValue = searchInput.value
                const hasRecordsValue = hasRecordsSelect.value
                const url = new URL(window.location.href)

                url.searchParams.set('search', searchValue)
                url.searchParams.set('has_records', hasRecordsValue)

                fetch(`${url.pathname}${url.search}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('players-table').innerHTML = html
                    })
            }

            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimeout)
                searchTimeout = setTimeout(updateResults, 300)
            })

            hasRecordsSelect.addEventListener('change', updateResults)
        </script>
    @endpush
@endsection 