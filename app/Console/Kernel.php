<?php

namespace App\Console;
use App\Models\Queue;
use App\Console\Commands\DeathQueue;
use App\Console\Commands\ActiveQueue;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{


    protected function schedule(Schedule $schedule)
     {
        //$schedule->call(function () {
    //     $q= Queue::where('id',4)->first();
    //             $q->update(['active'=>10]);

    // })->everyMinute();
        $schedule->command('Death:Queue')->everyMinute();
        $schedule->command('Active:Queue')->everyMinute();
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
