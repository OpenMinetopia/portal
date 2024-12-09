@extends('portal.layouts.app')

@section('title', 'Bedrijfstypes')
@section('header', 'Bedrijfstypes')

@section('content')
    <div class="space-y-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <!-- Total Types -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-500/10 rounded-lg">
                            <x-heroicon-s-building-office class="h-6 w-6 text-blue-600 dark:text-blue-400"/>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Totaal Types</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $companyTypes->total() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Types -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-500/10 rounded-lg">
                            <x-heroicon-s-check-badge class="h-6 w-6 text-green-600 dark:text-green-400"/>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Actieve Types</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $companyTypes->where('is_active', true)->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Companies -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-indigo-100 dark:bg-indigo-500/10 rounded-lg">
                            <x-heroicon-s-building-library class="h-6 w-6 text-indigo-600 dark:text-indigo-400"/>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Totaal Bedrijven</h3>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ \App\Models\Company::count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Company Types Table -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Bedrijfstypes</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Beheer alle beschikbare bedrijfstypes</p>
                    </div>
                    <a href="{{ route('portal.admin.companies.types.create') }}"
                       class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                        <x-heroicon-s-plus class="h-4 w-4 mr-2"/>
                        Nieuw Type
                    </a>
                </div>
            </div>

            <div class="flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800/50">
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:pl-3">Bedrijfstype</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Prijs</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Velden</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Bedrijven</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-3">
                                        <span class="sr-only">Acties</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($companyTypes as $type)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-3">
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $type->name }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($type->description, 50) }}</div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            €{{ number_format($type->price, 2) }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $type->is_active
                                                ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20'
                                                : 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20'
                                            }}">
                                                {{ $type->is_active ? 'Actief' : 'Inactief' }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20">
                                                {{ count($type->form_fields) }} velden
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-600/20 dark:bg-gray-500/10 dark:text-gray-400 dark:ring-gray-500/20">
                                                {{ $type->companies()->count() }} bedrijven
                                            </span>
                                        </td>
                                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-3">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('portal.admin.companies.types.edit', $type) }}"
                                                   class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                    Bewerken
                                                </a>
                                                <form action="{{ route('portal.admin.companies.types.destroy', $type) }}" 
                                                      method="POST" 
                                                      class="inline"
                                                      onsubmit="return confirm('Weet je zeker dat je dit bedrijfstype wilt verwijderen?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                                        Verwijderen
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 py-8 text-center">
                                            <div class="flex flex-col items-center">
                                                <x-heroicon-o-building-office class="h-12 w-12 text-gray-400 dark:text-gray-500"/>
                                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen bedrijfstypes</h3>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                    Er zijn nog geen bedrijfstypes aangemaakt.
                                                </p>
                                                <div class="mt-6">
                                                    <a href="{{ route('portal.admin.companies.types.create') }}"
                                                       class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                                        <x-heroicon-s-plus class="h-4 w-4 mr-2"/>
                                                        Nieuw Type
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($companyTypes->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                    {{ $companyTypes->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div x-data="{ show: true }"
             x-show="show"
             x-transition
             x-init="setTimeout(() => show = false, 3000)"
             class="fixed bottom-0 right-0 m-6 w-96 max-w-full">
            <div class="rounded-lg bg-green-50 p-4 shadow-lg dark:bg-green-500/10">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-s-check-circle class="h-5 w-5 text-green-400 dark:text-green-500"/>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                            {{ session('success') }}
                        </p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button @click="show = false" type="button"
                                    class="inline-flex rounded-md p-1.5 text-green-500 hover:bg-green-100 dark:text-green-400 dark:hover:bg-green-500/20">
                                <span class="sr-only">Sluiten</span>
                                <x-heroicon-s-x-mark class="h-5 w-5"/>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection 