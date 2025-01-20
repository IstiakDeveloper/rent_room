<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;

class Scheduler
{
    public function schedule(Schedule $schedule): void
    {
        // Run auto-renewal check daily at midnight
        $schedule->command('bookings:process-renewals')
            ->dailyAt('00:00')
            ->withoutOverlapping()
            ->emailOutputOnFailure(config('mail.admin_email'));
    }
}
