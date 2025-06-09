@extends('portal.layouts.v2.app')

@section('title', 'Bedrijvenregister')
@section('header', 'Bedrijvenregister')

@section('content')
    <div class="space-y-8" x-data="companyRegistry">
        <!-- Welcome Header -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500/10 via-blue-500/10 to-purple-500/10 backdrop-blur-sm border border-white/20 p-8">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
            <div class="relative">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <span class="text-2xl">🔍</span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 dark:text-white">Bedrijvenregister</h1>
                        <p class="text-lg text-gray-600 dark:text-gray-300">Zoek en ontdek geregistreerde bedrijven</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Doorzoek alle actieve bedrijven</span>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-600/5"></div>
            <div class="relative p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <x-heroicon-s-funnel class="h-5 w-5 text-white" />
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Zoekfilters</h2>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Search Input -->
                    <div class="lg:col-span-2">
                        <label for="search" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Zoeken</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-heroicon-s-magnifying-glass class="h-5 w-5 text-gray-400"/>
                            </div>
                            <input type="text"
                                   name="search"
                                   id="search"
                                   x-model="search"
                                   x-on:input.debounce.300ms="fetchCompanies"
                                   class="block w-full pl-10 pr-4 py-3 border border-gray-200/50 dark:border-gray-700/50 rounded-xl bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200"
                                   placeholder="Zoek op naam, KvK nummer of beschrijving">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select id="status"
                                name="status"
                                x-model="selectedStatus"
                                x-on:change="fetchCompanies"
                                class="block w-full px-4 py-3 border border-gray-200/50 dark:border-gray-700/50 rounded-xl bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200">
                            <option value="">Alle statussen</option>
                            <option value="active">Actief</option>
                            <option value="inactive">Inactief</option>
                        </select>
                    </div>

                    <!-- Type Filter -->
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label for="type" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Type bedrijf</label>
                        <select id="type"
                                name="type"
                                x-model="selectedType"
                                x-on:change="fetchCompanies"
                                class="block w-full px-4 py-3 border border-gray-200/50 dark:border-gray-700/50 rounded-xl bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200">
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
        <div class="space-y-8">
            <!-- Empty State -->
            <template x-if="!loading && companies.data.length === 0">
                <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-12 shadow-lg text-center">
                    <div class="absolute inset-0 bg-gradient-to-br from-gray-500/5 to-gray-600/5"></div>
                    <div class="relative">
                        <div class="w-24 h-24 bg-gradient-to-r from-gray-400 to-gray-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <x-heroicon-o-building-office class="h-12 w-12 text-white"/>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Geen bedrijven gevonden</h3>
                        <p class="text-gray-600 dark:text-gray-400">
                            Probeer andere zoektermen of filters om bedrijven te vinden.
                        </p>
                    </div>
                </div>
            </template>

            <!-- Companies Grid -->
            <template x-if="!loading && companies.data.length > 0">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <template x-for="company in companies.data" :key="company.id">
                        <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-lg transition-all duration-300">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-blue-600/5"></div>
                            <div class="relative p-6">
                                <!-- Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-xl font-black text-gray-900 dark:text-white mb-1 truncate" x-text="company.name"></h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="company.type.name"></p>
                                    </div>
                                    <div class="ml-3 flex-shrink-0">
                                        <span class="inline-flex items-center rounded-xl px-3 py-1 text-xs font-bold ring-1 ring-inset"
                                              :class="{
                                                  'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20': company.is_active,
                                                  'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20': !company.is_active
                                              }">
                                            <div class="w-1.5 h-1.5 rounded-full mr-1.5"
                                                 :class="{
                                                     'bg-emerald-500': company.is_active,
                                                     'bg-red-500': !company.is_active
                                                 }"></div>
                                            <span x-text="company.is_active ? 'Actief' : 'Inactief'"></span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Company Details -->
                                <div class="space-y-4 mb-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <x-heroicon-s-identification class="h-4 w-4 text-white" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">KvK Nummer</p>
                                            <p class="text-sm font-bold text-gray-900 dark:text-white font-mono" x-text="company.kvk_number"></p>
                                        </div>
                                    </div>

                                    <div x-show="company.description" class="flex items-start gap-3">
                                        <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <x-heroicon-s-document-text class="h-4 w-4 text-white" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Beschrijving</p>
                                            <p class="text-sm text-gray-900 dark:text-white leading-relaxed" x-text="company.description"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <div class="border-t border-gray-200/50 dark:border-gray-700/50 pt-4">
                                    <a :href="'{{ route('portal.companies.registry.show', '') }}/' + company.id"
                                       class="w-full inline-flex justify-center items-center gap-2 px-6 py-3 text-sm font-bold text-white bg-gradient-to-r from-emerald-500 to-blue-600 rounded-xl hover:shadow-lg transition-all duration-200">
                                        <x-heroicon-s-eye class="h-4 w-4"/>
                                        Bekijk Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Loading State -->
            <div x-show="loading" class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-12 shadow-lg text-center">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 to-purple-600/5"></div>
                <div class="relative">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="animate-spin h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">Bedrijven laden...</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Even geduld alstublieft</p>
                </div>
            </div>

            <!-- Pagination -->
            <div x-show="!loading && companies.data.length > 0" class="mt-8">
                <template x-if="companies.last_page > 1">
                    <div class="card-hover relative overflow-hidden rounded-2xl bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 p-4 shadow-lg">
                        <div class="absolute inset-0 bg-gradient-to-br from-gray-500/5 to-gray-600/5"></div>
                        <div class="relative flex items-center justify-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Paginering wordt hier getoond wanneer er meerdere pagina's zijn
                            </p>
                        </div>
                    </div>
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