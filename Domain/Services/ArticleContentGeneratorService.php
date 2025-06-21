<?php

namespace Src\ContentGeneration\ReviewSchedule\Domain\Services;

use Illuminate\Support\Facades\Log;
use Src\ContentGeneration\ReviewSchedule\Domain\Entities\ReviewScheduleArticle;
use Src\ContentGeneration\ReviewSchedule\Infrastructure\ContentTemplates\CarMaintenanceTemplate;
use Src\ContentGeneration\ReviewSchedule\Infrastructure\ContentTemplates\MotorcycleMaintenanceTemplate;
use Src\ContentGeneration\ReviewSchedule\Infrastructure\ContentTemplates\HybridVehicleMaintenanceTemplate;
use Src\ContentGeneration\ReviewSchedule\Infrastructure\ContentTemplates\ElectricVehicleMaintenanceTemplate;

class ArticleContentGeneratorService
{
    private CarMaintenanceTemplate $carTemplate;
    private MotorcycleMaintenanceTemplate $motorcycleTemplate;
    private ElectricVehicleMaintenanceTemplate $electricTemplate;
    private HybridVehicleMaintenanceTemplate $hybridTemplate;
    private VehicleTypeDetectorService $typeDetector;

    public function __construct(
        CarMaintenanceTemplate $carTemplate,
        MotorcycleMaintenanceTemplate $motorcycleTemplate,
        ElectricVehicleMaintenanceTemplate $electricTemplate,
        HybridVehicleMaintenanceTemplate $hybridTemplate,
        VehicleTypeDetectorService $typeDetector
    ) {
        $this->carTemplate = $carTemplate;
        $this->motorcycleTemplate = $motorcycleTemplate;
        $this->electricTemplate = $electricTemplate;
        $this->hybridTemplate = $hybridTemplate;
        $this->typeDetector = $typeDetector;
    }

    public function generateArticle(array $vehicleData): ReviewScheduleArticle
    {
        try {
            // Validar dados de entrada
            if (empty($vehicleData['make']) || empty($vehicleData['model'])) {
                throw new \Exception('Dados do veículo incompletos: make e model são obrigatórios');
            }

            $vehicleType = $this->typeDetector->detectVehicleType($vehicleData);
            $vehicleSubcategory = $this->typeDetector->detectVehicleSubcategory($vehicleData['category'] ?? '', $vehicleType);

            $template = $this->selectTemplate($vehicleType);

            // Log da seleção para debug
            Log::debug("Template selecionado", [
                'vehicle' => $this->getVehicleName($vehicleData),
                'vehicle_type' => $vehicleType,
                'template_class' => get_class($template)
            ]);

            $vehicleInfo = $this->buildVehicleInfo($vehicleData, $vehicleType, $vehicleSubcategory);
            $title = $this->generateTitle($vehicleInfo);
            $content = $this->generateContent($template, $vehicleData, $vehicleType, $vehicleSubcategory);

            return new ReviewScheduleArticle($title, $vehicleInfo, $content);
        } catch (\Exception $e) {
            Log::error("Erro ao gerar artigo em ArticleContentGeneratorService", [
                'vehicle_data' => $vehicleData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e; // Re-throw para ser tratado pelo service superior
        }
    }

    private function selectTemplate(string $vehicleType)
    {
        return match ($vehicleType) {
            'motorcycle' => $this->motorcycleTemplate,
            'electric' => $this->electricTemplate,
            'hybrid' => $this->hybridTemplate,
            default => $this->carTemplate
        };
    }

    private function buildVehicleInfo(array $vehicleData, string $vehicleType, string $vehicleSubcategory): array
    {
        return [
            'make' => $vehicleData['make'] ?? '',
            'model' => $vehicleData['model'] ?? '',
            'year' => (int)($vehicleData['year'] ?? date('Y')),
            'engine' => $this->extractEngine($vehicleData),
            'vehicle_type' => $vehicleType,
            'subcategory' => $vehicleSubcategory,
            'fuel_type' => $this->extractFuelType($vehicleData),
            'version' => $this->extractVersion($vehicleData)
        ];
    }

    private function generateTitle(array $vehicleInfo): string
    {
        return "Cronograma de Revisões do {$vehicleInfo['make']} {$vehicleInfo['model']} {$vehicleInfo['year']}";
    }

    private function generateContent($template, array $vehicleData, string $vehicleType, string $vehicleSubcategory): array
    {
        try {
            return [
                'introducao' => $this->safeTemplateCall($template, 'generateIntroduction', $vehicleData),
                'visao_geral_revisoes' => $this->safeTemplateCall($template, 'generateOverviewTable', $vehicleData),
                'cronograma_detalhado' => $this->safeTemplateCall($template, 'generateDetailedSchedule', $vehicleData),
                'manutencao_preventiva' => $this->safeTemplateCall($template, 'generatePreventiveMaintenance', $vehicleData),
                'pecas_atencao' => $this->safeTemplateCall($template, 'generateCriticalParts', $vehicleData),
                'especificacoes_tecnicas' => $this->safeTemplateCall($template, 'generateTechnicalSpecs', $vehicleData),
                'garantia_recomendacoes' => $this->safeTemplateCall($template, 'generateWarrantyInfo', $vehicleData),
                'perguntas_frequentes' => $this->safeTemplateCall($template, 'generateFAQs', $vehicleData),
                'consideracoes_finais' => $this->safeTemplateCall($template, 'generateConclusion', $vehicleData)
            ];
        } catch (\Exception $e) {
            // Log do erro específico da seção que falhou
            Log::error("Erro na geração de conteúdo: " . $e->getMessage(), [
                'vehicle_data' => $vehicleData,
                'vehicle_type' => $vehicleType,
                'template_class' => get_class($template),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw com contexto adicional
            throw new \Exception("Falha na geração de conteúdo para {$vehicleData['make']} {$vehicleData['model']} {$vehicleData['year']}: " . $e->getMessage(), 0, $e);
        }
    }

    private function safeTemplateCall($template, string $method, array $vehicleData)
    {
        try {
            if (!method_exists($template, $method)) {
                throw new \Exception("Método {$method} não existe no template " . get_class($template));
            }

            return $template->$method($vehicleData);
        } catch (\Exception $e) {
            // Log específico da seção que falhou
            Log::error("Erro no método {$method} do template " . get_class($template), [
                'error' => $e->getMessage(),
                'vehicle_data' => $vehicleData,
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            // Para manter a compatibilidade, retornar fallback baseado no tipo de método
            return $this->getMethodFallback($method, $vehicleData);
        }
    }

    private function getVehicleName(array $vehicleData): string
    {
        $make = $vehicleData['make'] ?? '';
        $model = $vehicleData['model'] ?? '';
        $year = $vehicleData['year'] ?? '';

        return trim("{$make} {$model} {$year}") ?: 'veículo';
    }

    private function getMethodFallback(string $method, array $vehicleData)
    {
        $vehicleName = $this->getVehicleName($vehicleData);

        return match ($method) {
            'generateIntroduction' => "Manter o seu {$vehicleName} em dia com as revisões é fundamental para garantir sua durabilidade, segurança e bom funcionamento.",

            'generateOverviewTable' => [
                ['revisao' => '1ª Revisão', 'intervalo' => '10.000 km ou 12 meses', 'principais_servicos' => 'Troca de óleo e filtros', 'estimativa_custo' => 'R$ 200 - R$ 300'],
                ['revisao' => '2ª Revisão', 'intervalo' => '20.000 km ou 24 meses', 'principais_servicos' => 'Óleo, filtros, velas', 'estimativa_custo' => 'R$ 250 - R$ 350'],
            ],

            'generateDetailedSchedule' => [
                [
                    'numero_revisao' => 1,
                    'intervalo' => '10.000 km ou 12 meses',
                    'km' => '10.000',
                    'servicos_principais' => ['Troca de óleo', 'Filtro de óleo', 'Verificação geral'],
                    'verificacoes_complementares' => ['Fluidos', 'Pneus', 'Luzes'],
                    'estimativa_custo' => 'R$ 200 - R$ 300',
                    'observacoes' => 'Primeira revisão importante'
                ]
            ],

            'generatePreventiveMaintenance' => [
                'verificacoes_mensais' => ['Óleo do motor', 'Água do radiador', 'Pneus', 'Luzes'],
                'verificacoes_trimestrais' => ['Fluido de freio', 'Bateria', 'Filtros']
            ],

            'generateCriticalParts' => [
                ['componente' => 'Freios', 'vida_util' => '40.000 km', 'sinais_desgaste' => 'Ruído ao frear'],
                ['componente' => 'Pneus', 'vida_util' => '50.000 km', 'sinais_desgaste' => 'Desgaste irregular']
            ],

            'generateTechnicalSpecs' => [
                'oleo_motor' => '5W30 Sintético',
                'capacidade_oleo' => '4.5 litros',
                'pressao_pneus' => '32 PSI',
                'filtro_combustivel' => 'A cada 20.000 km'
            ],

            'generateWarrantyInfo' => [
                'prazo_garantia' => '3 anos ou 100.000 km',
                'observacoes_importantes' => 'Revisões devem ser feitas dentro do prazo',
                'dicas_vida_util' => ['Dirigir com suavidade', 'Manter limpeza']
            ],

            'generateFAQs' => [
                ['pergunta' => 'Com que frequência devo revisar?', 'resposta' => 'A cada 10.000 km ou 12 meses'],
                ['pergunta' => 'Posso fazer em oficina independente?', 'resposta' => 'Sim, desde que use peças originais']
            ],

            'generateConclusion' => "Seguir o cronograma de revisões do {$vehicleName} é essencial para manter a garantia e preservar seu valor.",

            default => "Informação não disponível para {$vehicleName}"
        };
    }

    private function extractEngine(array $vehicleData): string
    {
        // Lógica para extrair motor baseada no modelo/make
        $make = strtolower($vehicleData['make'] ?? '');
        $model = strtolower($vehicleData['model'] ?? '');

        // Algumas inferências básicas
        if (strpos($model, 'turbo') !== false) {
            return '1.0 Turbo';
        }
        if (strpos($model, '1.6') !== false) {
            return '1.6';
        }
        if (strpos($model, '2.0') !== false) {
            return '2.0';
        }

        return '1.0'; // Default
    }

    private function extractFuelType(array $vehicleData): string
    {
        $model = strtolower($vehicleData['model'] ?? '');
        $category = strtolower($vehicleData['category'] ?? '');

        if (strpos($category, 'electric') !== false) {
            return 'elétrico';
        }
        if (strpos($category, 'hybrid') !== false) {
            return 'híbrido';
        }
        if (strpos($model, 'diesel') !== false) {
            return 'diesel';
        }

        return 'flex'; // Default no Brasil
    }

    private function extractVersion(array $vehicleData): string
    {
        $model = $vehicleData['model'] ?? '';

        // Extrair versões comuns
        if (strpos($model, 'LT') !== false) return 'LT';
        if (strpos($model, 'LTZ') !== false) return 'LTZ';
        if (strpos($model, 'Premier') !== false) return 'Premier';
        if (strpos($model, 'Touring') !== false) return 'Touring';
        if (strpos($model, 'Sport') !== false) return 'Sport';

        return 'Base';
    }
}
