<?php

namespace App\Services\Plugin;

use App\Services\MojangApiService;

class BankingService
{
    protected PluginApiService $apiService;
    protected MojangApiService $mojangApi;

    public function __construct(PluginApiService $apiService, MojangApiService $mojangApi)
    {
        $this->apiService = $apiService;
        $this->mojangApi = $mojangApi;
    }

    /**
     * Get bank account details.
     *
     * @param string $uuid
     * @return array
     */
    public function getBankAccount(string $uuid): array
    {
        $data = $this->apiService->get("/api/bankaccount/{$uuid}");
        return $data ?? [];
    }

    /**
     * Get bank account users.
     *
     * @param string $uuid
     * @return array
     */
    public function getBankAccountUsers(string $uuid): array
    {
        $data = $this->apiService->get("/api/bankaccount/{$uuid}/users");

        if (!isset($data['users'])) {
            return [];
        }

        $users = [];
        foreach ($data['users'] as $userUuid => $userData) {
            $users[] = [
                'uuid' => $userUuid,
                'permission' => $userData['permission'],
                'owner' => $userData['permission'] === 'ADMIN'
            ];
        }

        // Sort users so owners appear first
        return collect($users)
            ->sortByDesc('owner')
            ->values()
            ->toArray();
    }

    /**
     * Get all bank accounts by collecting from all registered users.
     *
     * @return array
     */
    public function getAllBankAccounts(): array
    {
        $users = \App\Models\User::where('minecraft_verified', true)->get();

        $allAccounts = collect();

        foreach ($users as $user) {
            $accounts = $this->getPlayerBankAccounts($user->minecraft_uuid);
            $allAccounts = $allAccounts->concat($accounts);
        }

        return $allAccounts
            ->unique('uuid')
            ->sortBy(function ($account) {
                return $account['type'] === 'PRIVATE' ? 0 : 1;
            })
            ->values()
            ->toArray();
    }

    /**
     * Get player bank accounts.
     *
     * @param string $uuid
     * @return array
     */
    public function getPlayerBankAccounts(string $uuid): array
    {
        $data = $this->apiService->get("/api/player/{$uuid}/bankaccounts");

        if (!isset($data['accounts'])) {
            return [];
        }

        // Transform accounts from object to array with uuid as key
        return collect($data['accounts'])
            ->map(function ($account, $uuid) {
                return array_merge($account, [
                    'uuid' => $uuid,
                ]);
            })
            ->values()
            ->toArray();
    }

    /**
     * Withdraw money from a bank account.
     *
     * @param string $uuid
     * @param float $amount
     * @return bool
     */
    public function withdraw(string $uuid, float $amount): bool
    {
        $response = $this->apiService->post("/api/bankaccount/{$uuid}/withdraw", [
            'amount' => $amount
        ]);

        return $response['success'] ?? false;
    }

    /**
     * Deposit money to a bank account.
     *
     * @param string $uuid
     * @param float $amount
     * @return bool
     */
    public function deposit(string $uuid, float $amount): bool
    {
        $response = $this->apiService->post("/api/bankaccount/{$uuid}/deposit", [
            'amount' => $amount
        ]);

        return $response['success'] ?? false;
    }
}
