@props(['active' => false])

<li>
    <div class="flex items-center">
        <svg class="h-full w-6 flex-shrink-0 text-gray-200 dark:text-gray-600" viewBox="0 0 24 44" preserveAspectRatio="none" fill="currentColor" aria-hidden="true">
            <path d="M.293 0l22 22-22 22h1.414l22-22-22-22H.293z" />
        </svg>
        <{{ $active ? 'span' : 'a' }} 
            {{ $attributes->merge([
                'class' => 'ml-4 text-sm font-medium ' . 
                ($active 
                    ? 'text-gray-700 dark:text-gray-200' 
                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'
                )
            ]) }}
        >
            {{ $slot }}
        </{{ $active ? 'span' : 'a' }}>
    </div>
</li> 