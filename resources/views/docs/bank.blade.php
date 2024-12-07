@extends('layouts.docs')

@section('content')
<div class="prose prose-lg dark:prose-invert max-w-none">
    <h1 class="text-gray-900 dark:text-white">Bank & Economy API</h1>
    <p class="lead text-gray-600 dark:text-gray-300">Manage bank accounts, transactions, and player balances in the Minetopia system.</p>

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
                            Update a player's bank balance. This endpoint handles deposits, withdrawals, and direct balance updates.
                            All transactions are logged for auditing purposes.
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
    "operation": "string, required (deposit, withdraw, set)",
    "reason": "string, required",
    "transaction_type": "string, required (salary, purchase, fine, rent, etc.)",
    "metadata": {
        "source": "string, optional (plot_id, vehicle_id, etc.)",
        "reference": "string, optional (transaction reference)"
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
public void updateBalance(String playerUuid, int amount, String operation, String reason) {
    JSONObject requestBody = new JSONObject();
    requestBody.put("minecraft_uuid", playerUuid);
    requestBody.put("amount", amount);
    requestBody.put("operation", operation);
    requestBody.put("reason", reason);
    requestBody.put("transaction_type", "salary");

    JSONObject metadata = new JSONObject();
    metadata.put("source", "monthly_salary");
    requestBody.put("metadata", metadata);

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
        "transaction_id": 123,
        "transaction_details": {
            "type": "salary",
            "timestamp": "2024-03-27T15:30:00Z",
            "reference": "SALARY-123"
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
                            <x-code-block language="json" title="Insufficient Funds (400)">
{
    "success": false,
    "message": "Insufficient funds for withdrawal",
    "error_code": "insufficient_funds",
    "data": {
        "current_balance": 100,
        "required_amount": 500
    }
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

                    <!-- Notes -->
                    <div class="rounded-md bg-blue-50 dark:bg-blue-900/50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Transaction Types</h3>
                                <div class="mt-2 text-sm text-blue-700 dark:text-blue-200">
                                    <p>Valid transaction types include:</p>
                                    <ul class="list-disc ml-4 mt-2">
                                        <li>salary - Monthly or periodic salary payments</li>
                                        <li>purchase - Item or service purchases</li>
                                        <li>fine - Police fines and penalties</li>
                                        <li>rent - Plot and property rent payments</li>
                                        <li>transfer - Player-to-player transfers</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Get Balance Endpoint -->
            <!-- Similar format for get balance endpoint -->

            <!-- Get Transaction History Endpoint -->
            <!-- Similar format for transaction history endpoint -->
        </div>
    </div>
</div>
@endsection 