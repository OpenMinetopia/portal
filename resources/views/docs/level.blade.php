@extends('layouts.docs')

@section('content')
<div class="prose prose-lg dark:prose-invert max-w-none">
    <h1 class="text-gray-900 dark:text-white">Level & Progress API</h1>
    <p class="lead text-gray-600 dark:text-gray-300">Manage player levels and progression in the Minetopia system.</p>

    <div class="not-prose">
        <div class="grid gap-8">
            <!-- Update Progress Endpoint -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Update Progress</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">POST /api/level/progress</p>
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
                            Update a player's level progress by providing points from various sources. The total points will be used to calculate the player's level.
                        </p>
                    </div>

                    <!-- Request Parameters -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Request Parameters</h3>
                        <div class="mt-4">
                            <x-code-block language="json">
{
    "minecraft_uuid": "string, required, 32 chars",
    "points": {
        "plots": "integer, optional, min: 0",
        "balance": "integer, optional, min: 0",
        "vehicles": "integer, optional, min: 0",
        "prefix": "integer, optional, min: 0",
        "playtime": "integer, optional, min: 0",
        "fitness": "integer, optional, min: 0"
    }
}
                            </x-code-block>
                        </div>
                    </div>

                    <!-- Example Requests -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Example Requests</h3>
                        <div class="mt-4 space-y-4">
                            <x-code-block language="java" title="Java">
public void updateProgress(String playerUuid, Map<String, Integer> points) {
    JSONObject requestBody = new JSONObject();
    requestBody.put("minecraft_uuid", playerUuid);
    requestBody.put("points", new JSONObject(points));

    HttpRequest request = HttpRequest.newBuilder()
        .uri(URI.create(BASE_URL + "/level/progress"))
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
    "message": "Progress updated successfully",
    "data": {
        "current_level": 5,
        "total_points": 450,
        "points_to_next_level": 50,
        "progress_percentage": 90,
        "points_breakdown": {
            "plots": 100,
            "balance": 150,
            "vehicles": 50,
            "prefix": 50,
            "playtime": 50,
            "fitness": 50
        }
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
    "message": "Points must be non-negative",
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
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Point Sources</h3>
                                <div class="mt-2 text-sm text-blue-700 dark:text-blue-200">
                                    <p>Points can come from various sources. Each source is optional, but at least one source must provide points. The total points determine the player's level according to the level calculation formula.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calculate Level Endpoint -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Calculate Level</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">POST /api/level/calculate</p>
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
                            Calculate a player's level based on their current progress without updating it.
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

                    <!-- Success Response -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Success Response</h3>
                        <div class="mt-4">
                            <x-code-block language="json">
{
    "success": true,
    "data": {
        "current_level": 5,
        "total_points": 450,
        "points_to_next_level": 50,
        "progress_percentage": 90
    }
}
                            </x-code-block>
                        </div>
                    </div>

                    <!-- Level Calculation Formula -->
                    <div class="rounded-md bg-green-50 dark:bg-green-900/50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800 dark:text-green-300">Level Calculation Formula</h3>
                                <div class="mt-2 text-sm text-green-700 dark:text-green-200">
                                    <p>The level is calculated using the following formula:</p>
                                    <ul class="list-disc ml-4 mt-2">
                                        <li>Level 1: 0-100 points</li>
                                        <li>Level 2: 101-250 points</li>
                                        <li>Level 3: 251-450 points</li>
                                        <li>Level 4: 451-700 points</li>
                                        <li>Level 5: 701-1000 points</li>
                                        <li>And so on...</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 