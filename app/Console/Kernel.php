<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected $commands = [
        \App\Console\Commands\SendCallReminders::class,
        \App\Console\Commands\SendTaskFailedNotifications::class,
    ];
    
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('calls:send-reminders')
             ->everyMinute()
             ->withoutOverlapping()
             ->appendOutputTo(storage_path('logs/call-reminders.log'));
        $schedule->command('tasks:send-failed-notifications')->daily();
    }
}
