@extends('portal.layouts.app')

@section('title', 'Politiedatabase')
@section('header', 'Politiedatabase')

@section('content')
    <div class="space-y-6">
        <!-- Search & Filters -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="p-6">
                <div class="space-y-4">
                    <!-- Search input -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Zoeken</label>
                        <div class="mt-1 relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                                <x-heroicon-m-magnifying-glass class="h-5 w-5 text-gray-400 dark:text-gray-500"/>
                            </div>
                            <input type="text" 
                                   name="search" 
                                   id="search"
                                   class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 dark:text-white dark:bg-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6"
                                   placeholder="Zoek op naam of minecraft username"
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="w-full sm:w-1/3">
                            <label for="has_records" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Strafblad</label>
                            <select name="has_records" 
                                    id="has_records"
                                    class="mt-1 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 dark:text-white dark:bg-gray-700 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-500 sm:text-sm sm:leading-6">
                                <option value="">Alle spelers</option>
                                <option value="1" {{ request('has_records') === '1' ? 'selected' : '' }}>Met strafblad</option>
                                <option value="0" {{ request('has_records') === '0' ? 'selected' : '' }}>Zonder strafblad</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div id="players-table" class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
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