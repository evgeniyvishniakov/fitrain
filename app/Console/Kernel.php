<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Генерация новых шаблонов тренировок каждую неделю
        $schedule->command('templates:generate --count=2')
                 ->weekly()
                 ->sundays()
                 ->at('09:00')
                 ->withoutOverlapping();

        // Генерация шаблонов для популярных категорий каждый день
        $schedule->command('templates:generate --count=1')
                 ->daily()
                 ->at('08:00')
                 ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
