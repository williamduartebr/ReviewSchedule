<?php

namespace Src\ContentGeneration\ReviewSchedule\Infrastructure\Repositories;

use InvalidArgumentException;
use Src\ContentGeneration\ReviewSchedule\Domain\Services\VehicleTypeDetectorService;

class CsvVehicleRepository
{
    private ?VehicleTypeDetectorService $typeDetector;

    public function __construct(?VehicleTypeDetectorService $typeDetector = null)
    {
        $this->typeDetector = $typeDetector;
    }

    public function loadVehiclesFromCsv(string $csvFilePath): array
    {
        return $this->loadVehiclesFromCsvWithFilters($csvFilePath, []);
    }

    public function loadVehiclesFromCsvWithFilters(string $csvFilePath, array $filters = []): array
    {
        if (!file_exists($csvFilePath)) {
            throw new InvalidArgumentException("CSV file not found: {$csvFilePath}");
        }

        $vehicles = [];
        $handle = fopen($csvFilePath, 'r');

        if ($handle === false) {
            throw new InvalidArgumentException("Cannot open CSV file: {$csvFilePath}");
        }

        $headers = fgetcsv($handle);
        if ($headers === false) {
            fclose($handle);
            throw new InvalidArgumentException("Cannot read CSV headers from: {$csvFilePath}");
        }

        $headers = array_map('strtolower', $headers);

        // Extrair configurações dos filtros
        $startLine = $filters['start'] ?? 0;
        $limit = $filters['limit'] ?? null;
        $vehicleTypeFilter = $filters['vehicle_type'] ?? null;
        $makeFilter = $filters['make'] ?? null;
        $yearRange = $filters['year_range'] ?? null;
        $categoryFilter = $filters['category'] ?? null;

        $lineNumber = 0;
        $vehicleCount = 0;

        while (($data = fgetcsv($handle)) !== false) {
            $lineNumber++;

            // Skip lines before start
            if ($lineNumber <= $startLine) {
                continue;
            }

            // Check limit
            if ($limit && $vehicleCount >= $limit) {
                break;
            }

            if (count($data) !== count($headers)) {
                continue; // Skip malformed rows
            }

            $vehicle = array_combine($headers, $data);

            // Validate required fields
            if (!$this->isValidVehicle($vehicle)) {
                continue;
            }

            $vehicle = $this->normalizeVehicleData($vehicle);

            // Apply filters
            if (!$this->matchesFilters($vehicle, $vehicleTypeFilter, $makeFilter, $yearRange, $categoryFilter)) {
                continue;
            }

            $vehicles[] = $vehicle;
            $vehicleCount++;
        }

        fclose($handle);
        return $vehicles;
    }

    public function previewVehiclesWithFilters(string $csvFilePath, array $filters = []): array
    {
        // Load with filters but add detected type for preview
        $vehicles = $this->loadVehiclesFromCsvWithFilters($csvFilePath, $filters);
        
        // Add detected type to each vehicle for preview
        foreach ($vehicles as &$vehicle) {
            if ($this->typeDetector) {
                $vehicle['detected_type'] = $this->typeDetector->detectVehicleType($vehicle);
            } else {
                $vehicle['detected_type'] = $this->detectVehicleTypeSimple($vehicle);
            }
        }

        return $vehicles;
    }

    public function loadVehiclesBatch(string $csvFilePath, int $batchSize = 50, int $offset = 0): array
    {
        $allVehicles = $this->loadVehiclesFromCsv($csvFilePath);
        return array_slice($allVehicles, $offset, $batchSize);
    }

    public function countVehicles(string $csvFilePath): int
    {
        if (!file_exists($csvFilePath)) {
            return 0;
        }

        $count = 0;
        $handle = fopen($csvFilePath, 'r');

        if ($handle === false) {
            return 0;
        }

        // Skip header
        fgetcsv($handle);

        while (fgetcsv($handle) !== false) {
            $count++;
        }

        fclose($handle);
        return $count;
    }

    public function countVehiclesWithFilters(string $csvFilePath, array $filters = []): int
    {
        return count($this->loadVehiclesFromCsvWithFilters($csvFilePath, $filters));
    }

    private function matchesFilters(
        array $vehicle, 
        ?string $vehicleTypeFilter, 
        ?string $makeFilter, 
        ?array $yearRange, 
        ?string $categoryFilter
    ): bool {
        // Filter by make
        if ($makeFilter && stripos($vehicle['make'], $makeFilter) === false) {
            return false;
        }

        // Filter by year range
        if ($yearRange) {
            $vehicleYear = (int) $vehicle['year'];
            if ($vehicleYear < $yearRange['start'] || $vehicleYear > $yearRange['end']) {
                return false;
            }
        }

        // Filter by category
        if ($categoryFilter && stripos($vehicle['category'], $categoryFilter) === false) {
            return false;
        }

        // Filter by vehicle type (requires detection)
        if ($vehicleTypeFilter) {
            $detectedType = $this->detectVehicleTypeForFilter($vehicle);
            if ($detectedType !== $vehicleTypeFilter) {
                return false;
            }
        }

        return true;
    }

    private function detectVehicleTypeForFilter(array $vehicle): string
    {
        if ($this->typeDetector) {
            return $this->typeDetector->detectVehicleType($vehicle);
        }

        return $this->detectVehicleTypeSimple($vehicle);
    }

    private function detectVehicleTypeSimple(array $vehicle): string
    {
        $category = strtolower($vehicle['category'] ?? '');
        
        // Check for electric
        if (strpos($category, 'electric') !== false || strpos($category, 'elétrico') !== false) {
            return 'electric';
        }

        // Check for hybrid
        if (strpos($category, 'hybrid') !== false || strpos($category, 'híbrido') !== false) {
            return 'hybrid';
        }

        // Check for motorcycle
        if (strpos($category, 'motorcycle') !== false || strpos($category, 'moto') !== false) {
            return 'motorcycle';
        }

        // Check tire pattern for motorcycle
        $tireSize = $vehicle['tire_size'] ?? '';
        if (preg_match('/\d+\/\d+-\d+.*\(dianteiro\).*\(traseiro\)/', $tireSize)) {
            return 'motorcycle';
        }

        // Check recommended oil for motorcycle
        $oil = strtolower($vehicle['recommended_oil'] ?? '');
        if (strpos($oil, 'moto') !== false || strpos($oil, '10w40') !== false) {
            return 'motorcycle';
        }

        // Default to car
        return 'car';
    }

    private function isValidVehicle(array $vehicle): bool
    {
        return !empty($vehicle['make']) &&
            !empty($vehicle['model']) &&
            !empty($vehicle['year']) &&
            !empty($vehicle['tire_size']);
    }

    private function normalizeVehicleData(array $vehicle): array
    {
        return [
            'make' => trim($vehicle['make']),
            'model' => trim($vehicle['model']),
            'year' => (int)$vehicle['year'],
            'tire_size' => trim($vehicle['tire_size'] ?? ''),
            'category' => trim($vehicle['category'] ?? ''),
            'recommended_oil' => trim($vehicle['recommended_oil'] ?? ''),
            'pressure_empty_front' => (int)($vehicle['pressure_empty_front'] ?? 30),
            'pressure_empty_rear' => (int)($vehicle['pressure_empty_rear'] ?? 28),
            'pressure_light_front' => (float)($vehicle['pressure_light_front'] ?? 32),
            'pressure_light_rear' => (float)($vehicle['pressure_light_rear'] ?? 30),
            'pressure_max_front' => (int)($vehicle['pressure_max_front'] ?? 36),
            'pressure_max_rear' => (int)($vehicle['pressure_max_rear'] ?? 34),
            'pressure_spare' => (float)($vehicle['pressure_spare'] ?? 35)
        ];
    }

    /**
     * Get vehicle statistics by type
     */
    public function getVehicleStatsByType(string $csvFilePath): array
    {
        $vehicles = $this->loadVehiclesFromCsv($csvFilePath);
        $stats = [
            'total' => count($vehicles),
            'by_type' => [],
            'by_make' => [],
            'year_range' => ['min' => null, 'max' => null]
        ];

        foreach ($vehicles as $vehicle) {
            // Count by type
            $type = $this->detectVehicleTypeSimple($vehicle);
            $stats['by_type'][$type] = ($stats['by_type'][$type] ?? 0) + 1;

            // Count by make
            $make = $vehicle['make'];
            $stats['by_make'][$make] = ($stats['by_make'][$make] ?? 0) + 1;

            // Track year range
            $year = (int) $vehicle['year'];
            if ($stats['year_range']['min'] === null || $year < $stats['year_range']['min']) {
                $stats['year_range']['min'] = $year;
            }
            if ($stats['year_range']['max'] === null || $year > $stats['year_range']['max']) {
                $stats['year_range']['max'] = $year;
            }
        }

        // Sort by count
        arsort($stats['by_type']);
        arsort($stats['by_make']);

        return $stats;
    }
}