@extends('portal.layouts.app')

@section('title', 'Vergunningen')
@section('header', 'Vergunningen')

@section('content')
    <div class="space-y-8">
        <!-- Available Permits -->
        <div class="space-y-4">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Beschikbare Vergunningen</h2>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($permitTypes as $type)
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden hover:ring-2 hover:ring-indigo-500 dark:hover:ring-indigo-400 transition-all duration-150">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $type->name }}</h3>
                                <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-600/20 dark:bg-indigo-500/10 dark:text-indigo-400 dark:ring-indigo-500/20">
                                    €{{ number_format($type->price, 2) }}
                                </span>
                            </div>
                            
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ $type->description }}
                            </p>

                            <div class="mt-4">
                                <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Vereiste informatie:</h4>
                                <ul class="mt-2 space-y-1">
                                    @foreach($type->form_fields as $field)
                                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                            <x-heroicon-s-chevron-right class="h-4 w-4 mr-1 text-gray-400 dark:text-gray-500"/>
                                            {{ $field['label'] }}
                                            @if($field['required'])
                                                <span class="ml-1 text-red-500">*</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="mt-6">
                                <a href="{{ route('portal.permits.request', $type) }}"
                                   class="block w-full rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                    Aanvragen
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="text-center rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700 p-12">
                            <x-heroicon-o-document-text class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"/>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen vergunningen beschikbaar</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Er zijn momenteel geen vergunningen beschikbaar voor aanvraag.
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- User's Permit Requests -->
        <div class="space-y-4">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Mijn Aanvragen</h2>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50">
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:pl-6">Vergunning</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Aangevraagd op</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Behandeld door</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Bekijk</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($requests as $request)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white sm:pl-6">
                                        {{ $request->type->name }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $request->created_at->format('d-m-Y H:i') }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        <span @class([
                                            'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                            'bg-yellow-50 text-yellow-700 ring-yellow-600/20 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/20' => $request->isPending(),
                                            'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' => $request->isApproved(),
                                            'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20' => $request->isDenied(),
                                        ])>
                                            @if($request->isPending())
                                                In behandeling
                                            @elseif($request->isApproved())
                                                Goedgekeurd
                                            @else
                                                Afgewezen
                                            @endif
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        @if($request->handler)
                                            {{ $request->handler->name }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <a href="{{ route('portal.permits.show', $request) }}"
                                           class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                            Bekijken
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        Je hebt nog geen vergunningen aangevraagd
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
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