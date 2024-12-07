@extends('layouts.docs')

@section('content')
<div class="prose prose-lg dark:prose-invert max-w-none">
    <h1 class="text-gray-900 dark:text-white">Economy API</h1>
    <p class="lead text-gray-600 dark:text-gray-300">Manage player balances, transactions, and bank accounts in the Minetopia system.</p>

    <div class="not-prose">
        <div class="grid gap-8">
            <!-- Update Balance Endpoint -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Update Balance</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">POST /api/economy/balance</p>
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
                            Update a player's balance by adding, subtracting, or setting a specific amount. All transactions are logged for auditing purposes.
                        </p>
                    </div>

                    <!-- Request Parameters -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Request Parameters</h3>
                        <div class="mt-4">
                            <x-code-block language="json">
{
    "minecraft_uuid": "string, required, 32 chars",
    "amount": "integer, required",
    "operation": "string, required (add, subtract, set)",
    "reason": "string, required",
    "transaction_type": "string, required (salary, purchase, fine, etc.)"
}
                            </x-code-block>
                        </div>
                    </div>

                    <!-- Example Requests -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Example Requests</h3>
                        <div class="mt-4 space-y-4">
                            <x-code-block language="java" title="Java">
public void updateBalance(String playerUuid, int amount, String operation, String reason) {
    JSONObject requestBody = new JSONObject();
    requestBody.put("minecraft_uuid", playerUuid);
    requestBody.put("amount", amount);
    requestBody.put("operation", operation);
    requestBody.put("reason", reason);
    requestBody.put("transaction_type", "salary");

    HttpRequest request = HttpRequest.newBuilder()
        .uri(URI.create(BASE_URL + "/economy/balance"))
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
    "message": "Balance updated successfully",
    "data": {
        "old_balance": 1000,
        "new_balance": 1500,
        "transaction_id": 123
    }
}
                            </x-code-block>
                        </div>
                    </div>

                    <!-- Error Responses -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Error Responses</h3>
                        <div class="mt-4 space-y-4">
                            <x-code-block language="json" title="Insufficient Funds (400)">
{
    "success": false,
    "message": "Insufficient funds for transaction",
    "error_code": "insufficient_funds"
}
                            </x-code-block>

                            <x-code-block language="json" title="Invalid Operation (400)">
{
    "success": false,
    "message": "Invalid operation type",
    "error_code": "invalid_operation"
}
                            </x-code-block>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Get Balance Endpoint -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-5 border-b border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Get Balance</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">GET /api/economy/balance/{minecraft_uuid}</p>
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
                            Get a player's current balance and recent transactions.
                        </p>
                    </div>

                    <!-- Example Requests -->
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Example Requests</h3>
                        <div class="mt-4 space-y-4">
                            <x-code-block language="java" title="Java">
public JSONObject getBalance(String playerUuid) {
    HttpRequest request = HttpRequest.newBuilder()
        .uri(URI.create(BASE_URL + "/economy/balance/" + playerUuid))
        .header("X-API-Key", API_KEY)
        .GET()
        .build();

    HttpResponse<String> response = client.send(request, HttpResponse.BodyHandlers.ofString());
    return new JSONObject(response.body());
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
        "balance": 1500,
        "recent_transactions": [
            {
                "id": 123,
                "amount": 500,
                "type": "salary",
                "timestamp": "2024-03-27T15:30:00Z"
            }
        ]
    }
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
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Transaction History</h3>
                                <div class="mt-2 text-sm text-blue-700 dark:text-blue-200">
                                    <p>The system maintains a complete history of all transactions. Recent transactions are included in the balance response by default.</p>
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