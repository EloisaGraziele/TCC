<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\AnalisarAlertas::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Executa análise de alertas da secretaria a cada hora em dias letivos
        $schedule->command('alertas:analisar')
                 ->hourly()
                 ->between('7:00', '18:00')
                 ->withoutOverlapping()
                 ->runInBackground();
                 
        // Processa alertas de alta taxa de faltas diariamente às 18:00
        $schedule->command('alertas:processar')
                 ->dailyAt('18:00')
                 ->withoutOverlapping()
                 ->runInBackground();
                 
        // Mantém listener MQTT sempre ativo
        $schedule->command('mqtt:listen --daemon')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}