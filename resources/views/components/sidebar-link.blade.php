<a href="{{ $href }}" 
   class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ $active 
        ? 'bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white' 
        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900/50 hover:text-gray-900 dark:hover:text-white'
    }}">
    @if(isset($icon))
        {{ $icon }}
    @endif
    {{ $slot }}
</a> 