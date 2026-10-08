@if (session('success') || session('error'))
    @php
        $type = session('success') ? 'success' : 'error';
        $message = session($type);
        $title = is_array($message) ? $message['title'] : null;
        $text = is_array($message) ? $message['message'] : $message;
    @endphp

    {{-- A toast bottom-right that slides in, then leaves after five seconds. --}}
    <div x-data="{ show: false }"
         x-init="$nextTick(() => show = true); setTimeout(() => show = false, 5000)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-3"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0 translate-y-3"
         role="{{ $type === 'error' ? 'alert' : 'status' }}"
         class="fixed bottom-0 right-0 z-50 m-4 w-96 max-w-[calc(100%-2rem)] sm:m-6">
        <div @class([
            'flex items-start gap-3 rounded-lg border border-l-4 bg-white p-4 shadow-lg dark:bg-gray-800',
            'border-gray-200 border-l-brand dark:border-gray-700 dark:border-l-brand' => $type === 'success',
            'border-gray-200 border-l-red-600 dark:border-gray-700 dark:border-l-red-400' => $type === 'error',
        ])>
            <div class="min-w-0 flex-1 text-sm">
                <p class="font-semibold text-gray-900 dark:text-white">{{ $title ?? ($type === 'success' ? 'Gelukt' : 'Er ging iets mis') }}</p>
                <p class="mt-0.5 text-gray-600 dark:text-gray-300">{{ $text }}</p>
            </div>
            <button @click="show = false" type="button" class="-m-1 rounded-md p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-700 dark:hover:text-white">
                <span class="sr-only">Sluiten</span>
                <x-heroicon-o-x-mark class="h-5 w-5"/>
            </button>
        </div>
    </div>
@endif
