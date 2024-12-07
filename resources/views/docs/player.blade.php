@extends('layouts.docs')

@section('content')
<div class="prose prose-lg dark:prose-invert max-w-none">
    <h1>Player Status API</h1>
    <p class="lead">Manage player online status and basic information.</p>

    <div class="not-prose">
        <div class="grid gap-8">
            <!-- Set Player Online Endpoint -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Set Player Online</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">POST /api/player/status/online</p>
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
                            Mark a player as online and update their last login time. This should be called when a player joins the server.
                        </p>
                    </div>

                    <!-- Request Parameters -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Request Parameters</h3>
                        <div class="mt-4">
                            <x-code-block language="json">
{
    "minecraft_uuid": "string, required, 32 chars"
}
                            </x-code-block>
                        </div>
                    </div>

                    <!-- Example Requests -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Example Requests</h3>
                        <div class="mt-4 space-y-4">
                            <x-code-block language="bash" title="cURL">
curl -X POST {{ config('app.url') }}/api/player/status/online \
-H "X-API-Key: your-api-key" \
-H "Content-Type: application/json" \
-d '{
    "minecraft_uuid": "550e8400e29b41d4a716446655440000"
}'
                            </x-code-block>

                            <x-code-block language="java" title="Java">
public void setPlayerOnline(String minecraftUuid) {
    JSONObject requestBody = new JSONObject();
    requestBody.put("minecraft_uuid", minecraftUuid);

    HttpRequest request = HttpRequest.newBuilder()
        .uri(URI.create(BASE_URL + "/player/status/online"))
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
    "message": "Player status updated to online"
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

                            <x-code-block language="json" title="Validation Error (422)">
{
    "success": false,
    "message": "The minecraft uuid must be 32 characters.",
    "error_code": "validation_error"
}
                            </x-code-block>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Set Player Offline Endpoint -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <!-- Similar format for offline endpoint -->
                <!-- Include playtime parameter -->
            </div>
        </div>
    </div>
</div>
@endsection 