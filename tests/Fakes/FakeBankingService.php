<?php

namespace Tests\Fakes;

use App\Services\Plugin\BankingService;

/** Bank accounts in memory: balances go down and up like in the plugin. */
class FakeBankingService extends BankingService
{
    /** @var array<string, array{owner: string, name: string, type: string, balance: float, frozen: bool}> */
    public array $accounts = [];

    /** @var list<array{0: string, 1: string, 2: float}> */
    public array $calls = [];

    public bool $failDeposits = false;

    public function __construct()
    {
    }

    public function addAccount(string $uuid, string $owner, float $balance, string $type = 'PRIVATE'): void
    {
        $this->accounts[$uuid] = ['owner' => $owner, 'name' => "Rekening {$uuid}", 'type' => $type, 'balance' => $balance, 'frozen' => false];
    }

    public function balance(string $uuid): float
    {
        return $this->accounts[$uuid]['balance'];
    }

    public function withdrawals(): array
    {
        return array_values(array_filter($this->calls, fn ($call) => $call[0] === 'withdraw'));
    }

    public function deposits(): array
    {
        return array_values(array_filter($this->calls, fn ($call) => $call[0] === 'deposit'));
    }

    public function getBankAccount(string $uuid): array
    {
        $account = $this->accounts[$uuid] ?? null;

        return $account ? ['name' => $account['name'], 'type' => $account['type'], 'balance' => $account['balance'], 'frozen' => $account['frozen']] : [];
    }

    public function getPlayerBankAccounts(string $uuid): array
    {
        return collect($this->accounts)
            ->filter(fn ($account) => $account['owner'] === $uuid)
            ->map(fn ($account, $accountUuid) => ['uuid' => $accountUuid, 'name' => $account['name'], 'type' => $account['type'], 'balance' => $account['balance'], 'frozen' => $account['frozen']])
            ->values()->all();
    }

    public function withdraw(string $uuid, float $amount): bool
    {
        if (! isset($this->accounts[$uuid]) || $this->accounts[$uuid]['balance'] < $amount) {
            return false;
        }

        $this->accounts[$uuid]['balance'] -= $amount;
        $this->calls[] = ['withdraw', $uuid, $amount];

        return true;
    }

    public function deposit(string $uuid, float $amount): bool
    {
        if ($this->failDeposits || ! isset($this->accounts[$uuid])) {
            return false;
        }

        $this->accounts[$uuid]['balance'] += $amount;
        $this->calls[] = ['deposit', $uuid, $amount];

        return true;
    }
}
