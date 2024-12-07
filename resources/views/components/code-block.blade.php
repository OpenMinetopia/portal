@props(['language' => 'json', 'title' => null])

<div x-data="codeBlock" class="relative">
    @if($title)
        <div class="text-sm font-medium text-gray-900 dark:text-white mb-2">{{ $title }}</div>
    @endif
    <div class="relative group">
        <button 
            @click="copyCode($refs.codeContainer)" 
            class="absolute right-2 top-2 p-2 rounded-md bg-gray-800 dark:bg-gray-700 text-white opacity-0 group-hover:opacity-100 transition-opacity"
        >
            <template x-if="!copied">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </template>
            <template x-if="copied">
                <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </template>
        </button>
        <div x-ref="codeContainer" class="bg-gray-800 dark:bg-gray-900 rounded-lg p-4 overflow-x-auto">
            <pre><code class="language-{{ $language }} text-gray-200">{{ $slot }}</code></pre>
        </div>
    </div>
</div> 