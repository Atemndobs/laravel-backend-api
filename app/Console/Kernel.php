<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $newdate = date('d M, Y', strtotime(now('CET')));
        $logFile = 'schedule_'.($newdate).'.log';

        $schedule->command('queue:work --max-jobs=1 --stop-when-empty')
            ->everyMinute()
            ->appendOutputTo('storage/logs/scheduler.log');

        $schedule->command('queue:work database --queue=analyze --stop-when-empty')
            ->everyMinute()
            ->appendOutputTo('storage/logs/scheduler.log');

        $schedule->command('queue:clear')
            ->everyThirtyMinutes()
            ->appendOutputTo('storage/logs/scheduler.log');

        $schedule->command('queue:flush')
            ->everyThirtyMinutes()
            ->appendOutputTo('storage/logs/scheduler.log');
/*        $schedule->command('song:import')
            ->everyThirtyMinutes()
            ->appendOutputTo('storage/logs/scheduler.log');

        $schedule->command('song:bpm')
            ->everyThirtyMinutes()
            ->appendOutputTo('storage/logs/scheduler.log')
        ;*/
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
