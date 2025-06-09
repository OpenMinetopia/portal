@if(session('success') || session('error'))
    @php
        $type = session('success') ? 'success' : 'error';
        $message = session($type);
        $title = is_array($message) ? $message['title'] : null;
        $text = is_array($message) ? $message['message'] : $message;
    @endphp

    <div x-data="{ show: true }"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         x-init="setTimeout(() => show = false, 5000)"
         class="fixed bottom-6 right-6 max-w-sm z-50">
        
        @if($type === 'success')
            <div class="glass-card-hover relative overflow-hidden rounded-2xl bg-emerald-500/10 backdrop-blur-sm border border-emerald-500/20 p-4 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-green-600/10"></div>
                <div class="relative flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-green-600 rounded-lg flex items-center justify-center shadow-lg">
                        <x-heroicon-s-check class="h-5 w-5 text-white"/>
                    </div>
                    <div class="flex-1">
                        @if($title)
                            <p class="text-sm font-bold text-emerald-800 dark:text-emerald-200">{{ $title }}</p>
                        @endif
                        <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-200">{{ $text }}</p>
                    </div>
                    <button @click="show = false" type="button"
                            class="w-6 h-6 flex items-center justify-center text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 transition-colors">
                        <x-heroicon-s-x-mark class="h-4 w-4"/>
                    </button>
                </div>
            </div>
        @else
            <div class="glass-card-hover relative overflow-hidden rounded-2xl bg-red-500/10 backdrop-blur-sm border border-red-500/20 p-4 shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-red-500/10 to-red-600/10"></div>
                <div class="relative flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-r from-red-500 to-red-600 rounded-lg flex items-center justify-center shadow-lg">
                        <x-heroicon-s-x-mark class="h-5 w-5 text-white"/>
                    </div>
                    <div class="flex-1">
                        @if($title)
                            <p class="text-sm font-bold text-red-800 dark:text-red-200">{{ $title }}</p>
                        @endif
                        <p class="text-sm font-semibold text-red-800 dark:text-red-200">{{ $text }}</p>
                    </div>
                    <button @click="show = false" type="button"
                            class="w-6 h-6 flex items-center justify-center text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                        <x-heroicon-s-x-mark class="h-4 w-4"/>
                    </button>
                </div>
            </div>
        @endif
    </div>
@endif 