<?php

namespace Src\ContentGeneration\ReviewSchedule\Console;

use Illuminate\Console\Command;
use Src\ContentGeneration\ReviewSchedule\Application\Services\ReviewScheduleApplicationService;

class CsvStatsCommand extends Command
{
    protected $signature = 'review-schedule:csv-stats 
                           {csv_file : Path to vehicles CSV file}
                           {--detailed : Show detailed breakdown}
                           {--vehicle-type= : Filter by vehicle type for preview}
                           {--make= : Filter by make for preview}';

    protected $description = 'Show statistics about vehicles in CSV file';

    public function handle(ReviewScheduleApplicationService $service): int
    {
        $csvFile = $this->argument('csv_file');
        $detailed = $this->option('detailed');
        $vehicleTypeFilter = $this->option('vehicle-type');
        $makeFilter = $this->option('make');

        if (!file_exists($csvFile)) {
            $this->error("CSV file not found: {$csvFile}");
            return self::FAILURE;
        }

        $this->info("📊 Analyzing CSV file: {$csvFile}");
        $this->newLine();

        try {
            // Get overall statistics
            $stats = $service->getCsvStats($csvFile);
            
            $this->showOverallStats($stats);
            $this->showTypeBreakdown($stats);
            $this->showMakeBreakdown($stats, $detailed);
            $this->showYearRange($stats);

            // Show filtered preview if filters are provided
            if ($vehicleTypeFilter || $makeFilter) {
                $this->showFilteredPreview($service, $csvFile, $vehicleTypeFilter, $makeFilter);
            }

            $this->showUsageExamples();

        } catch (\Exception $e) {
            $this->error("Error analyzing CSV: " . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function showOverallStats(array $stats): void
    {
        $this->info("📈 Overall Statistics:");
        $this->line("  Total vehicles: {$stats['total']}");
        $this->line("  Year range: {$stats['year_range']['min']} - {$stats['year_range']['max']}");
        $this->line("  Unique makes: " . count($stats['by_make']));
        $this->newLine();
    }

    private function showTypeBreakdown(array $stats): void
    {
        $this->info("🚗 Vehicle Types:");
        
        foreach ($stats['by_type'] as $type => $count) {
            $percentage = round(($count / $stats['total']) * 100, 1);
            $this->line("  {$type}: {$count} vehicles ({$percentage}%)");
        }
        $this->newLine();
    }

    private function showMakeBreakdown(array $stats, bool $detailed): void
    {
        $this->info("🏭 Vehicle Makes:");
        
        $makesToShow = $detailed ? $stats['by_make'] : array_slice($stats['by_make'], 0, 10, true);
        
        foreach ($makesToShow as $make => $count) {
            $percentage = round(($count / $stats['total']) * 100, 1);
            $this->line("  {$make}: {$count} vehicles ({$percentage}%)");
        }
        
        if (!$detailed && count($stats['by_make']) > 10) {
            $remaining = count($stats['by_make']) - 10;
            $this->line("  ... and {$remaining} more makes");
            $this->line("  Use --detailed to see all makes");
        }
        $this->newLine();
    }

    private function showYearRange(array $stats): void
    {
        $this->info("📅 Year Distribution:");
        
        // Group by decade for cleaner display
        $decades = [];
        $startYear = $stats['year_range']['min'];
        $endYear = $stats['year_range']['max'];
        
        for ($year = $startYear; $year <= $endYear; $year++) {
            $decade = floor($year / 10) * 10;
            $decades[$decade] = ($decades[$decade] ?? 0) + 1;
        }
        
        foreach ($decades as $decade => $count) {
            $this->line("  {$decade}s: {$count} vehicles");
        }
        $this->newLine();
    }

    private function showFilteredPreview(ReviewScheduleApplicationService $service, string $csvFile, ?string $vehicleType, ?string $make): void
    {
        $this->info("🔍 Filtered Preview:");
        
        $filters = [];
        if ($vehicleType) {
            $filters['vehicle_type'] = $this->normalizeVehicleType($vehicleType);
            $this->line("  Vehicle Type Filter: {$vehicleType}");
        }
        if ($make) {
            $filters['make'] = $make;
            $this->line("  Make Filter: {$make}");
        }
        
        try {
            $preview = $service->previewVehiclesWithFilters($csvFile, $filters);
            
            if (empty($preview)) {
                $this->warn("  No vehicles found matching the filters");
            } else {
                $this->line("  Found: " . count($preview) . " vehicles");
                
                // Show some examples
                $examples = array_slice($preview, 0, 5);
                foreach ($examples as $vehicle) {
                    $this->line("    - {$vehicle['make']} {$vehicle['model']} {$vehicle['year']} ({$vehicle['detected_type']})");
                }
                
                if (count($preview) > 5) {
                    $this->line("    ... and " . (count($preview) - 5) . " more");
                }
            }
        } catch (\Exception $e) {
            $this->warn("  Error generating preview: " . $e->getMessage());
        }
        
        $this->newLine();
    }

    private function showUsageExamples(): void
    {
        $this->info("💡 Usage Examples:");
        $this->line("  # Generate 10 electric vehicles:");
        $this->line("  php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=electric --limit=10");
        $this->newLine();
        
        $this->line("  # Generate BMW vehicles only:");
        $this->line("  php artisan review-schedule:generate data/todos_veiculos.csv --make=BMW --limit=20");
        $this->newLine();
        
        $this->line("  # Generate motorcycles starting from line 100:");
        $this->line("  php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=motorcycle --start=100 --limit=15");
        $this->newLine();
        
        $this->line("  # Generate vehicles from 2020-2025:");
        $this->line("  php artisan review-schedule:generate data/todos_veiculos.csv --year=2020-2025 --limit=50");
        $this->newLine();
        
        $this->line("  # Dry run to see what would be generated:");
        $this->line("  php artisan review-schedule:generate data/todos_veiculos.csv --vehicle-type=hybrid --limit=10 --dry-run");
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
}