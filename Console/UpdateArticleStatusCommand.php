<?php

namespace Src\ContentGeneration\ReviewSchedule\Console;

use Illuminate\Console\Command;
use Src\ContentGeneration\ReviewSchedule\Application\Services\ReviewScheduleApplicationService;

class UpdateArticleStatusCommand extends Command
{
    protected $signature = 'review-schedule:update-status 
                           {--from=draft : Current status of articles to update}
                           {--to=published : New status to set}
                           {--limit=100 : Maximum number of articles to update}
                           {--dry-run : Show what would be updated without updating}
                           {--confirm : Skip confirmation prompt}';

    protected $description = 'Update article status in review_schedule_temp_articles collection';

    public function handle(ReviewScheduleApplicationService $service): int
    {
        $fromStatus = $this->option('from');
        $toStatus = $this->option('to');
        $limit = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');
        $skipConfirm = $this->option('confirm');

        $this->info("Updating article status in review_schedule_temp_articles...");
        $this->info("From Status: {$fromStatus}");
        $this->info("To Status: {$toStatus}");
        $this->info("Limit: {$limit}");

        if ($dryRun) {
            $this->warn("DRY RUN MODE - No articles will be updated");
        }

        $this->newLine();

        // Mostrar estatísticas atuais
        $stats = $service->getArticleStats();
        $this->info("Current articles in review_schedule_temp_articles:");
        $this->line("  Total: {$stats['total']}");
        $this->line("  Draft: {$stats['draft']}");
        $this->line("  Published: {$stats['published']}");
        $this->newLine();

        if ($stats[$fromStatus] === 0) {
            $this->warn("No articles found with status: {$fromStatus}");
            return self::SUCCESS;
        }

        $articlesToUpdate = min($stats[$fromStatus], $limit);

        if (!$skipConfirm && !$dryRun) {
            if (!$this->confirm("Do you want to update {$articlesToUpdate} articles from '{$fromStatus}' to '{$toStatus}'?")) {
                $this->info("Update cancelled.");
                return self::SUCCESS;
            }
        }

        $this->info("Processing {$articlesToUpdate} articles...");

        $result = $service->publishArticlesByStatus($fromStatus);

        // Mostrar resultados
        if ($dryRun) {
            $this->info("Status update simulation completed!");
        } else {
            $this->info("Status update completed!");
        }

        $this->info("Articles updated: {$result->published}");

        if ($result->failed > 0) {
            $this->error("Articles failed: {$result->failed}");
        }

        // Mostrar estatísticas após atualização
        if (!$dryRun) {
            $statsAfter = $service->getArticleStats();
            $this->newLine();
            $this->info("Updated article statistics:");
            $this->line("  Total: {$statsAfter['total']}");
            $this->line("  Draft: {$statsAfter['draft']} (" . 
                ($statsAfter['draft'] - $stats['draft']) . ")");
            $this->line("  Published: {$statsAfter['published']} (+" . 
                ($statsAfter['published'] - $stats['published']) . ")");
        }

        // Mostrar erros se houver
        if (!empty($result->errors)) {
            $this->newLine();
            $this->error("Errors encountered:");
            foreach (array_slice($result->errors, 0, 10) as $error) {
                $this->line("  - {$error}");
            }

            if (count($result->errors) > 10) {
                $remaining = count($result->errors) - 10;
                $this->line("  ... and {$remaining} more errors");
            }
        }

        return $result->failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}