<?php

namespace Src\ContentGeneration\ReviewSchedule\Console;

use Illuminate\Console\Command;
use Src\ContentGeneration\ReviewSchedule\Application\Services\ReviewScheduleApplicationService;

class GenerateArticlesCommand extends Command
{
    protected $signature = 'review-schedule:generate 
                           {csv_file : Path to vehicles CSV file}
                           {--batch=50 : Number of articles to generate per batch}
                           {--limit= : Maximum articles to generate}
                           {--start=1 : Starting line number}
                           {--vehicle-type= : Filter by vehicle type (electric|car|motorcycle|hybrid)}
                           {--make= : Filter by vehicle make}
                           {--year= : Filter by year range (e.g., 2020-2025)}
                           {--category= : Filter by category}
                           {--dry-run : Show what would be generated without saving}
                           {--force : Regenerate existing articles}';

    protected $description = 'Generate review schedule articles from CSV data with filters';

    public function handle(ReviewScheduleApplicationService $service): int
    {
        $csvFile = $this->argument('csv_file');
        $batchSize = (int) $this->option('batch');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $startLine = (int) $this->option('start') - 1;
        $vehicleTypeFilter = $this->option('vehicle-type');
        $makeFilter = $this->option('make');
        $yearFilter = $this->option('year');
        $categoryFilter = $this->option('category');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if (!file_exists($csvFile)) {
            $this->error("CSV file not found: {$csvFile}");
            return self::FAILURE;
        }

        $this->info("Starting review schedule articles generation...");
        $this->info("CSV File: {$csvFile}");
        $this->info("Batch Size: {$batchSize}");
        
        if ($limit) {
            $this->info("Limit: {$limit} articles");
        }
        
        if ($startLine > 0) {
            $this->info("Starting from line: " . ($startLine + 1));
        }

        // Mostrar filtros aplicados
        $this->showAppliedFilters($vehicleTypeFilter, $makeFilter, $yearFilter, $categoryFilter);

        if ($dryRun) {
            $this->warn("DRY RUN MODE - No articles will be saved");
        }

        if ($force) {
            $this->warn("FORCE MODE - Existing articles will be regenerated");
        }

        // Mostrar estatísticas antes da geração
        $statsBefore = $service->getArticleStats();
        $this->info("Current articles in database:");
        $this->line("  Total: {$statsBefore['total']}");
        $this->line("  Draft: {$statsBefore['draft']}");
        $this->line("  Published: {$statsBefore['published']}");
        $this->newLine();

        // Construir filtros
        $filters = $this->buildFilters($limit, $startLine, $vehicleTypeFilter, $makeFilter, $yearFilter, $categoryFilter);

        // Preview dos veículos que serão processados
        if ($dryRun || $this->output->isVerbose()) {
            $this->showVehiclePreview($service, $csvFile, $filters);
        }

        $progressBar = $this->output->createProgressBar();
        $progressBar->setFormat('verbose');

        $result = $service->generateArticlesFromCsvWithFilters(
            $csvFile,
            $batchSize,
            $filters,
            $dryRun,
            $force,
            function ($current, $total) use ($progressBar) {
                $progressBar->setMaxSteps($total);
                $progressBar->setProgress($current);
            }
        );

        $progressBar->finish();
        $this->newLine(2);

        // Mostrar resultados
        $this->info("Generation completed!");
        $this->info("Articles generated: {$result->generated}");

        if ($result->skipped > 0) {
            $this->warn("Articles skipped: {$result->skipped}");
        }

        if ($result->failed > 0) {
            $this->error("Articles failed: {$result->failed}");
        }

        // Mostrar estatísticas após a geração
        if (!$dryRun) {
            $statsAfter = $service->getArticleStats();
            $this->newLine();
            $this->info("Updated articles in database:");
            $this->line("  Total: {$statsAfter['total']} (+" . ($statsAfter['total'] - $statsBefore['total']) . ")");
            $this->line("  Draft: {$statsAfter['draft']} (+" . ($statsAfter['draft'] - $statsBefore['draft']) . ")");
            $this->line("  Published: {$statsAfter['published']}");
        }

        // Mostrar erros se houver
        if (!empty($result->errors)) {
            $this->newLine();
            $this->error("Errors encountered:");
            foreach ($result->errors as $error) {
                $this->line("  - {$error}");
            }
        }

        // Sugestões de próximos passos
        if (!$dryRun && $result->generated > 0) {
            $this->newLine();
            $this->info("Next steps:");
            $this->line("  - Review generated articles: php artisan review-schedule:stats");
            $this->line("  - Publish articles: php artisan review-schedule:publish");
        }

        return $result->failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function buildFilters(?int $limit, int $startLine, ?string $vehicleType, ?string $make, ?string $year, ?string $category): array
    {
        $filters = [];

        if ($limit) {
            $filters['limit'] = $limit;
        }

        if ($startLine > 0) {
            $filters['start'] = $startLine;
        }

        if ($vehicleType) {
            $filters['vehicle_type'] = $this->normalizeVehicleType($vehicleType);
        }

        if ($make) {
            $filters['make'] = trim($make);
        }

        if ($year) {
            $filters['year_range'] = $this->parseYearRange($year);
        }

        if ($category) {
            $filters['category'] = trim($category);
        }

        return $filters;
    }

    private function normalizeVehicleType(string $type): string
    {
        return match(strtolower(trim($type))) {
            'carro', 'car', 'auto' => 'car',
            'elétrico', 'eletrico', 'electric', 'ev' => 'electric',
            'híbrido', 'hibrido', 'hybrid', 'hev' => 'hybrid',
            'motocicleta', 'moto', 'motorcycle', 'bike' => 'motorcycle',
            default => strtolower(trim($type))
        };
    }

    private function parseYearRange(string $yearInput): array
    {
        if (strpos($yearInput, '-') !== false) {
            [$start, $end] = explode('-', $yearInput, 2);
            return [
                'start' => (int) trim($start),
                'end' => (int) trim($end)
            ];
        }

        $year = (int) trim($yearInput);
        return [
            'start' => $year,
            'end' => $year
        ];
    }

    private function showAppliedFilters(?string $vehicleType, ?string $make, ?string $year, ?string $category): void
    {
        $filters = [];

        if ($vehicleType) {
            $filters[] = "Vehicle Type: {$vehicleType}";
        }

        if ($make) {
            $filters[] = "Make: {$make}";
        }

        if ($year) {
            $filters[] = "Year: {$year}";
        }

        if ($category) {
            $filters[] = "Category: {$category}";
        }

        if (!empty($filters)) {
            $this->info("Applied Filters:");
            foreach ($filters as $filter) {
                $this->line("  - {$filter}");
            }
            $this->newLine();
        }
    }

    private function showVehiclePreview(ReviewScheduleApplicationService $service, string $csvFile, array $filters): void
    {
        try {
            $preview = $service->previewVehiclesWithFilters($csvFile, $filters);
            
            if (empty($preview)) {
                $this->warn("No vehicles found matching the specified filters");
                return;
            }

            $this->info("Vehicle Preview (" . count($preview) . " vehicles found):");
            
            $typeGroups = [];
            foreach ($preview as $vehicle) {
                $type = $vehicle['detected_type'] ?? 'unknown';
                if (!isset($typeGroups[$type])) {
                    $typeGroups[$type] = [];
                }
                $typeGroups[$type][] = $vehicle;
            }

            foreach ($typeGroups as $type => $vehicles) {
                $this->line("  {$type}: " . count($vehicles) . " vehicles");
                foreach (array_slice($vehicles, 0, 3) as $vehicle) {
                    $this->line("    - {$vehicle['make']} {$vehicle['model']} {$vehicle['year']}");
                }
                if (count($vehicles) > 3) {
                    $this->line("    ... and " . (count($vehicles) - 3) . " more");
                }
            }
            $this->newLine();
        } catch (\Exception $e) {
            $this->warn("Could not generate preview: " . $e->getMessage());
        }
    }
}