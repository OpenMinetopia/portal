@extends('layouts.docs')

@section('content')
<div class="prose prose-lg dark:prose-invert max-w-none">
    <h1 class="text-gray-900 dark:text-white">Fitness System API</h1>
    <p class="lead text-gray-600 dark:text-gray-300">Manage player fitness and health statistics in the Minetopia system.</p>

    <div class="not-prose">
        <div class="grid gap-8">
            <!-- Update Fitness Endpoint -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Update Fitness</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">POST /api/fitness/update</p>
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
                            Update a player's fitness statistics. This includes various health-related statistics that affect gameplay.
                        </p>
                    </div>

                    <!-- Request Parameters -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Request Parameters</h3>
                        <div class="mt-4">
                            <x-code-block language="json">
{
    "minecraft_uuid": "string, required, 32 chars",
    "total_fitness": "integer, required, min: 0",
    "max_fitness": "integer, required, min: 0",
    "current_status": "string, optional",
    "statistics": {
        "health": "integer, required, min: 0, max: 100",
        "stamina": "integer, optional, min: 0, max: 100",
        "strength": "integer, optional, min: 0, max: 100"
        // Additional statistics can be added
    }
}
                            </x-code-block>
                        </div>
                    </div>

                    <!-- Example Requests -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Example Requests</h3>
                        <div class="mt-4 space-y-4">
                            <x-code-block language="bash" title="cURL">
curl -X POST {{ config('app.url') }}/api/fitness/update \
-H "X-API-Key: your-api-key" \
-H "Content-Type: application/json" \
-d '{
    "minecraft_uuid": "550e8400e29b41d4a716446655440000",
    "total_fitness": 75,
    "max_fitness": 100,
    "current_status": "exercising",
    "statistics": {
        "health": 85,
        "stamina": 70,
        "strength": 60
    }
}'
                            </x-code-block>

                            <x-code-block language="java" title="Java">
public void updateFitness(String minecraftUuid, FitnessData data) {
    JSONObject requestBody = new JSONObject();
    requestBody.put("minecraft_uuid", minecraftUuid);
    requestBody.put("total_fitness", data.getTotalFitness());
    requestBody.put("max_fitness", data.getMaxFitness());
    requestBody.put("current_status", data.getCurrentStatus());
    
    JSONObject statistics = new JSONObject();
    statistics.put("health", data.getHealth());
    statistics.put("stamina", data.getStamina());
    statistics.put("strength", data.getStrength());
    requestBody.put("statistics", statistics);

    HttpRequest request = HttpRequest.newBuilder()
        .uri(URI.create(BASE_URL + "/fitness/update"))
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
    "message": "Fitness updated successfully",
    "data": {
        "total_fitness": 75,
        "max_fitness": 100,
        "percentage": 75,
        "statistics": {
            "health": 85,
            "stamina": 70,
            "strength": 60
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
    "message": "The health statistic must be between 0 and 100",
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
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Statistics System</h3>
                                <div class="mt-2 text-sm text-blue-700 dark:text-blue-200">
                                    <p>The statistics system is flexible and can handle various health-related metrics. The health statistic is required, but additional statistics can be added as needed.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Get Fitness Endpoint -->
            <!-- Similar format for get endpoint -->
        </div>
    </div>
</div>
@endsection 