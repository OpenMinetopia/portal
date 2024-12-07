@extends('layouts.docs')

@section('content')
<div class="prose prose-lg dark:prose-invert max-w-none">
    <h1 class="text-gray-900 dark:text-white">Emergency System API</h1>
    <p class="lead text-gray-600 dark:text-gray-300">Manage emergency calls and responses in the Minetopia system.</p>

    <div class="not-prose">
        <div class="grid gap-8">
            <!-- Create Emergency Call Endpoint -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Create Emergency Call</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">POST /api/emergency/calls</p>
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
                            Create a new emergency call. This endpoint should be used when a player requests emergency assistance.
                            The location is stored to help responders find the caller.
                        </p>
                    </div>

                    <!-- Request Parameters -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Request Parameters</h3>
                        <div class="mt-4">
                            <x-code-block language="json">
{
    "minecraft_uuid": "string, required, 32 chars",
    "location": {
        "x": "number, required",
        "y": "number, required",
        "z": "number, required",
        "world": "string, required"
    },
    "message": "string, required"
}
                            </x-code-block>
                        </div>
                    </div>

                    <!-- Example Requests -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Example Requests</h3>
                        <div class="mt-4 space-y-4">
                            <x-code-block language="bash" title="cURL">
curl -X POST {{ config('app.url') }}/api/emergency/calls \
-H "X-API-Key: your-api-key" \
-H "Content-Type: application/json" \
-d '{
    "minecraft_uuid": "550e8400e29b41d4a716446655440000",
    "location": {
        "x": 100,
        "y": 64,
        "z": -200,
        "world": "world"
    },
    "message": "Help! Someone is robbing my store!"
}'
                            </x-code-block>

                            <x-code-block language="java" title="Java">
public void createEmergencyCall(String minecraftUuid, Location location, String message) {
    JSONObject requestBody = new JSONObject();
    requestBody.put("minecraft_uuid", minecraftUuid);
    
    JSONObject locationObj = new JSONObject();
    locationObj.put("x", location.getX());
    locationObj.put("y", location.getY());
    locationObj.put("z", location.getZ());
    locationObj.put("world", location.getWorld().getName());
    
    requestBody.put("location", locationObj);
    requestBody.put("message", message);

    HttpRequest request = HttpRequest.newBuilder()
        .uri(URI.create(BASE_URL + "/emergency/calls"))
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
    "message": "Emergency call created",
    "data": {
        "call_id": 123
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

                            <x-code-block language="json" title="Validation Error (422)">
{
    "success": false,
    "message": "The location field is required.",
    "error_code": "validation_error"
}
                            </x-code-block>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="rounded-md bg-yellow-50 dark:bg-yellow-900/50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Rate Limiting</h3>
                                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                    <p>Emergency calls are rate limited to prevent abuse. Users can only create one emergency call every 5 minutes.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Respond to Emergency Call Endpoint -->
            <!-- Similar format for respond endpoint -->

            <!-- Close Emergency Call Endpoint -->
            <!-- Similar format for close endpoint -->
        </div>
    </div>
</div>
@endsection 