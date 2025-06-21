<?php

namespace Src\ContentGeneration\ReviewSchedule\Console\Schedules;


use Illuminate\Console\Scheduling\Schedule;

class SyncBlogReviewSchedule
{
    /**
     * Registra todos os schedules de sincronização do blog
     */
    public static function register(Schedule $schedule): void
    {
        static::registerMinutelySync($schedule);
        static::registerHourlySync($schedule);
        static::registerDailyMaintenance($schedule);
    }

    /**
     * Sincronização a cada minuto (apenas artigos não sincronizados)
     */
    private static function registerMinutelySync(Schedule $schedule): void
    {
        // Sincronizar artigos não processados a cada minuto
        // Processa apenas 10 artigos por vez para não sobrecarregar
        $schedule->command('review-schedule:sync-blog --limit=10')
            ->everyMinute()
            ->withoutOverlapping(5) // Impede execuções simultâneas, timeout 5 min
            ->runInBackground()     // Executa em background
            ->appendOutputTo(storage_path('logs/blog-sync-minute.log')); // Salva logs para monitoramento
    }

    /**
     * Sincronização de recuperação a cada hora
     */
    private static function registerHourlySync(Schedule $schedule): void
    {
        // Sincronização de recuperação para artigos que podem ter falhado
        // Processa mais artigos por vez na sincronização horária
        $schedule->command('review-schedule:sync-blog --limit=50')
            ->hourly()
            ->withoutOverlapping(10) // Impede execuções simultâneas, timeout 10 min
            ->runInBackground()      // Executa em background
            ->appendOutputTo(storage_path('logs/blog-sync-hourly.log')); // Salva logs para monitoramento
    }

    /**
     * Manutenção diária de logs
     */
    private static function registerDailyMaintenance(Schedule $schedule): void
    {
        // Limpar logs antigos diariamente às 01:00
        // Remove logs com mais de 7 dias para economizar espaço
        $schedule->call(function () {
            $logFiles = [
                storage_path('logs/blog-sync-minute.log'),
                storage_path('logs/blog-sync-hourly.log')
            ];

            foreach ($logFiles as $logFile) {
                if (file_exists($logFile) && filemtime($logFile) < strtotime('-7 days')) {
                    unlink($logFile);
                }
            }
        })
        ->daily()
        ->at('01:00')
        ->name('blog-sync-log-cleanup'); // Nome para identificar no schedule:list
    }
}