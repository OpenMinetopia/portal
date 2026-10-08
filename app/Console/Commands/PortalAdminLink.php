<?php

namespace App\Console\Commands;

use App\Services\Tenancy\AdminClaim;
use App\Support\Portal;
use Illuminate\Console\Command;

class PortalAdminLink extends Command
{
    protected $signature = 'portal:admin-link {--hours= : Hoe lang de link geldig is (standaard 24)}';

    protected $description = 'Maak een eenmalige link waarmee je beheerder van het portaal wordt';

    public function handle(): int
    {
        if (Portal::hosted()) {
            $this->error('Op het gehoste platform maakt de OpenMinetopia-website deze links.');

            return self::FAILURE;
        }

        $hours = (int) ($this->option('hours') ?: config('portal.admin_link_hours'));
        $url = AdminClaim::url(null, AdminClaim::issue(null, $hours * 60));

        $this->line('Open deze link en log in of maak een account aan. Daarna ben je beheerder.');
        $this->line("De link werkt één keer en is {$hours} uur geldig. Een nieuwe link maakt de vorige ongeldig.");
        $this->newLine();
        $this->line("  {$url}");
        $this->newLine();

        return self::SUCCESS;
    }
}
