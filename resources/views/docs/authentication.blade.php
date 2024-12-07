@extends('layouts.docs')

@section('content')
<div class="prose prose-lg dark:prose-invert max-w-none">
    <h1>Authentication</h1>
    <p class="lead">Learn how to authenticate your requests to the OpenMinetopia API.</p>

    <div class="not-prose">
        <div class="grid gap-8">
            <!-- API Key Authentication -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">API Key Authentication</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Required for all protected endpoints</p>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-5 sm:p-6 space-y-6">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Header Format</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Include your API key in the <code>X-API-Key</code> header with every request to protected endpoints.
                        </p>
                    </div>

                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Example Requests</h3>
                        <div class="mt-4 space-y-4">
                            <x-code-block language="bash" title="cURL">
curl -X POST {{ config('app.url') }}/api/endpoint \
-H "X-API-Key: your-api-key" \
-H "Content-Type: application/json"
                            </x-code-block>

                            <x-code-block language="java" title="Java">
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.net.URI;

public class ApiClient {
    private static final String API_KEY = "your-api-key";
    private static final String BASE_URL = "{{ config('app.url') }}/api";

    public static HttpResponse<String> makeRequest(String endpoint) throws Exception {
        HttpClient client = HttpClient.newHttpClient();
        
        HttpRequest request = HttpRequest.newBuilder()
            .uri(URI.create(BASE_URL + endpoint))
            .header("X-API-Key", API_KEY)
            .header("Content-Type", "application/json")
            .build();

        return client.send(request, HttpResponse.BodyHandlers.ofString());
    }
}
                            </x-code-block>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Error Responses</h3>
                        <div class="mt-4 space-y-4">
                            <x-code-block language="json" title="Missing API Key (401)">
{
    "success": false,
    "message": "Invalid API key.",
    "error_code": "invalid_api_key"
}
                            </x-code-block>
                        </div>
                    </div>

                    <div class="rounded-md bg-yellow-50 dark:bg-yellow-900/50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Security Note</h3>
                                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                    <p>Never share your API key or commit it to version control. Use environment variables to store sensitive credentials.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rate Limiting -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Rate Limiting</h2>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Public endpoints are limited to 60 requests per minute. Protected endpoints have higher limits based on your API key tier.
                    </p>
                    
                    <div class="mt-4">
                        <x-code-block language="json" title="Rate Limit Exceeded (429)">
{
    "success": false,
    "message": "Too Many Requests",
    "error_code": "rate_limit_exceeded",
    "data": {
        "retry_after": 30
    }
}
                        </x-code-block>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 