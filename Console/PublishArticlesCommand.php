<?php

namespace Src\ContentGeneration\ReviewSchedule\Console;

use Illuminate\Console\Command;
use Src\ContentGeneration\ReviewSchedule\Application\Services\ReviewScheduleApplicationService;

class PublishArticlesCommand extends Command
{
    protected $signature = 'review-schedule:publish 
                           {--status=draft : Status of articles to publish}
                           {--limit=100 : Maximum number of articles to publish}
                           {--dry-run : Show what would be published without updating}
                           {--confirm : Skip confirmation prompt}';

    protected $description = 'Publish review schedule articles to TempArticle collection with category and publication data';

    public function handle(ReviewScheduleApplicationService $service): int
    {
        $status = $this->option('status');
        $limit = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');
        $skipConfirm = $this->option('confirm');

        $this->info("Publishing review schedule articles to TempArticle collection...");
        $this->info("Source Status: {$status}");
        $this->info("Limit: {$limit}");

        if ($dryRun) {
            $this->warn("DRY RUN MODE - No articles will be published");
        }

        // Mostrar estatísticas atuais
        $stats = $service->getArticleStats();
        $this->info("Current articles in review_schedule_temp_articles:");
        $this->line("  Total: {$stats['total']}");
        $this->line("  Draft: {$stats['draft']}");
        $this->line("  Published: {$stats['published']}");
        $this->newLine();

        if ($stats[$status] === 0) {
            $this->warn("No articles found with status: {$status}");
            return self::SUCCESS;
        }

        $articlesToPublish = min($stats[$status], $limit);

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

        $this->info("Publication completed!");
        $this->info("Articles published to TempArticle: {$result->published}");
        $this->info("Articles skipped (duplicates): {$result->skipped}");

        if ($result->failed > 0) {
            $this->error("Articles failed: {$result->failed}");
        }

        // Mostrar estatísticas do TempArticle
        if (!$dryRun) {
            $tempArticleStats = $service->getTempArticleStats();
            $this->newLine();
            $this->info("TempArticle collection status:");
            $this->line("  Total articles: {$tempArticleStats['total']}");
            $this->line("  Review schedule articles: {$tempArticleStats['review_schedule']}");
        }

        // Mostrar erros se houver
        if (!empty($result->errors)) {
            $this->newLine();
            $this->error("Errors encountered:");
            foreach (array_slice($result->errors, 0, 10) as $error) {
                $this->line("  - {$error}");
            }

            if (count($result->errors) > 10) {
                $this->line("  ... and " . (count($result->errors) - 10) . " more errors");
            }
        }

        // Sugestões finais
        if (!$dryRun && $result->published > 0) {
            $this->newLine();
            $this->info("Published articles are now available in the temp_articles collection.");
            $this->info("All articles were published with status 'draft' and category 'Revisões Programadas'.");
        }

        return $result->failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}