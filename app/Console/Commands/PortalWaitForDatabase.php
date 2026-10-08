<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class PortalWaitForDatabase extends Command
{
    protected $signature = 'portal:wait-for-database {--timeout=60 : Maximaal zoveel seconden wachten}';

    protected $description = 'Wacht tot de database bereikbaar is (voor Docker en Pterodactyl)';

    public function handle(): int
    {
        $deadline = time() + (int) $this->option('timeout');

        do {
            try {
                DB::connection()->getPdo();

                return self::SUCCESS;
            } catch (Throwable $exception) {
                $error = $exception->getMessage();
                DB::purge();
                sleep(2);
            }
        } while (time() < $deadline);

        $this->error('De database is niet bereikbaar: '.$error);

        return self::FAILURE;
    }
}
