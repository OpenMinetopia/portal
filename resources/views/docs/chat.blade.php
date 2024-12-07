@extends('layouts.docs')

@section('content')
<div class="prose prose-lg dark:prose-invert max-w-none">
    <h1 class="text-gray-900 dark:text-white">Chat System API</h1>
    <p class="lead text-gray-600 dark:text-gray-300">Manage chat messages and player prefixes in the Minetopia system.</p>

    <div class="not-prose">
        <div class="grid gap-8">
            <!-- Store Message Endpoint -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Store Message</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">POST /api/chat/message</p>
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
                            Store a chat message. Messages can be global, local (radius-based), or private (specific recipients).
                        </p>
                    </div>

                    <!-- Request Parameters -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Request Parameters</h3>
                        <div class="mt-4">
                            <x-code-block language="json">
{
    "minecraft_uuid": "string, required, 32 chars",
    "message": "string, required",
    "type": "string, required (global, local, private)",
    "radius": "integer, optional (required for local chat)",
    "recipients": "array of minecraft_uuids, optional (required for private chat)",
    "location": {
        "x": "number, optional (required for local chat)",
        "y": "number, optional (required for local chat)",
        "z": "number, optional (required for local chat)",
        "world": "string, optional (required for local chat)"
    }
}
                            </x-code-block>
                        </div>
                    </div>

                    <!-- Example Requests -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Example Requests</h3>
                        <div class="mt-4 space-y-4">
                            <x-code-block language="java" title="Global Chat">
public void sendGlobalMessage(String playerUuid, String message) {
    JSONObject requestBody = new JSONObject();
    requestBody.put("minecraft_uuid", playerUuid);
    requestBody.put("message", message);
    requestBody.put("type", "global");

    HttpRequest request = HttpRequest.newBuilder()
        .uri(URI.create(BASE_URL + "/chat/message"))
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
    "message": "Message stored successfully",
    "data": {
        "message_id": 123,
        "recipients_count": 50
    }
}
                            </x-code-block>
                        </div>
                    </div>

                    <!-- Error Responses -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Error Responses</h3>
                        <div class="mt-4 space-y-4">
                            <x-code-block language="json" title="Chat Cooldown (429)">
{
    "success": false,
    "message": "Chat is on cooldown",
    "error_code": "chat_cooldown",
    "data": {
        "remaining_seconds": 3
    }
}
                            </x-code-block>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Prefix Endpoint -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Update Prefix</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">POST /api/chat/prefix</p>
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
                            Update a player's chat prefix. If no prefix is active, the default_prefix will be used.
                        </p>
                    </div>

                    <!-- Request Parameters -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Request Parameters</h3>
                        <div class="mt-4">
                            <x-code-block language="json">
{
    "minecraft_uuid": "string, required, 32 chars",
    "prefix": "string, required",
    "color": "string, optional (hex color or color code)",
    "is_active": "boolean, optional (defaults to true)",
    "is_default": "boolean, optional (set as default_prefix)"
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
    "message": "Prefix updated successfully",
    "data": {
        "prefix": "[VIP]",
        "formatted_prefix": "§6[VIP]"
    }
}
                            </x-code-block>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 