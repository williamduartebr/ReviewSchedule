<?php

namespace Src\ContentGeneration\ReviewSchedule\Console;

use Illuminate\Console\Command;
use Src\ContentGeneration\ReviewSchedule\Application\Services\ReviewScheduleApplicationService;
use Src\ArticleGenerator\Infrastructure\Eloquent\TempArticle;

class ImprovedPublishArticlesCommand extends Command
{
    protected $signature = 'review-schedule:publish-improved 
                           {--status=draft : Status of articles to publish}
                           {--limit=100 : Maximum number of articles to publish}
                           {--dry-run : Show what would be published without actually publishing}
                           {--skip-duplicates : Skip articles that already exist in TempArticle}
                           {--check-conflicts : Check for slug conflicts before publishing}';

    protected $description = 'Publish review schedule articles to TempArticle with duplicate checking';

    public function handle(ReviewScheduleApplicationService $service): int
    {
        $status = $this->option('status');
        $limit = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');
        $skipDuplicates = $this->option('skip-duplicates');
        $checkConflicts = $this->option('check-conflicts');

        $this->info("Starting improved article publishing...");
        $this->info("Source status: {$status}");
        $this->info("Limit: {$limit}");

        if ($dryRun) {
            $this->warn("DRY RUN MODE - No articles will be published");
        }
        if ($skipDuplicates) {
            $this->info("SKIP DUPLICATES MODE - Existing TempArticles will be skipped");
        }
        if ($checkConflicts) {
            $this->info("CONFLICT CHECK MODE - Will check for slug conflicts");
        }
        $this->newLine();

        // Mostrar estatísticas antes
        $reviewScheduleStats = $service->getArticleStats();
        $tempArticleStats = $service->getTempArticleStats();

        $this->info("Current ReviewSchedule articles:");
        $this->line("  Total: {$reviewScheduleStats['total']}");
        $this->line("  Draft: {$reviewScheduleStats['draft']}");
        $this->line("  Published: {$reviewScheduleStats['published']}");

        $this->info("Current TempArticles:");
        $this->line("  Total: {$tempArticleStats['total']}");
        $this->line("  Review Schedule: {$tempArticleStats['review_schedule']}");
        $this->newLine();

        try {
            // Buscar artigos para publicar
            $articles = $this->getArticlesToPublish($service, $status, $limit);

            if (empty($articles)) {
                $this->warn("No articles found with status '{$status}'");
                return self::SUCCESS;
            }

            $this->info("Found " . count($articles) . " articles to process");
            $this->newLine();

            $results = (object)[
                'published' => 0,
                'skipped' => 0,
                'failed' => 0,
                'conflicts' => 0,
                'errors' => []
            ];

            // Verificar conflitos se solicitado
            if ($checkConflicts) {
                $this->checkSlugConflicts($articles, $results);
            }

            // Processar artigos
            $progressBar = $this->output->createProgressBar(count($articles));
            $progressBar->setFormat('verbose');

            foreach ($articles as $article) {
                $this->processArticle($article, $results, $dryRun, $skipDuplicates);
                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            $this->showPublishResults($results, $dryRun);

            // Mostrar estatísticas após
            if (!$dryRun) {
                $tempArticleStatsAfter = $service->getTempArticleStats();
                $this->newLine();
                $this->info("Updated TempArticles:");
                $this->line("  Total: {$tempArticleStatsAfter['total']} (+" .
                    ($tempArticleStatsAfter['total'] - $tempArticleStats['total']) . ")");
                $this->line("  Review Schedule: {$tempArticleStatsAfter['review_schedule']} (+" .
                    ($tempArticleStatsAfter['review_schedule'] - $tempArticleStats['review_schedule']) . ")");
            }
        } catch (\Exception $e) {
            $this->error("Fatal error during publishing: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function getArticlesToPublish(ReviewScheduleApplicationService $service, string $status, int $limit): array
    {
        try {
            return $service->getArticleRepository()->findByStatus($status, $limit);
        } catch (\Exception $e) {
            $this->error("Error fetching articles: " . $e->getMessage());
            return [];
        }
    }

    private function checkSlugConflicts(array $articles, object $results): void
    {
        $this->info("Checking for slug conflicts...");

        $slugs = array_column($articles, 'slug');
        $duplicateSlugs = array_count_values($slugs);
        $conflicts = array_filter($duplicateSlugs, fn($count) => $count > 1);

        if (!empty($conflicts)) {
            $results->conflicts = count($conflicts);
            $this->warn("Found " . count($conflicts) . " slug conflicts:");
            foreach ($conflicts as $slug => $count) {
                $this->line("  - {$slug} ({$count} times)");
            }
            $this->newLine();
        } else {
            $this->info("No slug conflicts found");
        }
    }

    private function processArticle(array $article, object $results, bool $dryRun, bool $skipDuplicates): void
    {
        $slug = $article['slug'] ?? 'unknown';
        $title = $article['title'] ?? 'Unknown Title';

        try {
            // Verificar se já existe no TempArticle
            if ($skipDuplicates && $this->tempArticleExists($slug)) {
                $results->skipped++;
                return;
            }

            if ($dryRun) {
                $results->published++;
                return;
            }

            // Converter e salvar
            $tempArticleData = $this->convertToTempArticleFormat($article);

            $tempArticle = new TempArticle();
            $tempArticle->fill($tempArticleData);

            if ($tempArticle->save()) {
                $results->published++;
            } else {
                $results->failed++;
                $results->errors[] = "Failed to save TempArticle: {$slug}";
            }
        } catch (\Exception $e) {
            $results->failed++;
            $results->errors[] = "Error processing {$slug}: " . $e->getMessage();
        }
    }

    private function tempArticleExists(string $slug): bool
    {
        try {
            return TempArticle::where('slug', $slug)->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    private function convertToTempArticleFormat(array $article): array
    {
        $vehicleInfo = $article['vehicle_info'] ?? [];
        $content = $article['content'] ?? [];
        $seoData = $article['seo_data'] ?? [];

        // Extrair entidades do veículo
        $extractedEntities = [
            'marca' => $vehicleInfo['make'] ?? '',
            'modelo' => $vehicleInfo['model'] ?? '',
            'ano' => $vehicleInfo['year'] ?? '',
            'motorizacao' => $vehicleInfo['engine'] ?? '',
            'versao' => $vehicleInfo['version'] ?? 'Todas',
            'tipo_veiculo' => $this->mapVehicleType($vehicleInfo['vehicle_type'] ?? 'car'),
            'categoria' => $vehicleInfo['subcategory'] ?? '',
            'combustivel' => $vehicleInfo['fuel_type'] ?? 'flex'
        ];

        // Dados SEO formatados
        $seoFormatted = [
            'page_title' => $seoData['page_title'] ?? $article['title'],
            'meta_description' => $seoData['meta_description'] ?? $this->generateMetaDescription($article['title'], $vehicleInfo),
            'url_slug' => $article['slug'],
            'h1' => $seoData['h1'] ?? $article['title'],
            'h2_tags' => $seoData['h2_tags'] ?? $this->extractH2Tags($content),
            'primary_keyword' => $seoData['primary_keyword'] ?? $this->generatePrimaryKeyword($vehicleInfo),
            'secondary_keywords' => $seoData['secondary_keywords'] ?? $this->generateSecondaryKeywords($vehicleInfo),
            'meta_robots' => 'index,follow',
            'canonical_url' => config('app.url') . '/' . $article['slug'],
            'schema_type' => 'Article',
            'focus_keywords' => $this->generateFocusKeywords($vehicleInfo)
        ];

        return [
            'title' => $article['title'],
            'slug' => $article['slug'],
            'content' => json_encode($content),
            'extracted_entities' => $extractedEntities,
            'seo_data' => $seoFormatted,
            'source' => 'review_schedule',
            'domain' => $article['domain'] ?? 'review_schedule',
            'status' => 'published',
            'template' => $article['template'] ?? 'review_schedule',
            'quality_score' => $this->calculateQualityScore($content),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    private function mapVehicleType(string $type): string
    {
        return match ($type) {
            'motorcycle' => 'motocicleta',
            'electric' => 'elétrico',
            'hybrid' => 'híbrido',
            default => 'carro'
        };
    }

    private function generateMetaDescription(string $title, array $vehicleInfo): string
    {
        $make = $vehicleInfo['make'] ?? '';
        $model = $vehicleInfo['model'] ?? '';
        $year = $vehicleInfo['year'] ?? '';

        return "Cronograma completo de revisões do {$make} {$model} {$year}. Intervalos, custos e dicas de manutenção preventiva. Mantenha seu veículo sempre em perfeito estado.";
    }

    private function extractH2Tags(array $content): array
    {
        return [
            'Visão Geral das Revisões',
            'Cronograma Detalhado',
            'Manutenção Preventiva',
            'Peças de Atenção',
            'Especificações Técnicas',
            'Garantia e Recomendações',
            'Perguntas Frequentes'
        ];
    }

    private function generatePrimaryKeyword(array $vehicleInfo): string
    {
        $make = strtolower($vehicleInfo['make'] ?? '');
        $model = strtolower($vehicleInfo['model'] ?? '');

        return "cronograma revisão {$make} {$model}";
    }

    private function generateSecondaryKeywords(array $vehicleInfo): array
    {
        $make = strtolower($vehicleInfo['make'] ?? '');
        $model = strtolower($vehicleInfo['model'] ?? '');
        $year = $vehicleInfo['year'] ?? '';

        return [
            "manutenção {$make} {$model}",
            "revisão {$make} {$model} {$year}",
            "cronograma manutenção preventiva",
            "quando revisar {$make} {$model}",
            "custos revisão {$make}"
        ];
    }

    private function generateFocusKeywords(array $vehicleInfo): array
    {
        return [
            'cronograma de revisões',
            'manutenção preventiva',
            'revisão programada',
            'garantia do veículo'
        ];
    }

    private function calculateQualityScore(array $content): int
    {
        $score = 0;

        $requiredSections = ['introducao', 'cronograma_detalhado', 'perguntas_frequentes', 'consideracoes_finais'];
        foreach ($requiredSections as $section) {
            if (isset($content[$section]) && !empty($content[$section])) {
                $score += 25;
            }
        }

        return min(100, $score);
    }

    private function showPublishResults(object $results, bool $dryRun): void
    {
        $this->info($dryRun ? "Publishing simulation completed!" : "Publishing completed!");

        $this->info("Articles processed: {$results->published}");

        if ($results->skipped > 0) {
            $this->warn("Articles skipped: {$results->skipped}");
        }

        if ($results->conflicts > 0) {
            $this->warn("Slug conflicts found: {$results->conflicts}");
        }

        if ($results->failed > 0) {
            $this->error("Articles failed: {$results->failed}");

            if (!empty($results->errors)) {
                $this->newLine();
                $this->error("Error details:");
                foreach (array_slice($results->errors, 0, 10) as $error) {
                    $this->line("  - {$error}");
                }

                if (count($results->errors) > 10) {
                    $this->line("  ... and " . (count($results->errors) - 10) . " more errors");
                }
            }
        }
    }
}
