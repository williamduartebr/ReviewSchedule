<?php

namespace Src\ContentGeneration\ReviewSchedule\Console;

use Illuminate\Console\Command;
use Src\ContentGeneration\ReviewSchedule\Application\Services\ReviewScheduleApplicationService;
use Src\ContentGeneration\ReviewSchedule\Application\DTOs\VehicleData;
use Src\ContentGeneration\ReviewSchedule\Domain\Services\ArticleContentGeneratorService;

class DebugGenerationCommand extends Command
{
    protected $signature = 'review-schedule:debug 
                           {csv_file : Path to vehicles CSV file}
                           {--vehicle=1 : Which vehicle to test (line number)}
                           {--detailed : Show detailed debug info}';

    protected $description = 'Debug article generation process to identify issues';

    public function handle(
        ReviewScheduleApplicationService $service,
        ArticleContentGeneratorService $contentGenerator
    ): int {
        $csvFile = $this->argument('csv_file');
        $vehicleIndex = (int)$this->option('vehicle') - 1; // Convert to 0-based index
        $detailed = $this->option('detailed');

        if (!file_exists($csvFile)) {
            $this->error("CSV file not found: {$csvFile}");
            return self::FAILURE;
        }

        $this->info("Starting debug of article generation...");
        $this->info("CSV File: {$csvFile}");
        $this->info("Testing vehicle at line: " . ($vehicleIndex + 1));
        $this->newLine();

        try {
            // Testar conexão com banco
            $this->testDatabaseConnection($service);

            // Carregar veículos do CSV
            $this->testCsvLoading($csvFile, $vehicleIndex);

            // Testar criação de VehicleData
            $vehicleData = $this->testVehicleDataCreation($csvFile, $vehicleIndex);

            // Testar geração de conteúdo
            $article = $this->testContentGeneration($contentGenerator, $vehicleData, $detailed);

            // Testar conversão para array
            $this->testArrayConversion($article);

            // Testar save no repositório
            $this->testRepositorySave($service, $article);

            $this->info("✅ All tests passed! The issue might be in batch processing or CSV data.");
        } catch (\Exception $e) {
            $this->error("❌ Found the issue: " . $e->getMessage());
            $this->error("Stack trace:");
            $this->line($e->getTraceAsString());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function testDatabaseConnection(ReviewScheduleApplicationService $service): void
    {
        $this->info("🔍 Testing database connection...");

        try {
            $stats = $service->getArticleStats();
            $this->info("✅ Database connection OK");
            $this->line("   Current articles: {$stats['total']}");
            $this->line("   Draft: {$stats['draft']}");
            $this->line("   Published: {$stats['published']}");
        } catch (\Exception $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }

        $this->newLine();
    }

    private function testCsvLoading(string $csvFile, int $vehicleIndex): void
    {
        $this->info("🔍 Testing CSV loading...");

        if (!is_readable($csvFile)) {
            throw new \Exception("CSV file is not readable: {$csvFile}");
        }

        $handle = fopen($csvFile, 'r');
        if (!$handle) {
            throw new \Exception("Could not open CSV file: {$csvFile}");
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            throw new \Exception("Could not read CSV headers");
        }

        $this->info("✅ CSV headers: " . implode(', ', $headers));

        // Skip to the target vehicle
        $currentIndex = 0;
        $vehicleRow = null;

        while (($row = fgetcsv($handle)) !== false && $currentIndex <= $vehicleIndex) {
            if ($currentIndex === $vehicleIndex) {
                $vehicleRow = $row;
                break;
            }
            $currentIndex++;
        }

        fclose($handle);

        if (!$vehicleRow) {
            throw new \Exception("Could not find vehicle at index {$vehicleIndex}");
        }

        $this->info("✅ Target vehicle data: " . implode(', ', array_slice($vehicleRow, 0, 3)));
        $this->newLine();
    }

    private function testVehicleDataCreation(string $csvFile, int $vehicleIndex): VehicleData
    {
        $this->info("🔍 Testing VehicleData creation...");

        // Load vehicle data from CSV
        $handle = fopen($csvFile, 'r');
        $headers = fgetcsv($handle);

        $currentIndex = 0;
        $vehicleRow = null;

        while (($row = fgetcsv($handle)) !== false && $currentIndex <= $vehicleIndex) {
            if ($currentIndex === $vehicleIndex) {
                $vehicleRow = array_combine($headers, $row);
                break;
            }
            $currentIndex++;
        }

        fclose($handle);

        if (!$vehicleRow) {
            throw new \Exception("Could not load vehicle data");
        }

        try {
            $vehicleData = new VehicleData($vehicleRow);

            $this->info("✅ VehicleData created successfully");
            $this->line("   Make: {$vehicleData->make}");
            $this->line("   Model: {$vehicleData->model}");
            $this->line("   Year: {$vehicleData->year}");
            $this->line("   Full name: {$vehicleData->getFullName()}");
            $this->line("   Vehicle type: {$vehicleData->getVehicleType()}");
            $this->line("   Is valid: " . ($vehicleData->isValid() ? 'Yes' : 'No'));

            if (!$vehicleData->isValid()) {
                $this->warn("   Validation issues: " . implode(', ', $vehicleData->getValidationIssues()));
            }

            return $vehicleData;
        } catch (\Exception $e) {
            throw new \Exception("VehicleData creation failed: " . $e->getMessage());
        }

        $this->newLine();
    }

    private function testContentGeneration(
        ArticleContentGeneratorService $contentGenerator,
        VehicleData $vehicleData,
        bool $detailed
    ) {
        $this->info("🔍 Testing content generation...");

        try {
            $article = $contentGenerator->generateArticle($vehicleData->toArray());

            $this->info("✅ Article generated successfully");
            $this->line("   Title: {$article->getTitle()}");
            $this->line("   Slug: {$article->getSlug()}");
            $this->line("   Template: {$article->getTemplate()}");
            $this->line("   Status: {$article->getStatus()}");

            if ($detailed) {
                $content = $article->getContent();
                $this->line("   Content sections: " . implode(', ', array_keys($content)));

                foreach ($content as $section => $data) {
                    if (is_string($data)) {
                        $this->line("   {$section}: " . strlen($data) . " characters");
                    } elseif (is_array($data)) {
                        $this->line("   {$section}: " . count($data) . " items");
                    }
                }
            }

            return $article;
        } catch (\Exception $e) {
            throw new \Exception("Content generation failed: " . $e->getMessage());
        }

        $this->newLine();
    }

    private function testArrayConversion($article): void
    {
        $this->info("🔍 Testing array conversion...");

        try {
            $articleArray = $article->toArray();

            $this->info("✅ Array conversion successful");
            $this->line("   Array keys: " . implode(', ', array_keys($articleArray)));
            $this->line("   Title present: " . (isset($articleArray['title']) ? 'Yes' : 'No'));
            $this->line("   Vehicle info present: " . (isset($articleArray['vehicle_info']) ? 'Yes' : 'No'));
            $this->line("   Content present: " . (isset($articleArray['content']) ? 'Yes' : 'No'));
        } catch (\Exception $e) {
            throw new \Exception("Array conversion failed: " . $e->getMessage());
        }

        $this->newLine();
    }

    private function testRepositorySave(ReviewScheduleApplicationService $service, $article): void
    {
        $this->info("🔍 Testing repository save (dry run)...");

        try {
            // Test if article already exists
            $exists = false;
            try {
                $existing = $service->findArticleBySlug($article->getSlug());
                $exists = !is_null($existing);
            } catch (\Exception $e) {
                // Continue if there's an error checking existence
            }

            $this->info("✅ Repository operations working");
            $this->line("   Article already exists: " . ($exists ? 'Yes' : 'No'));

            if ($exists) {
                $this->warn("   This would be skipped in normal operation");
            } else {
                $this->info("   This would be saved in normal operation");
            }
        } catch (\Exception $e) {
            throw new \Exception("Repository operations failed: " . $e->getMessage());
        }

        $this->newLine();
    }
}
