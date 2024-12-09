@extends('portal.layouts.app')

@section('title', 'Bedrijvenregister')
@section('header', 'Bedrijvenregister')

@section('content')
    <div class="space-y-6" x-data="companyRegistry">
        <!-- Search Section -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <!-- Search Input (make it span 2 columns on sm screens) -->
                    <div class="sm:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Zoeken</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-heroicon-s-magnifying-glass class="h-5 w-5 text-gray-400"/>
                            </div>
                            <input type="text"
                                   name="search"
                                   id="search"
                                   x-model="search"
                                   x-on:input.debounce.300ms="fetchCompanies"
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:text-white"
                                   placeholder="Zoek op naam, KvK nummer of beschrijving">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select id="status"
                                name="status"
                                x-model="selectedStatus"
                                x-on:change="fetchCompanies"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:text-white">
                            <option value="">Alle statussen</option>
                            <option value="active">Actief</option>
                            <option value="inactive">Inactief</option>
                        </select>
                    </div>

                    <!-- Type Filter -->
                    <div class="sm:col-span-3">
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type bedrijf</label>
                        <select id="type"
                                name="type"
                                x-model="selectedType"
                                x-on:change="fetchCompanies"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:text-white">
                            <option value="">Alle types</option>
                            @foreach(\App\Models\CompanyType::where('is_active', true)->get() as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div class="space-y-6">
            <template x-if="!loading && companies.data.length === 0">
                <div class="text-center bg-white dark:bg-gray-800 shadow-sm rounded-lg px-4 py-12">
                    <x-heroicon-o-building-office class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"/>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen bedrijven gevonden</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Probeer andere zoektermen of filters.
                    </p>
                </div>
            </template>

            <template x-if="!loading && companies.data.length > 0">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <template x-for="company in companies.data" :key="company.id">
                        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg hover:shadow-md transition-shadow duration-200">
                            <div class="px-4 py-5 sm:p-6">
                                <div class="flex items-start justify-between">
                                    <div class="space-y-1">
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white" x-text="company.name"></h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400" x-text="company.type.name"></p>
                                    </div>
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
                                          :class="{
                                              'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20': company.is_active,
                                              'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20': !company.is_active
                                          }">
                                        <span x-text="company.is_active ? 'Actief' : 'Inactief'"></span>
                                    </span>
                                </div>

                                <div class="mt-4 space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">KvK Nummer</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white font-mono" x-text="company.kvk_number"></dd>
                                    </div>
                                    <div x-show="company.description">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Beschrijving</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white" x-text="company.description"></dd>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <a :href="'{{ route('portal.companies.registry.show', '') }}/' + company.id"
                                       class="inline-flex items-center text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                                        Bekijk details
                                        <x-heroicon-s-arrow-right class="ml-1 h-4 w-4"/>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Loading State -->
            <div x-show="loading" class="text-center py-12">
                <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm text-indigo-600 dark:text-indigo-400 transition ease-in-out duration-150 cursor-not-allowed">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Laden...
                </div>
            </div>

            <!-- Pagination -->
            <div x-show="!loading && companies.data.length > 0" class="mt-6">
                <template x-if="companies.last_page > 1">
                    <nav class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 px-4 sm:px-0">
                        <!-- Pagination implementation here -->
                    </nav>
                </template>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('companyRegistry', () => ({
                    search: '',
                    selectedType: '',
                    selectedStatus: '',
                    companies: { data: [] },
                    loading: true,
                    page: 1,

                    init() {
                        this.fetchCompanies()
                    },

                    async fetchCompanies() {
                        this.loading = true
                        try {
                            const response = await fetch(`/portal/companies/registry/search?search=${this.search}&type=${this.selectedType}&status=${this.selectedStatus}&page=${this.page}`)
                            this.companies = await response.json()
                        } catch (error) {
                            console.error('Error fetching companies:', error)
                        }
                        this.loading = false
                    },

                    setPage(page) {
                        this.page = page
                        this.fetchCompanies()
                    }
                }))
            })
        </script>
    @endpush
@endsection 