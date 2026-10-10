@extends('portal.layouts.app')

@php
    $accountNames = collect($bankAccounts)->pluck('name', 'uuid');
    $date = fn ($value) => $value?->format('d-m-Y H:i');
    $badge = fn ($rental) => match (true) {
        !$rental->isActive() => 'bg-gray-50 text-gray-600 ring-gray-500/20 dark:bg-gray-500/10 dark:text-gray-400 dark:ring-gray-500/20',
        $rental->isOverdue() => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20',
        $rental->isCancelled() => 'bg-yellow-50 text-yellow-800 ring-yellow-600/20 dark:bg-yellow-500/10 dark:text-yellow-300 dark:ring-yellow-500/20',
        default => 'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20',
    };
    $card = 'bg-white dark:bg-gray-800 shadow-sm rounded-lg';
    $cancelButton = 'inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20';
@endphp

@section('title', 'Mijn huur')
@section('header', 'Mijn huur')

@section('content')
    <div class="space-y-8">
        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Plots die ik huur</h2>
                @can('use-broker')
                    <a href="{{ route('portal.plots.listings.index') }}#te-huur" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Plots te huur bekijken</a>
                @endcan
            </div>

            @forelse($renting as $rental)
                <div class="{{ $card }} p-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    @if($rental->isActive())
                                        <a href="{{ route('portal.plots.show', $rental->plot_name) }}" class="hover:underline">{{ $rental->plot_name }}</a>
                                    @else
                                        {{ $rental->plot_name }}
                                    @endif
                                </h3>
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $badge($rental) }}">{{ $rental->status_text }}</span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Verhuurder: {{ $rental->landlord_name }} · {{ $rental->formatted_price }} {{ $rental->period_text }}
                            </p>
                        </div>
                        @if($rental->isActive() && !$rental->isCancelled())
                            <form action="{{ route('portal.plots.rentals.cancel', $rental) }}" method="POST"
                                  onsubmit="return confirm('{{ $rental->isOverdue() ? 'De huur stopt meteen en je verliest de toegang tot het plot. Doorgaan?' : 'Je houdt het plot tot het eind van de betaalde periode. Daarna stopt de huur. Doorgaan?' }}')">
                                @csrf
                                <button type="submit" class="{{ $cancelButton }}">Opzeggen</button>
                            </form>
                        @endif
                    </div>

                    <dl class="mt-4 grid grid-cols-1 gap-4 text-sm sm:grid-cols-3">
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Gehuurd sinds</dt>
                            <dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ $date($rental->started_at) }}</dd>
                        </div>
                        <div>
                            @if(!$rental->isActive())
                                <dt class="text-gray-500 dark:text-gray-400">Beëindigd op</dt>
                                <dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ $date($rental->ended_at) }}</dd>
                            @elseif($rental->isCancelled())
                                <dt class="text-gray-500 dark:text-gray-400">Eindigt op</dt>
                                <dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ $date($rental->ends_at) }}</dd>
                            @else
                                <dt class="text-gray-500 dark:text-gray-400">Betaald tot</dt>
                                <dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ $date($rental->paid_until) }}</dd>
                            @endif
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400">Betaalrekening</dt>
                            <dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ $accountNames[$rental->renter_bank_account_uuid] ?? 'Onbekende rekening' }}</dd>
                        </div>
                    </dl>

                    @if($rental->isOverdue())
                        <div class="mt-4 rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-500/10 dark:text-red-200">
                            <p class="font-medium text-red-800 dark:text-red-300">De huur van {{ $rental->formatted_price }} kon niet worden afgeschreven.</p>
                            <p class="mt-1">Betaal uiterlijk <strong>{{ $date($rental->grace_until) }}</strong>, anders eindigt de huur en verlies je de toegang tot het plot.
                                Zet geld op je rekening of betaal hieronder met een andere rekening.</p>
                        </div>
                    @endif

                    @if($rental->isActive() && !$rental->isCancelled())
                        <form action="{{ route('portal.plots.rentals.pay', $rental) }}" method="POST" class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center">
                            @csrf
                            <label for="account-{{ $rental->id }}" class="sr-only">Betaalrekening</label>
                            <select id="account-{{ $rental->id }}" name="renter_bank_account_uuid"
                                    class="block w-full sm:w-auto py-2 pl-3 pr-10 text-sm border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @foreach($bankAccounts as $account)
                                    <option value="{{ $account['uuid'] }}" @selected($account['uuid'] === $rental->renter_bank_account_uuid)>
                                        {{ $account['name'] }} (€ {{ number_format($account['balance'], 2, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" @class([
                                'inline-flex justify-center items-center px-3 py-2 text-sm font-medium rounded-md shadow-sm',
                                'text-white bg-indigo-600 hover:bg-indigo-700' => $rental->isOverdue(),
                                'text-gray-700 bg-white ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600' => !$rental->isOverdue(),
                            ])>
                                {{ $rental->isOverdue() ? 'Nu betalen' : 'Rekening wijzigen' }}
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="{{ $card }} px-6 py-8 text-center">
                    <x-heroicon-o-key class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500"/>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Je huurt geen plots.</p>
                </div>
            @endforelse
        </section>

        @if($letting->isNotEmpty() || $offers->isNotEmpty())
            <section class="space-y-4">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Plots die ik verhuur</h2>

                @if($letting->isNotEmpty())
                    <div class="{{ $card }} overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr class="text-left text-gray-500 dark:text-gray-400">
                                    <th class="px-4 py-3 font-medium">Plot</th>
                                    <th class="px-4 py-3 font-medium">Huurder</th>
                                    <th class="px-4 py-3 font-medium">Huur</th>
                                    <th class="px-4 py-3 font-medium">Status</th>
                                    <th class="px-4 py-3 font-medium">Betaald tot</th>
                                    <th class="px-4 py-3"><span class="sr-only">Acties</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($letting as $rental)
                                    <tr class="text-gray-900 dark:text-white">
                                        <td class="px-4 py-3 font-medium whitespace-nowrap">
                                            {{ $rental->plot_name }}
                                            @if($rental->is_server_listing)
                                                <span class="ml-1 text-xs font-normal text-gray-500 dark:text-gray-400">({{ config('app.name') }})</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $rental->renter_name }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $rental->formatted_price }} {{ $rental->period_text }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $badge($rental) }}">{{ $rental->status_text }}</span>
                                            @if($rental->isOverdue())
                                                <span class="block mt-1 text-xs text-gray-500 dark:text-gray-400">Eindigt {{ $date($rental->grace_until) }} zonder betaling</span>
                                            @elseif($rental->isCancelled())
                                                <span class="block mt-1 text-xs text-gray-500 dark:text-gray-400">Eindigt {{ $date($rental->ends_at) }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $rental->isActive() ? $date($rental->paid_until) : '—' }}</td>
                                        <td class="px-4 py-3 text-right">
                                            @if($rental->isActive() && !$rental->isCancelled())
                                                <form action="{{ route('portal.plots.rentals.cancel', $rental) }}" method="POST"
                                                      onsubmit="return confirm('{{ $rental->isOverdue() ? 'De huur stopt meteen en de huurder verliest de toegang. Doorgaan?' : 'De huurder houdt het plot tot het eind van de betaalde periode. Doorgaan?' }}')">
                                                    @csrf
                                                    <button type="submit" class="{{ $cancelButton }}">Opzeggen</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if($offers->isNotEmpty() && \App\Models\PortalFeature::isEnabled('broker'))
                    <div class="{{ $card }} divide-y divide-gray-200 dark:divide-gray-700">
                        <div class="px-4 py-3 text-sm font-medium text-gray-500 dark:text-gray-400">Te huur, nog geen huurder</div>
                        @foreach($offers as $offer)
                            <div class="flex items-center justify-between gap-4 px-4 py-3 text-sm">
                                <span class="text-gray-900 dark:text-white">
                                    <span class="font-medium">{{ $offer->plot_name }}</span>
                                    · {{ $offer->formatted_price }} {{ $offer->period_text }}
                                </span>
                                <form action="{{ route('portal.plots.rentals.offers.destroy', $offer) }}" method="POST"
                                      onsubmit="return confirm('Weet je zeker dat je dit plot van de markt wilt halen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="{{ $cancelButton }}">Van de markt halen</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        @endif
    </div>
@endsection
