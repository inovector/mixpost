<?php

namespace Inovector\Mixpost;

use Illuminate\Console\Scheduling\Schedule as LaravelSchedule;

class Schedule
{
    public static function register(LaravelSchedule $schedule): void
    {
        $schedule->command('mixpost:run-scheduled-posts')->everyMinute();
        $schedule->command('mixpost:import-account-data')->everyTwoHours();
        $schedule->command('mixpost:import-account-audience')->everyThreeHours();
        $schedule->command('mixpost:process-metrics')->everyThreeHours();
        $schedule->command('mixpost:delete-old-data')->daily();
        $schedule->command('mixpost:prune-temporary-directory')->hourly();
    }
}
