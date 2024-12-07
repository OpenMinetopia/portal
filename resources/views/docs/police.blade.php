@extends('layouts.docs')

@section('content')
<div class="prose prose-lg dark:prose-invert max-w-none">
    <h1 class="text-gray-900 dark:text-white">Police System API</h1>
    <p class="lead text-gray-600 dark:text-gray-300">Manage arrests, fines, and security items in the police system.</p>

    <div class="not-prose">
        <div class="grid gap-8">
            <!-- Create Arrest Endpoint -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Create Arrest</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">POST /api/police/arrests</p>
                        </div>
                        <span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-900 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-300">
                            Requires API Key
                        </span>
                    </div>
                </div>

                <div class="px-4 py-5 sm:p-6 space-y-6">
                    <!-- Description -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Description</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Create a new arrest record. Only police officers can create arrests. The duration is specified in seconds.
                        </p>
                    </div>

                    <!-- Request Parameters -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Request Parameters</h3>
                        <div class="mt-4">
                            <x-code-block language="json">
{
    "minecraft_uuid": "string, required, 32 chars",
    "officer_uuid": "string, required, 32 chars",
    "reason": "string, required",
    "duration": "integer, required, minimum: 1 (seconds)"
}
                            </x-code-block>
                        </div>
                    </div>

                    <!-- Example Requests -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Example Requests</h3>
                        <div class="mt-4 space-y-4">
                            <x-code-block language="bash" title="cURL">
curl -X POST {{ config('app.url') }}/api/police/arrests \
-H "X-API-Key: your-api-key" \
-H "Content-Type: application/json" \
-d '{
    "minecraft_uuid": "550e8400e29b41d4a716446655440000",
    "officer_uuid": "550e8400e29b41d4a716446655440001",
    "reason": "Theft of vehicle",
    "duration": 300
}'
                            </x-code-block>

                            <x-code-block language="java" title="Java">
public void arrestPlayer(String playerUuid, String reason, int duration) {
    JSONObject requestBody = new JSONObject();
    requestBody.put("minecraft_uuid", playerUuid);
    requestBody.put("officer_uuid", getCurrentOfficerUuid());
    requestBody.put("reason", reason);
    requestBody.put("duration", duration);

    HttpRequest request = HttpRequest.newBuilder()
        .uri(URI.create(BASE_URL + "/police/arrests"))
        .header("X-API-Key", API_KEY)
        .header("Content-Type", "application/json")
        .POST(HttpRequest.BodyPublishers.ofString(requestBody.toString()))
        .build();

    HttpResponse<String> response = client.send(request, HttpResponse.BodyHandlers.ofString());
}
                            </x-code-block>
                        </div>
                    </div>

                    <!-- Success Response -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Success Response</h3>
                        <div class="mt-4">
                            <x-code-block language="json">
{
    "success": true,
    "message": "Arrest recorded successfully",
    "data": {
        "arrest_id": 123,
        "release_time": "2024-03-27T15:30:00Z"
    }
}
                            </x-code-block>
                        </div>
                    </div>

                    <!-- Error Responses -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Error Responses</h3>
                        <div class="mt-4 space-y-4">
                            <x-code-block language="json" title="User Not Found (404)">
{
    "success": false,
    "message": "User not found",
    "error_code": "user_not_found"
}
                            </x-code-block>

                            <x-code-block language="json" title="Not Authorized (403)">
{
    "success": false,
    "message": "User is not a police officer",
    "error_code": "not_authorized"
}
                            </x-code-block>

                            <x-code-block language="json" title="Validation Error (422)">
{
    "success": false,
    "message": "The duration must be at least 1 second.",
    "error_code": "validation_error"
}
                            </x-code-block>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="rounded-md bg-blue-50 dark:bg-blue-900/50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Release Time</h3>
                                <div class="mt-2 text-sm text-blue-700 dark:text-blue-200">
                                    <p>The release time is automatically calculated based on the duration. Players will be automatically released when this time is reached.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Release Arrest Endpoint -->
            <!-- Similar format for release endpoint -->

            <!-- Create Fine Endpoint -->
            <!-- Similar format for fine endpoint -->
        </div>
    </div>
</div>
@endsection 