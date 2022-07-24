<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Spatie\Health\Commands\RunHealthChecksCommand;

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
        $new_date = date('d M, Y', strtotime(now('CET')));
        $logFile = 'schedule_'.($new_date).'.log';

        $schedule->command('watch:audio ')
            ->everyMinute()
            ->withoutOverlapping()
            ->appendOutputTo('storage/logs/downloads.log');

        $schedule->command('rabbitmq:consume --queue=classify --max-jobs=1 --stop-when-empty')
            ->everyMinute()
            ->withoutOverlapping()
            ->appendOutputTo('storage/logs/indexer.log');


//        $schedule->command('queue:work --queue=analyze --max-jobs=1 --stop-when-empty')
//            ->everyMinute()
//            ->withoutOverlapping()
//            ->appendOutputTo('storage/logs/analyze.log');

        $schedule->command('queue:clear')
            ->everyThirtyMinutes()
            ->appendOutputTo('storage/logs/scheduler.log');

        $schedule->command('queue:flush default')
            ->everyThirtyMinutes()
            ->appendOutputTo('storage/logs/scheduler.log');

        $schedule->command(RunHealthChecksCommand::class)
            ->everyMinute();
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
