{{-- One VOG from the plugin: outcome, when it was given and by whom, and until when it is valid. --}}
<div class="flex items-start gap-3">
    <div class="flex-shrink-0 pt-0.5">
        @if($vog['positive'])
            <x-heroicon-s-shield-check class="h-5 w-5 text-green-500 dark:text-green-400"/>
        @else
            <x-heroicon-s-shield-exclamation class="h-5 w-5 text-red-500 dark:text-red-400"/>
        @endif
    </div>
    <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-sm font-medium text-gray-900 dark:text-white">
                {{ $vog['positive'] ? 'Positief' : 'Negatief' }}
            </span>
            @if($vog['valid'])
                <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-xs font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20">
                    Geldig tot {{ $vog['valid_until']->format('d-m-Y') }}
                </span>
            @else
                <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-xs font-medium bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/20 dark:bg-gray-500/10 dark:text-gray-400 dark:ring-gray-500/20">
                    Verlopen
                </span>
            @endif
        </div>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            {{ $vog['date']->format('d-m-Y H:i') }}
            @if($vog['officer_name'])
                · Agent: {{ $vog['officer_name'] }}
            @endif
            @unless($vog['positive'])
                · {{ $vog['record_count'] }} {{ $vog['record_count'] === 1 ? 'overtreding' : 'overtredingen' }}
            @endunless
        </p>
    </div>
</div>
