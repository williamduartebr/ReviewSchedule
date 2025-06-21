<?php

namespace Src\ContentGeneration\ReviewSchedule\Console;

use Illuminate\Console\Command;
use Src\ContentGeneration\ReviewSchedule\Application\Services\ReviewScheduleApplicationService;

class PublishToTempArticlesCommand extends Command
{
    protected $signature = 'review-schedule:publish-temp 
                           {--status=draft : Status of articles to publish}
                           {--limit=100 : Maximum number of articles to publish}
                           {--dry-run : Show what would be published without publishing}
                           {--confirm : Skip confirmation prompt}
                           {--skip-duplicates : Skip articles that already exist in TempArticle}';

    protected $description = 'Publish review schedule articles to TempArticle collection';

    public function handle(ReviewScheduleApplicationService $service): int
    {
        $status = $this->option('status');
        $limit = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');
        $skipConfirm = $this->option('confirm');
        $skipDuplicates = $this->option('skip-duplicates');

        $this->info("Publishing review schedule articles to TempArticle collection...");
        $this->info("Source Status: {$status}");
        $this->info("Limit: {$limit}");

        if ($dryRun) {
            $this->warn("DRY RUN MODE - No articles will be published");
        }

        if ($skipDuplicates) {
            $this->info("SKIP DUPLICATES MODE - Existing TempArticles will be skipped");
        }

        $this->newLine();

        // Mostrar estatísticas atuais
        $reviewScheduleStats = $service->getArticleStats();
        $tempArticleStats = $service->getTempArticleStats();

        $this->info("Current articles in review_schedule_temp_articles:");
        $this->line("  Total: {$reviewScheduleStats['total']}");
        $this->line("  Draft: {$reviewScheduleStats['draft']}");
        $this->line("  Published: {$reviewScheduleStats['published']}");
        
        $this->info("Current articles in temp_articles:");
        $this->line("  Total: {$tempArticleStats['total']}");
        $this->line("  Review Schedule: {$tempArticleStats['review_schedule']}");
        $this->newLine();

        if ($reviewScheduleStats[$status] === 0) {
            $this->warn("No articles found with status: {$status}");
            return self::SUCCESS;
        }

        $articlesToPublish = min($reviewScheduleStats[$status], $limit);

        if (!$skipConfirm && !$dryRun) {
            if (!$this->confirm("Do you want to publish {$articlesToPublish} articles to TempArticle collection?")) {
                $this->info("Publication cancelled.");
                return self::SUCCESS;
            }
        }

        $this->info("Processing {$articlesToPublish} articles...");

        $progressBar = $this->output->createProgressBar();
        $progressBar->setFormat('verbose');

        $result = $service->publishToTempArticles(
            $status,
            $limit,
            $dryRun,
            function ($current, $total) use ($progressBar) {
                $progressBar->setMaxSteps($total);
                $progressBar->setProgress($current);
            }
        );

        $progressBar->finish();
        $this->newLine(2);

        // Mostrar resultados
        if ($dryRun) {
            $this->info("Publication simulation completed!");
        } else {
            $this->info("Publication completed!");
        }

        $this->info("Articles published to TempArticle: {$result->published}");
        
        if ($result->skipped > 0) {
            $this->warn("Articles skipped (duplicates): {$result->skipped}");
        }

        if ($result->failed > 0) {
            $this->error("Articles failed: {$result->failed}");
        }

        // Mostrar estatísticas do TempArticle após publicação
        if (!$dryRun) {
            $tempArticleStatsAfter = $service->getTempArticleStats();
            $this->newLine();
            $this->info("Updated TempArticle collection status:");
            $this->line("  Total articles: {$tempArticleStatsAfter['total']} (+" . 
                ($tempArticleStatsAfter['total'] - $tempArticleStats['total']) . ")");
            $this->line("  Review schedule articles: {$tempArticleStatsAfter['review_schedule']} (+" . 
                ($tempArticleStatsAfter['review_schedule'] - $tempArticleStats['review_schedule']) . ")");
        }

        // Mostrar erros se houver
        if (!empty($result->errors)) {
            $this->newLine();
            $this->error("Errors encountered:");
            
            $maxErrorsToShow = 10;
            $errorsToShow = array_slice($result->errors, 0, $maxErrorsToShow);
            
            foreach ($errorsToShow as $error) {
                $this->line("  - {$error}");
            }

            if (count($result->errors) > $maxErrorsToShow) {
                $remaining = count($result->errors) - $maxErrorsToShow;
                $this->line("  ... and {$remaining} more errors");
                $this->line("  💡 Tip: Use --dry-run to check for issues first");
            }
        }

        // Sugestões finais
        if (!$dryRun && $result->published > 0) {
            $this->newLine();
            $this->info("✅ Success! Published articles are now available in the temp_articles collection.");
            $this->info("📝 All articles were published with:");
            $this->line("  • Status: draft");
            $this->line("  • Category: Revisões Programadas (ID: 21)");
            $this->line("  • Source: review_schedule");
            $this->line("  • Template: review_schedule");
            $this->newLine();
            
            $this->info("🚀 Next steps:");
            $this->line("  • Review published articles in temp_articles collection");
            $this->line("  • Check content quality before final publication");
            $this->line("  • Use blog publishing system to make articles live");
        }

        return $result->failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}