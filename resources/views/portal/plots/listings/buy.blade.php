@extends('portal.layouts.app')

@section('title', 'Plot kopen')
@section('header')
    <div class="flex items-center gap-4">
        <a href="{{ route('portal.plots.listings.index') }}"
           class="group flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
            <x-heroicon-s-arrow-left class="w-5 h-5"/>
            Terug naar overzicht
        </a>
    </div>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Warning Alert -->
        <div class="bg-yellow-50 dark:bg-yellow-500/10 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-yellow-400 dark:text-yellow-300"/>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Let op</h3>
                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-200">
                        <p>Je staat op het punt een plot te kopen. Deze actie kan niet ongedaan worden gemaakt. Controleer alle details zorgvuldig voordat je doorgaat met de aankoop.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Plot Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Plot Info Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                    @if($listing->image_path)
                        <img src="{{ Storage::url($listing->image_path) }}"
                             alt="Afbeelding van {{ $listing->plot_name }}"
                             class="w-full h-64 object-cover">
                    @endif

                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $listing->plot_name }}</h1>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Verkoper: {{ \App\Helpers\MinecraftHelper::getName($listing->seller->minecraft_uuid) }}
                                </p>
                            </div>
                            <span class="inline-flex items-center rounded-md px-2.5 py-1.5 text-sm font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20">
                                {{ $listing->formatted_price }}
                            </span>
                        </div>

                        <div class="mt-6">
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Beschrijving</h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $listing->description }}</p>
                        </div>

                        <!-- Plot Specifications -->
                        <div class="mt-6 grid grid-cols-2 gap-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Afmetingen</h3>
                                <dl class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                    <div class="flex justify-between">
                                        <dt>Breedte</dt>
                                        <dd class="font-medium">{{ $listing->dimensions['width'] }} blokken</dd>
                                    </div>
                                    <div class="flex justify-between mt-1">
                                        <dt>Lengte</dt>
                                        <dd class="font-medium">{{ $listing->dimensions['length'] }} blokken</dd>
                                    </div>
                                    <div class="flex justify-between mt-1">
                                        <dt>Hoogte</dt>
                                        <dd class="font-medium">{{ $listing->dimensions['height'] }} blokken</dd>
                                    </div>
                                    <div class="flex justify-between mt-1 pt-1 border-t border-gray-200 dark:border-gray-700">
                                        <dt>Oppervlakte</dt>
                                        <dd class="font-medium">{{ $listing->area }} m²</dd>
                                    </div>
                                </dl>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Locatie</h3>
                                <dl class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                    <div class="space-y-1">
                                        <div class="flex justify-between">
                                            <dt>Minimaal</dt>
                                            <dd class="font-mono">{{ $listing->min_x }}, {{ $listing->min_y }}, {{ $listing->min_z }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt>Maximaal</dt>
                                            <dd class="font-mono">{{ $listing->max_x }}, {{ $listing->max_y }}, {{ $listing->max_z }}</dd>
                                        </div>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purchase Summary -->
            <div class="space-y-6">
                <!-- Transaction Summary -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Transactie overzicht</h3>
                    </div>
                    <div class="px-6 py-5">
                        <dl class="space-y-4">
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-500 dark:text-gray-400">Je huidige saldo</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">
                                    € {{ number_format(auth()->user()->balance, 2, ',', '.') }}
                                </dd>
                            </div>
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-500 dark:text-gray-400">Aankoopprijs</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">
                                    {{ $listing->formatted_price }}
                                </dd>
                            </div>
                            <div class="pt-4 flex justify-between text-sm border-t border-gray-200 dark:border-gray-700">
                                <dt class="font-medium text-gray-900 dark:text-white">Resterend saldo</dt>
                                <dd class="font-medium text-gray-900 dark:text-white">
                                    € {{ number_format(auth()->user()->balance - $listing->price, 2, ',', '.') }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Confirmation -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input id="terms" name="terms" type="checkbox" required
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600">
                                <label for="terms" class="ml-2 block text-sm text-gray-900 dark:text-white">
                                    Ik begrijp dat deze aankoop definitief is
                                </label>
                            </div>

                            <form action="{{ route('portal.plots.listings.buy', $listing) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                    Plot kopen voor {{ $listing->formatted_price }}
                                </button>
                            </form>

                            <p class="text-xs text-center text-gray-500 dark:text-gray-400">
                                Door op 'Plot kopen' te klikken ga je akkoord met de aankoop en wordt het bedrag direct van je saldo afgeschreven
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Enable/disable submit button based on checkbox
        const termsCheckbox = document.getElementById('terms');
        const submitButton = document.querySelector('button[type="submit"]');
        
        submitButton.disabled = true;
        submitButton.classList.add('opacity-50', 'cursor-not-allowed');
        
        termsCheckbox.addEventListener('change', function() {
            submitButton.disabled = !this.checked;
            submitButton.classList.toggle('opacity-50', !this.checked);
            submitButton.classList.toggle('cursor-not-allowed', !this.checked);
        });
    </script>
    @endpush
@endsection 