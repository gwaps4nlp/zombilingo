<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\SendEmails::class,
        \App\Console\Commands\SendEmailsDuel::class,
        \App\Console\Commands\SendNews::class,
        \App\Console\Commands\PlanEmailNews::class,
        \App\Console\Commands\ExportCorpus::class,
        \App\Console\Commands\PlanDailyEmails::class,
        \App\Console\Commands\PlanWeeklyEmails::class,
        \App\Console\Commands\PlanMonthlyEmails::class,
        \App\Console\Commands\ComputeStatistics::class,
        \App\Console\Commands\BatchStatistics::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('corpus:export')
                 ->dailyAt('01:00');

        $schedule->command('news:plan-email')
                 ->everyFiveMinutes();

        $schedule->command('news:send')
                 ->everyFiveMinutes();

        $schedule->command('emails:send-duel')
                 ->everyFiveMinutes();

        $schedule->command('emails:plan-daily')
                 ->dailyAt('07:59');
        
        $schedule->command('emails:plan-weekly')
                 ->weekly()->mondays()->at('08:05');

        $schedule->command('emails:plan-monthly')
                 ->monthly()->mondays()->at('08:10');
                 
        $schedule->command('emails:send')
                 ->everyFiveMinutes();

        $schedule->command('statistics:compute')
                 ->dailyAt('03:00');
    }
}
