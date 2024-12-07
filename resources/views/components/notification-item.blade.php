@props(['title', 'description', 'time', 'type' => 'info'])

<div class="py-4">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <span @class([
                'inline-flex h-8 w-8 items-center justify-center rounded-full',
                'bg-blue-100 dark:bg-blue-900' => $type === 'info',
                'bg-green-100 dark:bg-green-900' => $type === 'success',
                'bg-yellow-100 dark:bg-yellow-900' => $type === 'warning',
                'bg-red-100 dark:bg-red-900' => $type === 'error',
            ])>
                @if($type === 'info')
                    <x-heroicon-s-information-circle class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                @elseif($type === 'success')
                    <x-heroicon-s-check-circle class="h-5 w-5 text-green-600 dark:text-green-400" />
                @elseif($type === 'warning')
                    <x-heroicon-s-exclamation-triangle class="h-5 w-5 text-yellow-600 dark:text-yellow-400" />
                @elseif($type === 'error')
                    <x-heroicon-s-x-circle class="h-5 w-5 text-red-600 dark:text-red-400" />
                @endif
            </span>
        </div>
        <div class="ml-4 flex-1">
            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $title }}</p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
            <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">{{ $time }}</p>
        </div>
    </div>
</div>
