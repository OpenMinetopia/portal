<?php

namespace App\Services\Tenancy;

use App\Models\Tenant;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Runs a tenant's pending migrations from database/migrations/tenant. Existing
 * portal databases already ran these files under the same names, so for them
 * nothing is pending.
 */
class TenantMigrator
{
    public static function path(): string
    {
        return database_path('migrations/tenant');
    }

    /**
     * @return array{ok: bool, ran: list<string>, error: ?string}
     */
    public function migrate(Tenant $tenant): array
    {
        // One at a time per tenant: the website and the deploy may both try.
        $lock = Cache::store('provisioning')->lock('tenant-migrate:'.$tenant->getTenantKey(), 300);

        try {
            $lock->block(60);

            return $tenant->run(function () {
                $before = $this->pending();

                Artisan::call('migrate', ['--path' => self::path(), '--realpath' => true, '--force' => true]);

                $after = $this->pending();

                return ['ok' => $after === [], 'ran' => array_values(array_diff($before, $after)), 'error' => $after === [] ? null : 'Still pending: '.implode(', ', $after)];
            });
        } catch (Throwable $exception) {
            return ['ok' => false, 'ran' => [], 'error' => $exception->getMessage()];
        } finally {
            $lock->release();
        }
    }

    /** Names of migrations not yet run on the tenant's database. Call inside tenant context. */
    public function pending(): array
    {
        /** @var Migrator $migrator */
        $migrator = app('migrator');
        $migrator->setConnection(null);

        $files = array_keys($migrator->getMigrationFiles([self::path()]));

        if (! $migrator->repositoryExists()) {
            return $files;
        }

        return array_values(array_diff($files, $migrator->getRepository()->getRan()));
    }

    /** Pending migration names for a tenant, or null when its database is unreachable. */
    public function pendingFor(Tenant $tenant): ?array
    {
        try {
            return $tenant->run(fn () => $this->pending());
        } catch (Throwable) {
            return null;
        }
    }
}
