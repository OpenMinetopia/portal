<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\Tenancy\TenantMigrator;
use Illuminate\Console\Command;

/**
 * Migrates every tenant on deploy. One broken tenant database never stops the
 * others; the command reports each tenant and fails at the end if any did.
 */
class TenantsMigrateSafe extends Command
{
    protected $signature = 'tenants:migrate-safe {--tenants=* : Only these tenant ids}';

    protected $description = 'Run pending tenant migrations, tenant by tenant, without stopping on a failure';

    public function handle(TenantMigrator $migrator): int
    {
        $query = Tenant::query()->orderBy('name');

        if ($ids = $this->option('tenants')) {
            $query->whereIn('id', $ids);
        }

        $rows = [];
        $failed = 0;

        foreach ($query->get() as $tenant) {
            $result = $migrator->migrate($tenant);
            $failed += $result['ok'] ? 0 : 1;

            $rows[] = [
                $tenant->id,
                $tenant->name,
                $result['ok'] ? 'ok' : 'FAILED',
                count($result['ran']),
                $result['error'] ? mb_strimwidth($result['error'], 0, 120, '…') : '',
            ];
        }

        $this->table(['tenant', 'name', 'result', 'migrated', 'error'], $rows);
        $this->line(sprintf('%d tenants, %d failed.', count($rows), $failed));

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
