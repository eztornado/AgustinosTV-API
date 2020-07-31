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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('planificador:email_aviso')->everyMinute();
        $schedule->command('check:node_status')->everyTenMinutes();
        $schedule->command('planificador:renderizar_video')->everyMinute();
        $schedule->command('gestion_eventos:check_inicio')->everyMinute();  
        $schedule->command('gestion_eventos:check_final')->everyMinute();  
        $schedule->command('retransmision:ver')->everyMinute();  
        
        $schedule->command('sesiones:cerrar')->dailyAt('00:00');
        $schedule->command('backup:clean')->weeklyOn(1, '1:00');
        $schedule->command('backup:run')->weeklyOn(1, '2:00');          
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
