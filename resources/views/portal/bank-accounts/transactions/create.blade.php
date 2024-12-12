@extends('portal.layouts.app')

@section('title', 'Geld Overmaken')
@section('header')
    <div class="flex items-center gap-4">
        <a href="{{ route('portal.bank-accounts.show', $account['uuid']) }}"
           class="group flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
            <x-heroicon-s-arrow-left class="w-5 h-5"/>
            Terug naar rekening
        </a>
    </div>
@endsection

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Column - Form -->
            <div class="lg:col-span-2">
                <form action="{{ route('portal.bank-accounts.transactions.store', $account['uuid']) }}" 
                      method="POST" 
                      x-data="transferForm()"
                      class="space-y-6">
                    @csrf
                    <input type="hidden" name="transferType" x-model="transferType">

                    @if($errors->any())
                        <div class="rounded-md bg-red-50 p-4 dark:bg-red-500/10">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <x-heroicon-s-x-circle class="h-5 w-5 text-red-400 dark:text-red-500"/>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm text-red-700 dark:text-red-300">
                                        {{ $errors->first('error') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Form Card -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Transactie Details</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Maak geld over naar een andere rekening
                            </p>
                        </div>
                        <div class="p-6 space-y-8">
                            <!-- Transfer Type Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    Type Overboeking
                                </label>
                                <div class="grid grid-cols-2 gap-4">
                                    <button type="button" 
                                            @click="transferType = 'player'"
                                            :class="{ 'ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-500/10': transferType === 'player' }"
                                            class="relative rounded-lg border border-gray-300 dark:border-gray-600 p-4 hover:border-indigo-400 dark:hover:border-indigo-500 focus:outline-none">
                                        <div class="flex items-center gap-3">
                                            <x-heroicon-s-user-group class="h-5 w-5 text-gray-400"/>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Andere Speler</span>
                                        </div>
                                    </button>
                                    <button type="button"
                                            @click="transferType = 'own'"
                                            :class="{ 'ring-2 ring-indigo-500 bg-indigo-50 dark:bg-indigo-500/10': transferType === 'own' }"
                                            class="relative rounded-lg border border-gray-300 dark:border-gray-600 p-4 hover:border-indigo-400 dark:hover:border-indigo-500 focus:outline-none">
                                        <div class="flex items-center gap-3">
                                            <x-heroicon-s-credit-card class="h-5 w-5 text-gray-400"/>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">Eigen Rekening</span>
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <!-- Player Selection -->
                            <div x-show="transferType === 'player'" x-transition>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Ontvanger <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="to_user_id"
                                            x-model="selectedUser"
                                            @change="loadUserAccounts()"
                                            :required="transferType === 'player'"
                                            :disabled="transferType !== 'player'"
                                            class="block w-full px-4 py-3 text-base border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                        <option value="">Selecteer een speler</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->minecraft_username }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <x-heroicon-s-user class="h-5 w-5 text-gray-400"/>
                                    </div>
                                </div>
                            </div>

                            <!-- Target Account Selection -->
                            <div x-show="selectedUser || transferType === 'own'" x-transition>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <span x-text="transferType === 'own' ? 'Doelrekening' : 'Rekening van ontvanger'"></span>
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select name="to_account_uuid" 
                                            id="target_account_uuid"
                                            required
                                            class="block w-full px-4 py-3 text-base border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                                        <option value="">Selecteer een rekening</option>
                                        <template x-if="transferType === 'own'">
                                            @foreach($allAccounts as $acc)
                                                @if($acc['uuid'] !== $account['uuid'])
                                                    <option value="{{ $acc['uuid'] }}">
                                                        {{ $acc['name'] }} (€ {{ number_format($acc['balance'], 2, ',', '.') }})
                                                    </option>
                                                @endif
                                            @endforeach
                                        </template>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <x-heroicon-s-credit-card class="h-5 w-5 text-gray-400"/>
                                    </div>
                                </div>
                            </div>

                            <!-- Amount -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Bedrag <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" 
                                           name="amount" 
                                           id="amount"
                                           step="0.01"
                                           min="0.01"
                                           max="{{ $account['balance'] }}"
                                           required
                                           class="px-4 py-3 block w-full border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-base dark:bg-gray-700 dark:text-white"
                                           placeholder="Voer een bedrag in">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <x-heroicon-s-banknotes class="h-5 w-5 text-gray-400"/>
                                    </div>
                                </div>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    Beschikbaar saldo: € {{ number_format($account['balance'], 2, ',', '.') }}
                                </p>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Omschrijving <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <textarea name="description" 
                                              id="description"
                                              required
                                              rows="2"
                                              maxlength="255"
                                              class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-base dark:bg-gray-700 dark:text-white resize-none"
                                              placeholder="Waar is deze betaling voor?"></textarea>
                                    <div class="absolute top-3 right-3">
                                        <x-heroicon-s-pencil class="h-5 w-5 text-gray-400"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end gap-4">
                        <button type="submit"
                                class="inline-flex justify-center rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                            <x-heroicon-s-paper-airplane class="h-4 w-4 mr-2"/>
                            Overmaken
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Source Account Info -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Bronrekening</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rekening</dt>
                                <dd class="mt-1 text-base text-gray-900 dark:text-white">{{ $account['name'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20">
                                        {{ $account['type'] }}
                                    </span>
                                </dd>
                            </div>
                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Beschikbaar saldo</dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                                    € {{ number_format($account['balance'], 2, ',', '.') }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('transferForm', () => ({
            transferType: 'player',
            selectedUser: '',
            init() {
                // Watch for changes in transferType
                this.$watch('transferType', value => {
                    const dropdown = document.getElementById('target_account_uuid');
                    dropdown.innerHTML = '<option value="">Selecteer een rekening</option>';
                    
                    if (value === 'own') {
                        // Show own accounts
                        @foreach($allAccounts as $acc)
                            dropdown.innerHTML += `
                                <option value="{{ $acc['uuid'] }}">
                                    {{ $acc['name'] }} (€ {{ number_format($acc['balance'], 2, ',', '.') }})
                                </option>
                            `;
                        @endforeach
                    } else {
                        // Clear selection when switching to player
                        this.selectedUser = '';
                    }
                });
            },
            async loadUserAccounts() {
                const dropdown = document.getElementById('target_account_uuid');
                dropdown.innerHTML = '<option value="">Selecteer een rekening</option>';
                
                if (!this.selectedUser) return;
                
                try {
                    const response = await fetch(`/portal/bank-accounts/accounts/${this.selectedUser}`);
                    if (!response.ok) throw new Error('Failed to fetch accounts');
                    
                    const accounts = await response.json();
                    console.log('Received accounts:', accounts);
                    
                    if (Array.isArray(accounts) && accounts.length > 0) {
                        accounts.forEach(account => {
                            dropdown.innerHTML += `
                                <option value="${account.uuid}">
                                    ${account.name}
                                </option>
                            `;
                        });
                    } else {
                        dropdown.innerHTML = '<option value="">Geen rekeningen gevonden</option>';
                    }
                } catch (error) {
                    console.error('Error loading accounts:', error);
                    dropdown.innerHTML = '<option value="">Error: Kon rekeningen niet laden</option>';
                }
            }
        }))
    });
</script>
@endpush 