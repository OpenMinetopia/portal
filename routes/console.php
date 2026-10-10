<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Plot rentals: renewals, the grace period and notice. Needs `schedule:run` every
// minute: Docker and Pterodactyl start it by themselves, see docs/HOSTING.md and README.md.
Schedule::command('plots:process-rentals')->hourly()->withoutOverlapping(30);
