@extends('layouts.docs')

@section('content')
<div class="prose prose-lg dark:prose-invert max-w-none">
    <h1>OpenMinetopia API Documentation</h1>
    
    <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg mb-8">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Base URL</h3>
                <div class="mt-2 text-sm text-blue-700 dark:text-blue-200">
                    <code class="bg-blue-100 dark:bg-blue-800 px-2 py-1 rounded">{{ config('app.url') }}/api</code>
                </div>
            </div>
        </div>
    </div>

    <h2>Getting Started</h2>
    <p>
        The OpenMinetopia API provides a comprehensive set of endpoints to integrate Minecraft servers with our platform.
        All API calls require authentication using an API key, except for public endpoints.
    </p>

    <h3>Authentication</h3>
    <p>
        To authenticate your requests, include your API key in the <code>X-API-Key</code> header:
    </p>

    <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg my-4">
        <pre><code class="language-bash">curl -X POST {{ config('app.url') }}/api/endpoint \
-H "X-API-Key: your-api-key" \
-H "Content-Type: application/json"</code></pre>
    </div>

    <h3>Response Format</h3>
    <p>All API responses follow this standard format:</p>

    <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg my-4">
        <pre><code class="language-json">{
    "success": true|false,
    "message": "A human-readable message",
    "error_code": "error_code_if_applicable",
    "data": {
        // Response data when applicable
    }
}</code></pre>
    </div>

    <h3>Common Error Codes</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800">
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Error Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">invalid_api_key</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">The provided API key is invalid or missing</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">user_not_found</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">The specified user could not be found</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">validation_error</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">The request data failed validation</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">server_error</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">An internal server error occurred</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection 