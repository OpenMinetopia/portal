@if(session('success') || session('error'))
    @php
        $type = session('success') ? 'success' : 'error';
        $message = session($type);
        $title = is_array($message) ? $message['title'] : null;
        $text = is_array($message) ? $message['message'] : $message;
        
        $colors = [
            'success' => [
                'bg' => 'bg-green-50 dark:bg-green-500/10',
                'text' => 'text-green-800 dark:text-green-200',
                'icon' => 'text-green-400 dark:text-green-500',
                'hover' => 'hover:bg-green-100 dark:hover:bg-green-500/20',
                'button' => 'text-green-500 dark:text-green-400',
            ],
            'error' => [
                'bg' => 'bg-red-50 dark:bg-red-500/10',
                'text' => 'text-red-800 dark:text-red-200',
                'icon' => 'text-red-400 dark:text-red-500',
                'hover' => 'hover:bg-red-100 dark:hover:bg-red-500/20',
                'button' => 'text-red-500 dark:text-red-400',
            ],
        ];
    @endphp

    <div x-data="{ show: true }"
         x-show="show"
         x-transition
         x-init="setTimeout(() => show = false, 5000)"
         class="fixed bottom-0 right-0 m-6 w-96 max-w-full z-50">
        <div class="rounded-lg shadow-lg {{ $colors[$type]['bg'] }} p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    @if($type === 'success')
                        <x-heroicon-s-check-circle class="h-5 w-5 {{ $colors[$type]['icon'] }}"/>
                    @else
                        <x-heroicon-s-x-circle class="h-5 w-5 {{ $colors[$type]['icon'] }}"/>
                    @endif
                </div>
                <div class="ml-3 w-0 flex-1">
                    @if($title)
                        <p class="text-sm font-medium {{ $colors[$type]['text'] }}">
                            {{ $title }}
                        </p>
                    @endif
                    <p class="mt-1 text-sm {{ $colors[$type]['text'] }}">
                        {{ $text }}
                    </p>
                </div>
                <div class="ml-4 flex flex-shrink-0">
                    <button @click="show = false"
                            type="button"
                            class="inline-flex rounded-md p-1.5 {{ $colors[$type]['button'] }} {{ $colors[$type]['hover'] }}">
                        <span class="sr-only">Sluiten</span>
                        <x-heroicon-s-x-mark class="h-5 w-5"/>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif 