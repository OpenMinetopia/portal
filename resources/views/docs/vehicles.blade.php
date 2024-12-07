@extends('layouts.docs')

@section('content')
<div class="prose prose-lg dark:prose-invert max-w-none">
    <h1 class="text-gray-900 dark:text-white">Vehicles API</h1>
    <p class="lead text-gray-600 dark:text-gray-300">Manage player vehicles in the Minetopia system.</p>

    <div class="not-prose">
        <div class="grid gap-8">
            <!-- Create Vehicle Endpoint -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Create Vehicle</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">POST /api/vehicles</p>
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
                            Create a new vehicle for a player. Each vehicle has a type, model, and can store custom data.
                        </p>
                    </div>

                    <!-- Request Parameters -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Request Parameters</h3>
                        <div class="mt-4">
                            <x-code-block language="json">
{
    "minecraft_uuid": "string, required, 32 chars",
    "type": "string, required (car, motorcycle, etc.)",
    "model": "string, required",
    "properties": {
        "color": "string, optional",
        "license_plate": "string, optional",
        "upgrades": "array, optional",
        "fuel_level": "integer, optional",
        "max_speed": "integer, optional"
    },
    "location": {
        "x": "number, required",
        "y": "number, required",
        "z": "number, required",
        "world": "string, required"
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
public void createVehicle(String playerUuid, String type, String model, Location location) {
    JSONObject requestBody = new JSONObject();
    requestBody.put("minecraft_uuid", playerUuid);
    requestBody.put("type", type);
    requestBody.put("model", model);
    
    JSONObject locationObj = new JSONObject();
    locationObj.put("x", location.getX());
    locationObj.put("y", location.getY());
    locationObj.put("z", location.getZ());
    locationObj.put("world", location.getWorld().getName());
    requestBody.put("location", locationObj);

    HttpRequest request = HttpRequest.newBuilder()
        .uri(URI.create(BASE_URL + "/vehicles"))
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
    "message": "Vehicle created successfully",
    "data": {
        "vehicle_id": 123,
        "license_plate": "ABC-123",
        "spawn_location": {
            "x": 100,
            "y": 64,
            "z": -200,
            "world": "world"
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
                            <x-code-block language="json" title="Invalid Vehicle Type (400)">
{
    "success": false,
    "message": "Invalid vehicle type specified",
    "error_code": "invalid_vehicle_type"
}
                            </x-code-block>

                            <x-code-block language="json" title="Vehicle Limit Reached (400)">
{
    "success": false,
    "message": "Player has reached maximum vehicle limit",
    "error_code": "vehicle_limit_reached"
}
                            </x-code-block>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Vehicle Endpoint -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Update Vehicle</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">PUT /api/vehicles/{id}</p>
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
                            Update an existing vehicle's properties or location.
                        </p>
                    </div>

                    <!-- Request Parameters -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Request Parameters</h3>
                        <div class="mt-4">
                            <x-code-block language="json">
{
    "properties": {
        "color": "string, optional",
        "fuel_level": "integer, optional",
        "upgrades": "array, optional"
    },
    "location": {
        "x": "number, optional",
        "y": "number, optional",
        "z": "number, optional",
        "world": "string, optional"
    }
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
    "message": "Vehicle updated successfully",
    "data": {
        "vehicle_id": 123,
        "updated_properties": ["color", "fuel_level"]
    }
}
                            </x-code-block>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Vehicle Endpoint -->
            <!-- Similar format for delete endpoint -->
        </div>
    </div>
</div>
@endsection 