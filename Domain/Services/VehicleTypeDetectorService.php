<?php

namespace Src\ContentGeneration\ReviewSchedule\Domain\Services;

class VehicleTypeDetectorService
{
    // Mapeamento expandido e mais preciso de categorias
    private const CATEGORY_MAPPING = [
        // Automóveis convencionais
        'hatch' => 'car',
        'sedan' => 'car',
        'suv' => 'car',
        'pickup' => 'car',
        'van' => 'car',
        'minivan' => 'car',
        'coupe' => 'car',
        'wagon' => 'car',
        'convertible' => 'car',
        
        // Automóveis com prefixo car_
        'car_sedan' => 'car',
        'car_hatchback' => 'car',
        'car_suv' => 'car',
        'car_pickup' => 'car',
        'car_sports' => 'car',
        
        // Híbridos
        'car_hybrid' => 'hybrid',
        'hybrid' => 'hybrid',
        'suv_hybrid' => 'hybrid',
        'suv_hibrido' => 'hybrid',
        
        // Elétricos
        'car_electric' => 'electric',
        'electric' => 'electric',
        'suv_electric' => 'electric',
        'hatch_electric' => 'electric',
        'sedan_electric' => 'electric',
        
        // Motocicletas convencionais
        'motorcycle_street' => 'motorcycle',
        'motorcycle_sport' => 'motorcycle',
        'motorcycle_trail' => 'motorcycle',
        'motorcycle_adventure' => 'motorcycle',
        'motorcycle_scooter' => 'motorcycle',
        'motorcycle_cruiser' => 'motorcycle',
        'motorcycle_touring' => 'motorcycle',
        'motorcycle_custom' => 'motorcycle',
        'motorcycle_naked' => 'motorcycle',
        'motorcycle_offroad' => 'motorcycle',
        
        // Motocicletas elétricas (tratamento especial)
        'motorcycle_electric' => 'electric',
        'moto_eletrica' => 'electric'
    ];

    // Padrões de pneus para identificação precisa
    private const TIRE_PATTERNS = [
        // Padrões de motocicletas (mais específicos)
        'motorcycle' => [
            '/\d+\/\d+-\d+.*\(dianteiro\).*\(traseiro\)/',
            '/\d+\/\d+-\d+.*dianteiro.*\d+\/\d+-\d+.*traseiro/',
            '/\d{2,3}\/\d{2}-\d{2}.*\d{2,3}\/\d{2}-\d{2}/',
            '/\d{2,3}\/\d{2}ZR\d{2}.*\d{2,3}\/\d{2}ZR\d{2}/',
            '/\d{2,3}\/\d{2}R\d{2}.*\d{2,3}\/\d{2}R\d{2}/'
        ],
        
        // Padrões de carros
        'car' => [
            '/\d{3}\/\d{2}\s*R\d{2}$/',
            '/\d{3}\/\d{2}\s*R\d{2}\s*\d{2}[HVW]?$/',
            '/P\d{3}\/\d{2}R\d{2}/'
        ]
    ];

    // Marcas conhecidas e seus tipos primários
    private const BRAND_TYPE_MAPPING = [
        // Marcas exclusivamente de carros
        'car_only' => [
            'ford', 'chevrolet', 'volkswagen', 'fiat', 'toyota', 'honda_auto',
            'hyundai', 'nissan', 'renault', 'peugeot', 'citroën', 'skoda',
            'seat', 'opel', 'mazda', 'subaru', 'mitsubishi_auto'
        ],
        
        // Marcas exclusivamente de motos
        'motorcycle_only' => [
            'yamaha_moto', 'suzuki_moto', 'kawasaki', 'ducati', 'harley-davidson',
            'triumph', 'ktm', 'bmw_moto', 'royal enfield', 'aprilia', 'mv agusta',
            'moto guzzi', 'benelli', 'cf moto', 'haojue', 'dafra', 'traxx',
            'shineray_moto'
        ],
        
        // Marcas premium de carros
        'premium_cars' => [
            'bmw', 'mercedes-benz', 'audi', 'volvo', 'jaguar', 'land rover',
            'porsche', 'lexus', 'infiniti', 'acura', 'cadillac', 'lincoln'
        ],
        
        // Marcas de elétricos
        'electric_specialist' => [
            'tesla', 'byd', 'nio', 'xpeng', 'lucid', 'rivian', 'polestar'
        ]
    ];

    // Palavras-chave para detecção de combustível/tipo
    private const FUEL_INDICATORS = [
        'electric' => ['electric', 'elétrico', 'ev', 'battery', 'bateria'],
        'hybrid' => ['hybrid', 'híbrido', 'hev', 'phev', 'plugin'],
        'diesel' => ['diesel', 'tdi', 'cdi', 'dci', 'crdi', 'dtec'],
        'flex' => ['flex', 'flexfuel', 'total flex', 'bicombustível']
    ];

    public function detectVehicleType(array $vehicleData): string
    {
        // 1. Verificação por categoria mapeada (mais confiável)
        $typeByCategory = $this->detectByCategory($vehicleData);
        if ($typeByCategory !== 'unknown') {
            return $typeByCategory;
        }

        // 2. Verificação por padrão de pneus (muito confiável para motos)
        $typeByTire = $this->detectByTirePattern($vehicleData);
        if ($typeByTire !== 'unknown') {
            return $typeByTire;
        }

        // 3. Verificação por marca (contexto adicional)
        $typeByBrand = $this->detectByBrand($vehicleData);
        if ($typeByBrand !== 'unknown') {
            return $typeByBrand;
        }

        // 4. Verificação por óleo recomendado (indicador indireto)
        $typeByOil = $this->detectByOilType($vehicleData);
        if ($typeByOil !== 'unknown') {
            return $typeByOil;
        }

        // 5. Verificação por modelo (último recurso)
        $typeByModel = $this->detectByModel($vehicleData);
        if ($typeByModel !== 'unknown') {
            return $typeByModel;
        }

        // Default para carro se nenhuma detecção funcionou
        return 'car';
    }

    public function detectVehicleSubcategory(string $category, string $mainType): string
    {
        $category = strtolower(trim($category));

        // Subcategorias para carros
        if ($mainType === 'car') {
            return $this->detectCarSubcategory($category);
        }

        // Subcategorias para motocicletas
        if ($mainType === 'motorcycle') {
            return $this->detectMotorcycleSubcategory($category);
        }

        // Subcategorias para veículos elétricos
        if ($mainType === 'electric') {
            return $this->detectElectricSubcategory($category);
        }

        // Subcategorias para híbridos
        if ($mainType === 'hybrid') {
            return $this->detectHybridSubcategory($category);
        }

        return $mainType . '_general';
    }

    private function detectByCategory(array $vehicleData): string
    {
        $category = strtolower(trim($vehicleData['category'] ?? ''));
        
        if (empty($category)) {
            return 'unknown';
        }

        // Verificação direta no mapeamento
        if (isset(self::CATEGORY_MAPPING[$category])) {
            return self::CATEGORY_MAPPING[$category];
        }

        // Verificações por palavras-chave na categoria
        $categoryChecks = [
            'electric' => ['electric', 'elétrico', 'ev'],
            'hybrid' => ['hybrid', 'híbrido', 'hev'],
            'motorcycle' => ['motorcycle', 'moto', 'bike'],
            'car' => ['car', 'carro', 'auto']
        ];

        foreach ($categoryChecks as $type => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($category, $keyword) !== false) {
                    return $type;
                }
            }
        }

        return 'unknown';
    }

    private function detectByTirePattern(array $vehicleData): string
    {
        $tireSize = trim($vehicleData['tire_size'] ?? '');
        
        if (empty($tireSize)) {
            return 'unknown';
        }

        // Verificar padrões de motocicleta (mais específicos primeiro)
        foreach (self::TIRE_PATTERNS['motorcycle'] as $pattern) {
            if (preg_match($pattern, $tireSize)) {
                return 'motorcycle';
            }
        }

        // Verificar se contém indicadores de moto
        $motorcycleIndicators = ['dianteiro', 'traseiro', 'front', 'rear'];
        foreach ($motorcycleIndicators as $indicator) {
            if (stripos($tireSize, $indicator) !== false) {
                return 'motorcycle';
            }
        }

        // Verificar padrões de carro
        foreach (self::TIRE_PATTERNS['car'] as $pattern) {
            if (preg_match($pattern, $tireSize)) {
                return 'car';
            }
        }

        return 'unknown';
    }

    private function detectByBrand(array $vehicleData): string
    {
        $make = strtolower(trim($vehicleData['make'] ?? ''));
        
        if (empty($make)) {
            return 'unknown';
        }

        // Normalizar nome da marca
        $normalizedMake = $this->normalizeBrandName($make);

        // Verificar marcas específicas
        foreach (self::BRAND_TYPE_MAPPING as $type => $brands) {
            if (in_array($normalizedMake, $brands)) {
                return match($type) {
                    'car_only', 'premium_cars' => 'car',
                    'motorcycle_only' => 'motorcycle',
                    'electric_specialist' => 'electric',
                    default => 'car'
                };
            }
        }

        // Marcas que fazem tanto carros quanto motos
        $dualBrands = ['honda', 'yamaha', 'suzuki', 'bmw'];
        if (in_array($normalizedMake, $dualBrands)) {
            // Precisará de verificação adicional por outros métodos
            return 'unknown';
        }

        return 'unknown';
    }

    private function detectByOilType(array $vehicleData): string
    {
        $recommendedOil = strtolower(trim($vehicleData['recommended_oil'] ?? ''));
        
        if (empty($recommendedOil)) {
            return 'unknown';
        }

        // Veículos elétricos não usam óleo de motor
        if (strpos($recommendedOil, 'não usa') !== false || 
            strpos($recommendedOil, 'na') === 0) {
            return 'electric';
        }

        // Óleos típicos de motocicletas (menor volume, viscosidades específicas)
        $motorcycleOilPatterns = [
            '/10w30.*semissintético.*moto/i',
            '/10w40.*semissintético.*moto/i',
            '/capacidade.*0\.[5-9].*litro/i', // Capacidades pequenas típicas de motos
            '/20w50.*mineral/i' // Óleo comum em motos antigas
        ];

        foreach ($motorcycleOilPatterns as $pattern) {
            if (preg_match($pattern, $recommendedOil)) {
                return 'motorcycle';
            }
        }

        return 'unknown';
    }

    private function detectByModel(array $vehicleData): string
    {
        $model = strtolower(trim($vehicleData['model'] ?? ''));
        
        if (empty($model)) {
            return 'unknown';
        }

        // Palavras-chave que indicam motocicletas
        $motorcycleKeywords = [
            'cg', 'cb', 'cbr', 'fazer', 'factor', 'ybr', 'mt', 'ninja', 
            'z400', 'z650', 'duke', 'adventure', 'bros', 'xre', 'titan',
            'fan', 'pop', 'biz', 'pcx', 'nmax', 'elite', 'neo'
        ];

        foreach ($motorcycleKeywords as $keyword) {
            if (strpos($model, $keyword) !== false) {
                return 'motorcycle';
            }
        }

        // Palavras-chave que indicam carros elétricos
        $electricKeywords = ['ev', 'electric', 'e-tron', 'leaf', 'model', 'bolt'];
        foreach ($electricKeywords as $keyword) {
            if (strpos($model, $keyword) !== false) {
                return 'electric';
            }
        }

        // Palavras-chave que indicam híbridos
        $hybridKeywords = ['prius', 'insight', 'accord hybrid', 'camry hybrid'];
        foreach ($hybridKeywords as $keyword) {
            if (strpos($model, $keyword) !== false) {
                return 'hybrid';
            }
        }

        return 'unknown';
    }

    private function detectCarSubcategory(string $category): string
    {
        $subcategoryMap = [
            'hatch' => 'hatch',
            'hatchback' => 'hatch',
            'sedan' => 'sedan',
            'suv' => 'suv',
            'pickup' => 'pickup',
            'van' => 'van',
            'minivan' => 'van',
            'wagon' => 'wagon',
            'coupe' => 'coupe',
            'convertible' => 'convertible',
            'sports' => 'sports',
            'sport' => 'sports'
        ];

        foreach ($subcategoryMap as $key => $subcategory) {
            if (strpos($category, $key) !== false) {
                return $subcategory;
            }
        }

        return 'car_general';
    }

    private function detectMotorcycleSubcategory(string $category): string
    {
        $subcategoryMap = [
            'sport' => 'motorcycle_sport',
            'adventure' => 'motorcycle_adventure',
            'trail' => 'motorcycle_trail',
            'scooter' => 'motorcycle_scooter',
            'custom' => 'motorcycle_custom',
            'cruiser' => 'motorcycle_cruiser',
            'touring' => 'motorcycle_touring',
            'street' => 'motorcycle_street',
            'naked' => 'motorcycle_naked',
            'offroad' => 'motorcycle_offroad'
        ];

        foreach ($subcategoryMap as $key => $subcategory) {
            if (strpos($category, $key) !== false) {
                return $subcategory;
            }
        }

        return 'motorcycle_general';
    }

    private function detectElectricSubcategory(string $category): string
    {
        // Determinar se é moto elétrica ou carro elétrico
        if (strpos($category, 'motorcycle') !== false || strpos($category, 'moto') !== false) {
            return 'motorcycle_electric';
        }

        // Subcategorias de carros elétricos
        if (strpos($category, 'suv') !== false) {
            return 'suv_electric';
        }
        if (strpos($category, 'hatch') !== false) {
            return 'hatch_electric';
        }
        if (strpos($category, 'sedan') !== false) {
            return 'sedan_electric';
        }

        return 'car_electric';
    }

    private function detectHybridSubcategory(string $category): string
    {
        // Subcategorias de híbridos
        if (strpos($category, 'suv') !== false) {
            return 'suv_hybrid';
        }
        if (strpos($category, 'sedan') !== false) {
            return 'sedan_hybrid';
        }
        if (strpos($category, 'hatch') !== false) {
            return 'hatch_hybrid';
        }

        return 'car_hybrid';
    }

    private function normalizeBrandName(string $make): string
    {
        $normalizations = [
            'mercedes-benz' => 'mercedes-benz',
            'mercedes' => 'mercedes-benz',
            'bmw_motorrad' => 'bmw_moto',
            'harley davidson' => 'harley-davidson',
            'harley' => 'harley-davidson',
            'royal enfield' => 'royal enfield',
            'caoa chery' => 'chery',
            'super soco' => 'super soco'
        ];

        return $normalizations[$make] ?? $make;
    }

    /**
     * Método público para verificar se é motocicleta elétrica
     */
    public function isElectricMotorcycle(array $vehicleData): bool
    {
        $type = $this->detectVehicleType($vehicleData);
        $subcategory = $this->detectVehicleSubcategory($vehicleData['category'] ?? '', $type);
        
        return $type === 'electric' && strpos($subcategory, 'motorcycle') !== false;
    }

    /**
     * Método público para obter informações completas do veículo
     */
    public function getVehicleTypeInfo(array $vehicleData): array
    {
        $mainType = $this->detectVehicleType($vehicleData);
        $subcategory = $this->detectVehicleSubcategory($vehicleData['category'] ?? '', $mainType);
        
        return [
            'main_type' => $mainType,
            'subcategory' => $subcategory,
            'is_electric_motorcycle' => $this->isElectricMotorcycle($vehicleData),
            'detection_confidence' => $this->calculateDetectionConfidence($vehicleData, $mainType),
            'detection_methods' => $this->getDetectionMethods($vehicleData, $mainType)
        ];
    }

    private function calculateDetectionConfidence(array $vehicleData, string $detectedType): string
    {
        $confidence = 0;
        
        // Categoria mapeada (+40 pontos)
        if (isset(self::CATEGORY_MAPPING[strtolower($vehicleData['category'] ?? '')])) {
            $confidence += 40;
        }
        
        // Padrão de pneu correspondente (+30 pontos)
        if ($this->detectByTirePattern($vehicleData) === $detectedType) {
            $confidence += 30;
        }
        
        // Marca corresponde (+20 pontos)
        if ($this->detectByBrand($vehicleData) === $detectedType) {
            $confidence += 20;
        }
        
        // Óleo corresponde (+10 pontos)
        if ($this->detectByOilType($vehicleData) === $detectedType) {
            $confidence += 10;
        }

        return match(true) {
            $confidence >= 80 => 'high',
            $confidence >= 60 => 'medium',
            $confidence >= 40 => 'low',
            default => 'very_low'
        };
    }

    private function getDetectionMethods(array $vehicleData, string $detectedType): array
    {
        $methods = [];
        
        if (isset(self::CATEGORY_MAPPING[strtolower($vehicleData['category'] ?? '')])) {
            $methods[] = 'category_mapping';
        }
        
        if ($this->detectByTirePattern($vehicleData) === $detectedType) {
            $methods[] = 'tire_pattern';
        }
        
        if ($this->detectByBrand($vehicleData) === $detectedType) {
            $methods[] = 'brand_classification';
        }
        
        if ($this->detectByOilType($vehicleData) === $detectedType) {
            $methods[] = 'oil_type_inference';
        }
        
        if ($this->detectByModel($vehicleData) === $detectedType) {
            $methods[] = 'model_keywords';
        }
        
        return empty($methods) ? ['default_fallback'] : $methods;
    }
}