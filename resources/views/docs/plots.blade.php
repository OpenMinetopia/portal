@extends('layouts.docs')

@section('content')
<div class="prose prose-lg dark:prose-invert max-w-none">
    <h1 class="text-gray-900 dark:text-white">Plots API</h1>
    <p class="lead text-gray-600 dark:text-gray-300">Manage plots and plot memberships in the Minetopia system.</p>

    <div class="not-prose">
        <div class="grid gap-8">
            <!-- Create Plot Endpoint -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Create Plot</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">POST /api/plots</p>
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
                            Create a new plot for a player. A plot represents a protected area in the world where only members can build.
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
    "type": "string, required (residential, commercial, etc.)",
    "corners": {
        "corner1": {
            "x": "integer, required",
            "y": "integer, required",
            "z": "integer, required",
            "world": "string, required"
        },
        "corner2": {
            "x": "integer, required",
            "y": "integer, required",
            "z": "integer, required",
            "world": "string, required"
        }
    },
    "settings": {
        "pvp_enabled": "boolean, optional",
        "public_access": "boolean, optional",
        "build_rights": "string, optional (owner_only, members, all)"
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
public void createPlot(String playerUuid, String name, Location corner1, Location corner2, String type) {
    JSONObject requestBody = new JSONObject();
    requestBody.put("minecraft_uuid", playerUuid);
    requestBody.put("name", name);
    requestBody.put("type", type);
    
    JSONObject corners = new JSONObject();
    JSONObject c1 = new JSONObject();
    c1.put("x", corner1.getBlockX());
    c1.put("y", corner1.getBlockY());
    c1.put("z", corner1.getBlockZ());
    c1.put("world", corner1.getWorld().getName());
    corners.put("corner1", c1);
    
    JSONObject c2 = new JSONObject();
    c2.put("x", corner2.getBlockX());
    c2.put("y", corner2.getBlockY());
    c2.put("z", corner2.getBlockZ());
    c2.put("world", corner2.getWorld().getName());
    corners.put("corner2", c2);
    
    requestBody.put("corners", corners);

    HttpRequest request = HttpRequest.newBuilder()
        .uri(URI.create(BASE_URL + "/plots"))
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
    "message": "Plot created successfully",
    "data": {
        "plot_id": 123,
        "area": 100,
        "monthly_cost": 1000,
        "settings": {
            "pvp_enabled": false,
            "public_access": false,
            "build_rights": "members"
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
                            <x-code-block language="json" title="Plot Overlap (400)">
{
    "success": false,
    "message": "Plot overlaps with existing plot",
    "error_code": "plot_overlap"
}
                            </x-code-block>

                            <x-code-block language="json" title="Invalid World (400)">
{
    "success": false,
    "message": "Invalid world specified",
    "error_code": "invalid_world"
}
                            </x-code-block>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Member Endpoint -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Add Plot Member</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">POST /api/plots/{id}/members</p>
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
                            Add a member to a plot. Only the plot owner can add members.
                        </p>
                    </div>

                    <!-- Request Parameters -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Request Parameters</h3>
                        <div class="mt-4">
                            <x-code-block language="json">
{
    "minecraft_uuid": "string, required, 32 chars",
    "role": "string, optional (default: member)"
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
    "message": "Member added successfully",
    "data": {
        "plot_id": 123,
        "member_count": 3
    }
}
                            </x-code-block>
                        </div>
                    </div>

                    <!-- Error Responses -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Error Responses</h3>
                        <div class="mt-4 space-y-4">
                            <x-code-block language="json" title="Not Plot Owner (403)">
{
    "success": false,
    "message": "Only the plot owner can add members",
    "error_code": "not_owner"
}
                            </x-code-block>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Remove Member Endpoint -->
            <!-- Similar format for remove member endpoint -->

            <!-- Update Plot Settings Endpoint -->
            <!-- Similar format for update settings endpoint -->
        </div>
    </div>
</div>
@endsection 