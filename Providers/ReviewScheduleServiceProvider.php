<?php

namespace Src\ContentGeneration\ReviewSchedule\Providers;

use Illuminate\Support\ServiceProvider;
use Src\ContentGeneration\ReviewSchedule\Console\CsvStatsCommand;
use Src\ContentGeneration\ReviewSchedule\Console\AnalyzeArticleQuality;
use Src\ContentGeneration\ReviewSchedule\Console\DebugArticlesStructure;
use Src\ContentGeneration\ReviewSchedule\Console\DebugGenerationCommand;
use Src\ContentGeneration\ReviewSchedule\Console\FixAnnualChecksCommand;
use Src\ContentGeneration\ReviewSchedule\Console\PublishArticlesCommand;
use Src\ContentGeneration\ReviewSchedule\Console\ResetFutureSyncCommand;
use Src\ContentGeneration\ReviewSchedule\Console\GenerateArticlesCommand;
use Src\ContentGeneration\ReviewSchedule\Console\UpdateArticleStatusCommand;
use Src\ContentGeneration\ReviewSchedule\Console\CleanupReviewScheduleTicker;
use Src\ContentGeneration\ReviewSchedule\Console\PublishToTempArticlesCommand;
use Src\ContentGeneration\ReviewSchedule\Console\SyncBlogReviewScheduleCommand;
use Src\ContentGeneration\ReviewSchedule\Console\FixBrokenReviewScheduleArticles;
use Src\ContentGeneration\ReviewSchedule\Console\CleanupArticleTemplateReviewTicker;
use Src\ContentGeneration\ReviewSchedule\Domain\Services\VehicleTypeDetectorService;
use Src\ContentGeneration\ReviewSchedule\Domain\Services\ArticleContentGeneratorService;
use Src\ContentGeneration\ReviewSchedule\Infrastructure\Repositories\CsvVehicleRepository;
use Src\ContentGeneration\ReviewSchedule\Application\Services\ReviewScheduleApplicationService;
use Src\ContentGeneration\ReviewSchedule\Infrastructure\ContentTemplates\CarMaintenanceTemplate;
use Src\ContentGeneration\ReviewSchedule\Infrastructure\ContentTemplates\MotorcycleMaintenanceTemplate;
use Src\ContentGeneration\ReviewSchedule\Infrastructure\ContentTemplates\HybridVehicleMaintenanceTemplate;
use Src\ContentGeneration\ReviewSchedule\Infrastructure\Repositories\MongoReviewScheduleArticleRepository;
use Src\ContentGeneration\ReviewSchedule\Infrastructure\ContentTemplates\ElectricVehicleMaintenanceTemplate;

class ReviewScheduleServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Registrar repositories
        $this->app->singleton(CsvVehicleRepository::class, function ($app) {
            return new CsvVehicleRepository($app->make(VehicleTypeDetectorService::class));
        });
        
        $this->app->singleton(MongoReviewScheduleArticleRepository::class);

        // Registrar templates de conteúdo
        $this->app->singleton(CarMaintenanceTemplate::class);
        $this->app->singleton(MotorcycleMaintenanceTemplate::class);
        $this->app->singleton(ElectricVehicleMaintenanceTemplate::class);
        $this->app->singleton(HybridVehicleMaintenanceTemplate::class);

        // Registrar services
        $this->app->singleton(VehicleTypeDetectorService::class);
        
        $this->app->singleton(ArticleContentGeneratorService::class, function ($app) {
            return new ArticleContentGeneratorService(
                $app->make(CarMaintenanceTemplate::class),
                $app->make(MotorcycleMaintenanceTemplate::class),
                $app->make(ElectricVehicleMaintenanceTemplate::class),
                $app->make(HybridVehicleMaintenanceTemplate::class),
                $app->make(VehicleTypeDetectorService::class)
            );
        });

        $this->app->singleton(ReviewScheduleApplicationService::class, function ($app) {
            return new ReviewScheduleApplicationService(
                $app->make(ArticleContentGeneratorService::class),
                $app->make(CsvVehicleRepository::class),
                $app->make(MongoReviewScheduleArticleRepository::class)
            );
        });
    }

    public function boot()
    {
        // Registrar comandos console
        if ($this->app->runningInConsole()) {
            $this->commands([
                // Comandos principais
                GenerateArticlesCommand::class,
                CsvStatsCommand::class,
                PublishArticlesCommand::class,
                
                // Debug e análise
                DebugGenerationCommand::class,
                DebugArticlesStructure::class,
                AnalyzeArticleQuality::class, // TODO: Renomear para AnalyzeArticleQuality
                
                // Sincronização e utilitários
                SyncBlogReviewScheduleCommand::class,
                ResetFutureSyncCommand::class,

                // Limpeza
                CleanupArticleTemplateReviewTicker::class,
                CleanupReviewScheduleTicker::class,

                // Publicar
                PublishToTempArticlesCommand::class,
                UpdateArticleStatusCommand::class,

            ]);
        }
    }
}