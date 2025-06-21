<?php

namespace Src\ContentGeneration\ReviewSchedule\Application\DTOs;

use Illuminate\Support\Facades\Log;

class VehicleData
{
    public string $make;
    public string $model;
    public int $year;
    public string $tireSize;
    public string $category;
    public ?string $recommendedOil;
    public int $pressureEmptyFront;
    public int $pressureEmptyRear;
    public float $pressureLightFront;
    public float $pressureLightRear;
    public int $pressureMaxFront;
    public int $pressureMaxRear;
    public float $pressureSpare;

    public function __construct(array $data)
    {
        try {
            $this->make = trim($data['make'] ?? '');
            $this->model = trim($data['model'] ?? '');
            $this->year = $this->validateYear($data['year'] ?? date('Y'));
            $this->tireSize = trim($data['tire_size'] ?? '');
            $this->category = trim($data['category'] ?? '');
            $this->recommendedOil = !empty($data['recommended_oil']) ? trim($data['recommended_oil']) : null;
            
            // Pressões com valores padrão seguros
            $this->pressureEmptyFront = $this->validatePressure($data['pressure_empty_front'] ?? 30, 30);
            $this->pressureEmptyRear = $this->validatePressure($data['pressure_empty_rear'] ?? 28, 28);
            $this->pressureLightFront = $this->validatePressure($data['pressure_light_front'] ?? 32, 32);
            $this->pressureLightRear = $this->validatePressure($data['pressure_light_rear'] ?? 30, 30);
            $this->pressureMaxFront = $this->validatePressure($data['pressure_max_front'] ?? 36, 36);
            $this->pressureMaxRear = $this->validatePressure($data['pressure_max_rear'] ?? 34, 34);
            $this->pressureSpare = $this->validatePressure($data['pressure_spare'] ?? 35, 35);
            
            // Log para debug se dados parecem suspeitos
            if (!$this->isValid()) {
                Log::warning('VehicleData created with invalid data', [
                    'make' => $this->make,
                    'model' => $this->model,
                    'year' => $this->year,
                    'tire_size' => $this->tireSize,
                    'validation_issues' => $this->getValidationIssues()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error creating VehicleData: ' . $e->getMessage(), [
                'input_data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function validateYear($year): int
    {
        $yearInt = (int)$year;
        $currentYear = (int)date('Y');
        
        // Anos válidos: 1980 até 3 anos no futuro
        if ($yearInt < 1980 || $yearInt > ($currentYear + 3)) {
            Log::warning('Invalid year provided, using current year', [
                'provided_year' => $year,
                'using_year' => $currentYear
            ]);
            return $currentYear;
        }
        
        return $yearInt;
    }

    private function validatePressure($pressure, $default): float
    {
        $pressureFloat = (float)$pressure;
        
        // Pressões válidas: 15 a 50 PSI
        if ($pressureFloat < 15 || $pressureFloat > 50) {
            return (float)$default;
        }
        
        return $pressureFloat;
    }

    public function toArray(): array
    {
        return [
            'make' => $this->make,
            'model' => $this->model,
            'year' => $this->year,
            'tire_size' => $this->tireSize,
            'category' => $this->category,
            'recommended_oil' => $this->recommendedOil,
            'pressure_empty_front' => $this->pressureEmptyFront,
            'pressure_empty_rear' => $this->pressureEmptyRear,
            'pressure_light_front' => $this->pressureLightFront,
            'pressure_light_rear' => $this->pressureLightRear,
            'pressure_max_front' => $this->pressureMaxFront,
            'pressure_max_rear' => $this->pressureMaxRear,
            'pressure_spare' => $this->pressureSpare
        ];
    }

    public function getFullName(): string
    {
        return trim("{$this->make} {$this->model} {$this->year}");
    }

    public function isMotorcycle(): bool
    {
        $lowerCategory = strtolower($this->category);
        $lowerModel = strtolower($this->model);
        
        // Verificações por categoria
        if (strpos($lowerCategory, 'motorcycle') !== false || 
            strpos($lowerCategory, 'moto') !== false) {
            return true;
        }
        
        // Verificações por padrão de pneu
        if (strpos($this->tireSize, '/') !== false && 
            (strpos($this->tireSize, 'dianteiro') !== false || 
             strpos($this->tireSize, 'traseiro') !== false)) {
            return true;
        }
        
        // Verificações por modelo
        $motorcycleKeywords = ['cb', 'cbr', 'ninja', 'yzf', 'gsxr', 'mt', 'z1000', 'hornet'];
        foreach ($motorcycleKeywords as $keyword) {
            if (strpos($lowerModel, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    public function isElectric(): bool
    {
        $lowerCategory = strtolower($this->category);
        $lowerModel = strtolower($this->model);
        
        return strpos($lowerCategory, 'electric') !== false ||
               strpos($lowerCategory, 'eletric') !== false ||
               strpos($lowerModel, 'electric') !== false ||
               strpos($lowerModel, 'ev') === 0 ||
               strpos($lowerModel, 'ioniq') !== false ||
               strpos($lowerModel, 'leaf') !== false;
    }

    public function isHybrid(): bool
    {
        $lowerCategory = strtolower($this->category);
        $lowerModel = strtolower($this->model);
        
        return strpos($lowerCategory, 'hybrid') !== false ||
               strpos($lowerCategory, 'hibrido') !== false ||
               strpos($lowerModel, 'hybrid') !== false ||
               strpos($lowerModel, 'prius') !== false;
    }

    public function getVehicleType(): string
    {
        if ($this->isElectric()) {
            return 'electric';
        }
        
        if ($this->isHybrid()) {
            return 'hybrid';
        }
        
        if ($this->isMotorcycle()) {
            return 'motorcycle';
        }
        
        return 'car';
    }

    public function isValid(): bool
    {
        $issues = $this->getValidationIssues();
        return empty($issues);
    }

    public function getValidationIssues(): array
    {
        $issues = [];
        
        if (empty($this->make)) {
            $issues[] = 'make_empty';
        }
        
        if (empty($this->model)) {
            $issues[] = 'model_empty';
        }
        
        if ($this->year < 1980 || $this->year > (date('Y') + 3)) {
            $issues[] = 'year_invalid';
        }
        
        if (empty($this->tireSize)) {
            $issues[] = 'tire_size_empty';
        }
        
        // Validação adicional para categoria suspeita
        if (empty($this->category)) {
            $issues[] = 'category_empty';
        }
        
        return $issues;
    }

    /**
     * Método para debug - retorna informações detalhadas
     */
    public function getDebugInfo(): array
    {
        return [
            'basic_data' => [
                'make' => $this->make,
                'model' => $this->model,
                'year' => $this->year,
                'full_name' => $this->getFullName()
            ],
            'classification' => [
                'vehicle_type' => $this->getVehicleType(),
                'is_motorcycle' => $this->isMotorcycle(),
                'is_electric' => $this->isElectric(),
                'is_hybrid' => $this->isHybrid()
            ],
            'validation' => [
                'is_valid' => $this->isValid(),
                'issues' => $this->getValidationIssues()
            ],
            'technical_data' => [
                'tire_size' => $this->tireSize,
                'category' => $this->category,
                'recommended_oil' => $this->recommendedOil,
                'pressures' => [
                    'empty_front' => $this->pressureEmptyFront,
                    'empty_rear' => $this->pressureEmptyRear,
                    'spare' => $this->pressureSpare
                ]
            ]
        ];
    }

    /**
     * Método estático para criar VehicleData com dados mínimos
     */
    public static function createMinimal(string $make, string $model, int $year): self
    {
        return new self([
            'make' => $make,
            'model' => $model,
            'year' => $year,
            'tire_size' => 'unknown',
            'category' => 'car'
        ]);
    }

    /**
     * Método para verificar se os dados são suficientes para gerar artigo
     */
    public function hasMinimumDataForArticle(): bool
    {
        return !empty($this->make) && 
               !empty($this->model) && 
               $this->year >= 1980 && 
               $this->year <= (date('Y') + 3);
    }

    /**
     * Método para limpar e normalizar dados de entrada
     */
    public static function sanitizeInputData(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = trim($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        // Normalizar campos específicos
        if (isset($sanitized['make'])) {
            $sanitized['make'] = ucfirst(strtolower($sanitized['make']));
        }
        
        if (isset($sanitized['model'])) {
            $sanitized['model'] = ucwords(strtolower($sanitized['model']));
        }
        
        return $sanitized;
    }

    /**
     * Método para obter informações do segmento do veículo
     */
    public function getSegmentInfo(): array
    {
        $make = strtolower($this->make);
        $model = strtolower($this->model);
        $category = strtolower($this->category);
        
        // Detectar segmento premium
        $premiumBrands = ['bmw', 'audi', 'mercedes-benz', 'volvo', 'lexus', 'porsche'];
        $isPremium = in_array($make, $premiumBrands);
        
        // Detectar tipo de veículo por categoria
        $vehicleSegment = 'compact';
        if (strpos($category, 'suv') !== false) {
            $vehicleSegment = 'suv';
        } elseif (strpos($category, 'pickup') !== false) {
            $vehicleSegment = 'pickup';
        } elseif (strpos($category, 'sedan') !== false) {
            $vehicleSegment = 'sedan';
        } elseif (strpos($category, 'hatch') !== false) {
            $vehicleSegment = 'hatchback';
        }
        
        return [
            'segment' => $vehicleSegment,
            'is_premium' => $isPremium,
            'brand_origin' => $this->getBrandOrigin($make),
            'estimated_price_range' => $this->getEstimatedPriceRange($make, $vehicleSegment, $isPremium)
        ];
    }
    
    private function getBrandOrigin(string $make): string
    {
        $origins = [
            'toyota' => 'japanese',
            'honda' => 'japanese',
            'nissan' => 'japanese',
            'mazda' => 'japanese',
            'subaru' => 'japanese',
            'mitsubishi' => 'japanese',
            'suzuki' => 'japanese',
            'hyundai' => 'korean',
            'kia' => 'korean',
            'volkswagen' => 'german',
            'audi' => 'german',
            'bmw' => 'german',
            'mercedes-benz' => 'german',
            'porsche' => 'german',
            'ford' => 'american',
            'chevrolet' => 'american',
            'jeep' => 'american',
            'dodge' => 'american',
            'ram' => 'american',
            'fiat' => 'italian',
            'peugeot' => 'french',
            'citroën' => 'french',
            'renault' => 'french',
            'volvo' => 'swedish',
            'byd' => 'chinese',
            'gwm' => 'chinese',
            'caoa chery' => 'chinese'
        ];
        
        return $origins[strtolower($make)] ?? 'unknown';
    }
    
    private function getEstimatedPriceRange(string $make, string $segment, bool $isPremium): string
    {
        if ($isPremium) {
            return match($segment) {
                'compact' => 'R$ 150.000 - R$ 250.000',
                'sedan' => 'R$ 200.000 - R$ 350.000',
                'suv' => 'R$ 250.000 - R$ 500.000',
                'pickup' => 'R$ 300.000 - R$ 600.000',
                default => 'R$ 150.000 - R$ 400.000'
            };
        }
        
        return match($segment) {
            'compact' => 'R$ 50.000 - R$ 100.000',
            'hatchback' => 'R$ 60.000 - R$ 120.000',
            'sedan' => 'R$ 80.000 - R$ 150.000',
            'suv' => 'R$ 90.000 - R$ 200.000',
            'pickup' => 'R$ 100.000 - R$ 250.000',
            default => 'R$ 60.000 - R$ 120.000'
        };
    }

    /**
     * Método para converter para formato específico do template
     */
    public function toTemplateFormat(): array
    {
        $base = $this->toArray();
        $segmentInfo = $this->getSegmentInfo();
        
        return array_merge($base, [
            'vehicle_type' => $this->getVehicleType(),
            'segment_info' => $segmentInfo,
            'full_name' => $this->getFullName(),
            'classification' => [
                'is_motorcycle' => $this->isMotorcycle(),
                'is_electric' => $this->isElectric(),
                'is_hybrid' => $this->isHybrid(),
                'is_premium' => $segmentInfo['is_premium']
            ],
            'technical_specs' => [
                'estimated_displacement' => $this->getEstimatedDisplacement(),
                'estimated_fuel_capacity' => $this->getEstimatedFuelCapacity(),
                'drivetrain' => $this->getEstimatedDrivetrain()
            ]
        ]);
    }
    
    private function getEstimatedDisplacement(): string
    {
        $model = strtolower($this->model);
        
        // Detectar motorização pelo modelo
        if (strpos($model, '1.0') !== false || strpos($model, 'mobi') !== false) {
            return '1.0';
        }
        if (strpos($model, '1.3') !== false) {
            return '1.3';
        }
        if (strpos($model, '1.4') !== false) {
            return '1.4';
        }
        if (strpos($model, '1.6') !== false) {
            return '1.6';
        }
        if (strpos($model, '2.0') !== false) {
            return '2.0';
        }
        if (strpos($model, 'turbo') !== false) {
            return '1.0 Turbo';
        }
        
        // Estimativa por segmento
        $segment = $this->getSegmentInfo()['segment'];
        return match($segment) {
            'compact' => '1.0',
            'hatchback' => '1.3',
            'sedan' => '1.6',
            'suv' => '2.0',
            'pickup' => '2.8',
            default => '1.6'
        };
    }
    
    private function getEstimatedFuelCapacity(): string
    {
        if ($this->isElectric()) {
            return 'N/A (Veículo Elétrico)';
        }
        
        $segment = $this->getSegmentInfo()['segment'];
        return match($segment) {
            'compact' => '45-50 litros',
            'hatchback' => '50-55 litros',
            'sedan' => '55-65 litros',
            'suv' => '60-70 litros',
            'pickup' => '70-80 litros',
            default => '50-60 litros'
        };
    }
    
    private function getEstimatedDrivetrain(): string
    {
        $category = strtolower($this->category);
        $model = strtolower($this->model);
        
        if (strpos($category, 'suv') !== false || strpos($category, 'pickup') !== false) {
            return 'Tração dianteira/4x4 (dependendo da versão)';
        }
        
        if (strpos($model, '4x4') !== false || strpos($model, 'awd') !== false) {
            return 'Tração integral (AWD/4x4)';
        }
        
        return 'Tração dianteira';
    }

    /**
     * Método para export em JSON com formatação
     */
    public function toJson(bool $prettyPrint = true): string
    {
        $data = $this->toTemplateFormat();
        $flags = JSON_UNESCAPED_UNICODE;
        
        if ($prettyPrint) {
            $flags |= JSON_PRETTY_PRINT;
        }
        
        return json_encode($data, $flags);
    }
}