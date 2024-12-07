@extends('layouts.docs')

@section('content')
<div class="prose prose-lg dark:prose-invert max-w-none">
    <h1 class="text-gray-900 dark:text-white">Detection Gates API</h1>
    <p class="lead text-gray-600 dark:text-gray-300">Manage security detection gates and item scanning in the Minetopia system.</p>

    <div class="not-prose">
        <div class="grid gap-8">
            <!-- Create Detection Gate Endpoint -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Create Detection Gate</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">POST /api/police/gates</p>
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
                            Create a new detection gate. Only police officers can create gates. Gates can detect specific materials and items when players pass through them.
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
    "flagged_materials": [
        "string, array of material names",
        "example: DIAMOND_SWORD",
        "example: TNT"
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
public void createDetectionGate(Location location, List<String> flaggedMaterials) {
    JSONObject requestBody = new JSONObject();
    requestBody.put("minecraft_uuid", getCurrentOfficerUuid());
    
    JSONObject locationObj = new JSONObject();
    locationObj.put("x", location.getX());
    locationObj.put("y", location.getY());
    locationObj.put("z", location.getZ());
    locationObj.put("world", location.getWorld().getName());
    requestBody.put("location", locationObj);
    
    requestBody.put("flagged_materials", new JSONArray(flaggedMaterials));
    requestBody.put("is_active", true);

    HttpRequest request = HttpRequest.newBuilder()
        .uri(URI.create(BASE_URL + "/police/gates"))
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
    "message": "Detection gate created successfully",
    "data": {
        "gate_id": 123
    }
}
                            </x-code-block>
                        </div>
                    </div>

                    <!-- Error Responses -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Error Responses</h3>
                        <div class="mt-4 space-y-4">
                            <x-code-block language="json" title="Not Authorized (403)">
{
    "success": false,
    "message": "Not authorized to create detection gates",
    "error_code": "not_authorized"
}
                            </x-code-block>

                            <x-code-block language="json" title="Validation Error (422)">
{
    "success": false,
    "message": "The flagged materials field is required",
    "error_code": "validation_error"
}
                            </x-code-block>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Log Detection Endpoint -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Log Detection</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">POST /api/police/gates/log</p>
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
                            Log when a player passes through a detection gate with flagged items.
                        </p>
                    </div>

                    <!-- Request Parameters -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Request Parameters</h3>
                        <div class="mt-4">
                            <x-code-block language="json">
{
    "minecraft_uuid": "string, required, 32 chars",
    "gate_id": "integer, required",
    "detected_items": [
        {
            "material": "string, required",
            "amount": "integer, required"
        }
    ]
}
                            </x-code-block>
                        </div>
                    </div>

                    <!-- Example Requests -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Example Requests</h3>
                        <div class="mt-4 space-y-4">
                            <x-code-block language="java" title="Java">
public void logDetection(String playerUuid, int gateId, Map<String, Integer> detectedItems) {
    JSONObject requestBody = new JSONObject();
    requestBody.put("minecraft_uuid", playerUuid);
    requestBody.put("gate_id", gateId);
    
    JSONArray items = new JSONArray();
    detectedItems.forEach((material, amount) -> {
        JSONObject item = new JSONObject();
        item.put("material", material);
        item.put("amount", amount);
        items.put(item);
    });
    requestBody.put("detected_items", items);

    HttpRequest request = HttpRequest.newBuilder()
        .uri(URI.create(BASE_URL + "/police/gates/log"))
        .header("X-API-Key", API_KEY)
        .header("Content-Type", "application/json")
        .POST(HttpRequest.BodyPublishers.ofString(requestBody.toString()))
        .build();

    HttpResponse<String> response = client.send(request, HttpResponse.BodyHandlers.ofString());
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
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Detection System</h3>
                                <div class="mt-2 text-sm text-blue-700 dark:text-blue-200">
                                    <p>Detection gates will only trigger for items that match the flagged_materials list. The system is case-sensitive for material names.</p>
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