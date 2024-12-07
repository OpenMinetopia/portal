@extends('layouts.docs')

@section('content')
<div class="prose prose-lg dark:prose-invert max-w-none">
    <h1 class="text-gray-900 dark:text-white">Teleporters API</h1>
    <p class="lead text-gray-600 dark:text-gray-300">Manage teleporter locations and functionality in the Minetopia system.</p>

    <div class="not-prose">
        <div class="grid gap-8">
            <!-- Create Teleporter Endpoint -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Create Teleporter</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">POST /api/teleporters</p>
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
                            Create a new teleporter location. Teleporters allow players to quickly move between predefined locations.
                        </p>
                    </div>

                    <!-- Request Parameters -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Request Parameters</h3>
                        <div class="mt-4">
                            <x-code-block language="json">
{
    "minecraft_uuid": "string, required, 32 chars",
    "name": "string, required",
    "location": {
        "x": "number, required",
        "y": "number, required",
        "z": "number, required",
        "world": "string, required"
    },
    "display_lines": [
        "string, array of text lines to display",
        "example: Welcome to the Mall",
        "example: Click to teleport"
    ],
    "is_active": "boolean, optional (defaults to true)"
}
                            </x-code-block>
                        </div>
                    </div>

                    <!-- Example Requests -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Example Requests</h3>
                        <div class="mt-4 space-y-4">
                            <x-code-block language="java" title="Java">
public void createTeleporter(String name, Location location, List<String> displayLines) {
    JSONObject requestBody = new JSONObject();
    requestBody.put("minecraft_uuid", getCurrentPlayerUuid());
    requestBody.put("name", name);
    
    JSONObject locationObj = new JSONObject();
    locationObj.put("x", location.getX());
    locationObj.put("y", location.getY());
    locationObj.put("z", location.getZ());
    locationObj.put("world", location.getWorld().getName());
    requestBody.put("location", locationObj);
    
    requestBody.put("display_lines", new JSONArray(displayLines));
    requestBody.put("is_active", true);

    HttpRequest request = HttpRequest.newBuilder()
        .uri(URI.create(BASE_URL + "/teleporters"))
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
    "message": "Teleporter created successfully",
    "data": {
        "teleporter_id": 123
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
    "message": "The name field is required",
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
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Display Lines</h3>
                                <div class="mt-2 text-sm text-blue-700 dark:text-blue-200">
                                    <p>Display lines are shown as holograms above the teleporter location. Each line can include color codes using the & symbol.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Teleporter Endpoint -->
            <!-- Similar format for update endpoint -->

            <!-- Delete Teleporter Endpoint -->
            <!-- Similar format for delete endpoint -->
        </div>
    </div>
</div>
@endsection 