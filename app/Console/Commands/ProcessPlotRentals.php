<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\PlotRentalService;
use App\Support\Portal;
use Illuminate\Console\Command;
use Throwable;

/**
 * Renews due plot rentals, ends unpaid ones after the grace period and ends the
 * ones whose notice ran out. Scheduled hourly (routes/console.php); safe to run
 * again at any time, a period is never charged twice. In hosted mode it runs for
 * every active tenant and one failing tenant never stops the others.
 */
class ProcessPlotRentals extends Command
{
    protected $signature = 'plots:process-rentals {--tenants=* : Hosted mode: only these tenant ids}';

    protected $description = 'Renew plot rentals and end unpaid or cancelled ones';

    public function handle(): int
    {
        if (Portal::single()) {
            $this->report('portaal', $this->process());

            return self::SUCCESS;
        }

        $query = Tenant::query()->where('status', Tenant::ACTIVE)->orderBy('name');

        if ($ids = $this->option('tenants')) {
            $query->whereIn('id', $ids);
        }

        $failed = 0;

        foreach ($query->get() as $tenant) {
            try {
                $this->report($tenant->id, $tenant->run(fn () => $this->process()));
            } catch (Throwable $exception) {
                $failed++;
                report($exception);
                $this->error("{$tenant->id}: {$exception->getMessage()}");
            }
        }

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }

    /** Resolved per run, so the plugin services read the current tenant's settings. */
    private function process(): array
    {
        return app(PlotRentalService::class)->processDue();
    }

    private function report(string $portal, array $stats): void
    {
        if (array_sum($stats) === 0) {
            return;
        }

        $this->line("{$portal}: ".collect($stats)->map(fn ($count, $key) => "{$count} {$key}")->implode(', '));
    }
}
