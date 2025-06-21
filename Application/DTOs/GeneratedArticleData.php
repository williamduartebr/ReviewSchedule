<?php

namespace Src\ContentGeneration\ReviewSchedule\Application\DTOs;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class GeneratedArticleData
{
    private string $title;
    private string $slug;
    private array $vehicleInfo;
    private array $extractedEntities;
    private array $seoData;
    private array $content;
    private string $template;
    private string $status;
    private string $source;
    private string $domain;
    private array $metadata;
    private array $qualityMetrics;

    // Sistema de controle de variações SEO (reutilizando do ReviewScheduleArticle)
    private static array $usedSeoVariations = [];

    // Configurações de qualidade do conteúdo
    private const QUALITY_THRESHOLDS = [
        'min_introduction_length' => 100,
        'min_conclusion_length' => 80,
        'min_faq_count' => 3,
        'max_faq_count' => 8,
        'min_revision_count' => 4,
        'max_revision_count' => 8,
        'min_critical_parts' => 3,
        'max_critical_parts' => 8
    ];

    // Mapeamento de templates por tipo de veículo
    private const TEMPLATE_MAPPING = [
        'car' => 'review_schedule_car',
        'motorcycle' => 'review_schedule_motorcycle',
        'electric' => 'review_schedule_electric',
        'hybrid' => 'review_schedule_hybrid'
    ];

    public function __construct(
        string $title,
        array $vehicleInfo,
        array $content,
        string $status = 'draft'
    ) {
        $this->title = $title;
        $this->vehicleInfo = $vehicleInfo;
        $this->content = $content;
        $this->status = $status;
        $this->source = 'intelligent_generator';
        $this->domain = 'review_schedule';

        // Detectar template baseado no tipo de veículo
        $this->template = $this->detectTemplate($vehicleInfo);

        // Gerar componentes com sistema inteligente
        $this->slug = $this->generateIntelligentSlug($vehicleInfo);
        $this->extractedEntities = $this->extractEnhancedEntities($vehicleInfo);
        $this->seoData = $this->generateVariedSeoData($vehicleInfo);
        $this->metadata = $this->generateMetadata($vehicleInfo, $content);
        $this->qualityMetrics = $this->calculateQualityMetrics($content);

        // Log da criação para auditoria
        $this->logCreation();
    }

    // Getters públicos
    public function getTitle(): string
    {
        return $this->title;
    }
    public function getSlug(): string
    {
        return $this->slug;
    }
    public function getVehicleInfo(): array
    {
        return $this->vehicleInfo;
    }
    public function getExtractedEntities(): array
    {
        return $this->extractedEntities;
    }
    public function getSeoData(): array
    {
        return $this->seoData;
    }
    public function getContent(): array
    {
        return $this->content;
    }
    public function getTemplate(): string
    {
        return $this->template;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getSource(): string
    {
        return $this->source;
    }
    public function getDomain(): string
    {
        return $this->domain;
    }
    public function getMetadata(): array
    {
        return $this->metadata;
    }
    public function getQualityMetrics(): array
    {
        return $this->qualityMetrics;
    }

    public function publish(): void
    {
        $this->status = 'published';
        Log::info("Article published", [
            'slug' => $this->slug,
            'vehicle' => $this->getVehicleIdentifier(),
            'quality_score' => $this->qualityMetrics['overall_score']
        ]);
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'slug' => "cronograma-revisoes-" . $this->slug,
            'new_slug' => "revisao-" . $this->slug,
            'vehicle_info' => $this->vehicleInfo,
            'extracted_entities' => $this->extractedEntities,
            'seo_data' => $this->seoData,
            'content' => $this->content,
            'template' => $this->template,
            'status' => $this->status,
            'source' => $this->source,
            'domain' => $this->domain,
            'metadata' => $this->metadata,
            'quality_metrics' => $this->qualityMetrics,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    public function toMongoDocument(): array
    {
        $document = $this->toArray();

        // Adicionar índices específicos para MongoDB
        $document['search_terms'] = $this->generateSearchTerms();
        $document['vehicle_key'] = $this->generateVehicleKey();
        $document['content_hash'] = $this->generateContentHash();

        return $document;
    }

    public function isHighQuality(): bool
    {
        return $this->qualityMetrics['overall_score'] >= 85;
    }

    public function getQualityIssues(): array
    {
        return $this->qualityMetrics['issues'] ?? [];
    }

    private function detectTemplate(array $vehicleInfo): string
    {
        $vehicleType = $vehicleInfo['vehicle_type'] ?? 'car';
        return self::TEMPLATE_MAPPING[$vehicleType] ?? self::TEMPLATE_MAPPING['car'];
    }

    private function generateIntelligentSlug(array $vehicleInfo): string
    {
        $make = $vehicleInfo['make'] ?? '';
        $model = $vehicleInfo['model'] ?? '';
        $year = $vehicleInfo['year'] ?? '';
        $vehicleType = $vehicleInfo['vehicle_type'] ?? 'car';

        // Slug base
        $baseSlug = "{$make}-{$model}-{$year}";

        // Adicionar tipo se não for carro convencional
        if ($vehicleType !== 'car') {
            $typeMap = [
                'motorcycle' => 'moto',
                'electric' => 'eletrico',
                'hybrid' => 'hibrido'
            ];

            if (isset($typeMap[$vehicleType])) {
                $baseSlug .= '-' . $typeMap[$vehicleType];
            }
        }

        // Limpar e gerar slug final
        return Str::slug($baseSlug);
    }

    private function extractEnhancedEntities(array $vehicleInfo): array
    {
        $entities = [
            // Entidades básicas
            'marca' => $vehicleInfo['make'] ?? '',
            'modelo' => $vehicleInfo['model'] ?? '',
            'ano' => (string)($vehicleInfo['year'] ?? ''),
            'tipo_veiculo' => $this->getVehicleTypeInPortuguese($vehicleInfo['vehicle_type'] ?? 'car'),
            'categoria' => $vehicleInfo['subcategory'] ?? $vehicleInfo['category'] ?? '',

            // Entidades enriquecidas
            'motorizacao' => $vehicleInfo['extracted_engine'] ?? $vehicleInfo['engine'] ?? '',
            'combustivel' => $vehicleInfo['extracted_fuel_type'] ?? $vehicleInfo['fuel_type'] ?? 'flex',
            'versao' => $vehicleInfo['extracted_version'] ?? $vehicleInfo['version'] ?? '',
            'segmento' => $vehicleInfo['segment'] ?? 'intermediario',
            'perfil_uso' => $vehicleInfo['usage_profile'] ?? 'geral',

            // Entidades específicas
            'pressao_pneu_dianteiro' => $vehicleInfo['pressure_empty_front'] ?? null,
            'pressao_pneu_traseiro' => $vehicleInfo['pressure_empty_rear'] ?? null,
            'oleo_recomendado' => $vehicleInfo['recommended_oil'] ?? '',
            'confianca_deteccao' => $vehicleInfo['detection_confidence'] ?? 'medium'
        ];

        // Remover entidades vazias
        return array_filter($entities, fn($value) => $value !== '' && $value !== null);
    }

    private function generateVariedSeoData(array $vehicleInfo): array
    {
        $make = $vehicleInfo['make'] ?? '';
        $model = $vehicleInfo['model'] ?? '';
        $year = $vehicleInfo['year'] ?? '';
        $vehicleType = $vehicleInfo['vehicle_type'] ?? 'car';

        // Usar o mesmo sistema de variações do ReviewScheduleArticle
        $seoVariation = $this->getSeoVariation($vehicleInfo);

        // H2 tags específicas por tipo
        $h2Tags = $this->getVariedH2Tags($vehicleType);

        // Keywords secundárias contextualizadas
        $secondaryKeywords = $this->getContextualSecondaryKeywords($make, $model, $vehicleType, $vehicleInfo);

        return [
            'page_title' => $seoVariation['page_title'],
            'meta_description' => $seoVariation['meta_description'],
            'url_slug' => "revisao-" . $this->slug,
            'h1' => "Cronograma de Revisões do {$make} {$model} {$year}",
            'h2_tags' => $h2Tags,
            'primary_keyword' => "cronograma revisões " . Str::lower("{$make} {$model} {$year}"),
            'secondary_keywords' => $secondaryKeywords,
            'meta_robots' => 'index,follow',
            'canonical_url' => $this->slug,
            'schema_type' => 'Article',
            'article_section' => 'Automotive',
            'target_audience' => $this->determineTargetAudience($vehicleInfo)
        ];
    }

    private function getSeoVariation(array $vehicleInfo): array
    {
        $make = $vehicleInfo['make'] ?? '';
        $model = $vehicleInfo['model'] ?? '';
        $year = $vehicleInfo['year'] ?? '';
        $vehicleType = $vehicleInfo['vehicle_type'] ?? 'car';

        // Criar chave para agrupamento (similar ao ReviewScheduleArticle)
        $vehicleKey = $this->getVehicleKeyForSeo($vehicleInfo);

        // Variações de page_title
        $pageTitleVariations = $this->getPageTitleVariations($make, $model, $year, $vehicleType);

        // Variações de meta_description
        $metaDescriptionVariations = $this->getMetaDescriptionVariations($make, $model, $year, $vehicleType);

        // Selecionar variações não usadas
        $selectedPageTitle = $this->selectUnusedVariation($pageTitleVariations, $vehicleKey, 'page_title');
        $selectedMetaDescription = $this->selectUnusedVariation($metaDescriptionVariations, $vehicleKey, 'meta_description');

        return [
            'page_title' => $selectedPageTitle,
            'meta_description' => $selectedMetaDescription
        ];
    }

    private function getPageTitleVariations(string $make, string $model, string $year, string $vehicleType): array
    {
        $baseVariations = [
            "Cronograma de Revisões do {$make} {$model} {$year} - Guia Completo",
            "Cronograma de Revisões {$make} {$model} {$year} - Manual de Manutenção",
            "Revisões Programadas {$make} {$model} {$year} - Cronograma Detalhado",
            "Manutenção {$make} {$model} {$year} - Cronograma de Revisões Completo",
            "Cronograma Oficial de Revisões {$make} {$model} {$year}",
            "Revisões {$make} {$model} {$year} - Guia de Manutenção Preventiva",
            "Cronograma de Manutenção {$make} {$model} {$year} - Revisões Programadas",
            "Guia de Revisões {$make} {$model} {$year} - Cronograma Completo",
            "Manual de Revisões {$make} {$model} {$year} - Manutenção Programada"
        ];

        // Variações específicas por tipo de veículo
        switch ($vehicleType) {
            case 'motorcycle':
                return array_merge($baseVariations, [
                    "Cronograma de Revisões da Moto {$make} {$model} {$year}",
                    "Revisões da Motocicleta {$make} {$model} {$year} - Guia Completo",
                    "Manutenção da Moto {$make} {$model} {$year} - Cronograma",
                    "Cronograma de Revisões para Moto {$make} {$model} {$year}",
                    "Guia de Manutenção da {$make} {$model} {$year}"
                ]);

            case 'electric':
                return array_merge($baseVariations, [
                    "Cronograma de Revisões do Elétrico {$make} {$model} {$year}",
                    "Manutenção Veículo Elétrico {$make} {$model} {$year}",
                    "Revisões para Carro Elétrico {$make} {$model} {$year}",
                    "Cronograma de Manutenção Elétrica {$make} {$model} {$year}",
                    "Guia de Revisões EV {$make} {$model} {$year}"
                ]);

            case 'hybrid':
                return array_merge($baseVariations, [
                    "Cronograma de Revisões do Híbrido {$make} {$model} {$year}",
                    "Manutenção Veículo Híbrido {$make} {$model} {$year}",
                    "Revisões para Carro Híbrido {$make} {$model} {$year}",
                    "Cronograma Híbrido {$make} {$model} {$year}",
                    "Guia de Manutenção Híbrida {$make} {$model} {$year}"
                ]);

            default:
                return $baseVariations;
        }
    }

    private function getMetaDescriptionVariations(string $make, string $model, string $year, string $vehicleType): array
    {
        $baseVariations = [
            "Cronograma completo de revisões do {$make} {$model} {$year}. Intervalos, custos, procedimentos e dicas para manter seu veículo sempre em dia.",
            "Guia detalhado de revisões para {$make} {$model} {$year}. Descubra os intervalos corretos, custos estimados e procedimentos de cada revisão.",
            "Manual de manutenção do {$make} {$model} {$year} com cronograma oficial de revisões, custos e dicas preventivas para seu veículo.",
            "Revisões programadas do {$make} {$model} {$year}: cronograma oficial, intervalos recomendados e procedimentos detalhados.",
            "Tudo sobre manutenção do {$make} {$model} {$year}: cronograma de revisões, custos estimados e cuidados preventivos.",
            "Cronograma oficial de revisões {$make} {$model} {$year} com intervalos, procedimentos e estimativas de custo para cada manutenção.",
            "Manutenção preventiva do {$make} {$model} {$year}: cronograma completo de revisões com dicas e procedimentos detalhados.",
            "Descubra o cronograma ideal de revisões para {$make} {$model} {$year}. Intervalos, custos e procedimentos para manter seu veículo perfeito."
        ];

        // Variações específicas por tipo de veículo
        switch ($vehicleType) {
            case 'motorcycle':
                return array_merge($baseVariations, [
                    "Cronograma de revisões da moto {$make} {$model} {$year}. Intervalos, procedimentos e dicas para manter sua motocicleta segura.",
                    "Guia completo de manutenção da motocicleta {$make} {$model} {$year} com cronograma de revisões e cuidados específicos.",
                    "Revisões da moto {$make} {$model} {$year}: cronograma detalhado, custos e procedimentos para pilotagem segura.",
                    "Manutenção da {$make} {$model} {$year}: cronograma de revisões, ajustes e verificações essenciais para sua moto."
                ]);

            case 'electric':
                return array_merge($baseVariations, [
                    "Cronograma de revisões do carro elétrico {$make} {$model} {$year}. Manutenção especializada para veículos elétricos.",
                    "Guia de manutenção do veículo elétrico {$make} {$model} {$year} com cronograma específico e cuidados com a bateria.",
                    "Revisões do {$make} {$model} {$year} elétrico: cronograma, verificações da bateria e sistemas elétricos.",
                    "Manutenção especializada para o {$make} {$model} {$year} elétrico com cronograma adaptado e dicas de conservação."
                ]);

            case 'hybrid':
                return array_merge($baseVariations, [
                    "Cronograma de revisões do híbrido {$make} {$model} {$year}. Manutenção especializada para sistemas dual.",
                    "Guia de manutenção do veículo híbrido {$make} {$model} {$year} com cronograma específico para tecnologia híbrida.",
                    "Revisões do {$make} {$model} {$year} híbrido: cronograma, verificações do sistema elétrico e motor a combustão.",
                    "Manutenção do {$make} {$model} {$year} híbrido com cronograma especializado para sistemas integrados."
                ]);

            default:
                return $baseVariations;
        }
    }

    private function generateMetadata(array $vehicleInfo, array $content): array
    {
        return [
            'content_structure' => [
                'sections_count' => count($content),
                'has_introduction' => isset($content['introduction']),
                'has_conclusion' => isset($content['conclusion']),
                'has_faqs' => isset($content['faqs']) && !empty($content['faqs']),
                'faq_count' => count($content['faqs'] ?? []),
                'revision_count' => count($content['detailed_schedule'] ?? [])
            ],
            'vehicle_characteristics' => [
                'type' => $vehicleInfo['vehicle_type'] ?? 'car',
                'segment' => $vehicleInfo['segment'] ?? 'unknown',
                'fuel_type' => $vehicleInfo['extracted_fuel_type'] ?? 'unknown',
                'detection_confidence' => $vehicleInfo['detection_confidence'] ?? 'medium',
                'is_premium' => ($vehicleInfo['segment'] ?? '') === 'premium'
            ],
            'generation_info' => [
                'template_used' => $this->template,
                'generation_timestamp' => now()->toISOString(),
                'source_system' => $this->source,
                'content_version' => '2.0'
            ],
            'seo_indicators' => [
                'target_keywords_count' => count($this->seoData['secondary_keywords'] ?? []),
                'h2_count' => count($this->seoData['h2_tags'] ?? []),
                'has_schema' => isset($this->seoData['schema_type']),
                'is_indexable' => ($this->seoData['meta_robots'] ?? '') !== 'noindex'
            ]
        ];
    }

    private function calculateQualityMetrics(array $content): array
    {
        $metrics = [
            'content_completeness' => 0,
            'content_depth' => 0,
            'structural_quality' => 0,
            'overall_score' => 0,
            'issues' => []
        ];

        // Verificar completude do conteúdo
        $completenessScore = $this->calculateCompletenessScore($content);
        $metrics['content_completeness'] = $completenessScore['score'];
        $metrics['issues'] = array_merge($metrics['issues'], $completenessScore['issues']);

        // Verificar profundidade do conteúdo
        $depthScore = $this->calculateDepthScore($content);
        $metrics['content_depth'] = $depthScore['score'];
        $metrics['issues'] = array_merge($metrics['issues'], $depthScore['issues']);

        // Verificar qualidade estrutural
        $structuralScore = $this->calculateStructuralScore($content);
        $metrics['structural_quality'] = $structuralScore['score'];
        $metrics['issues'] = array_merge($metrics['issues'], $structuralScore['issues']);

        // Calcular score geral
        $metrics['overall_score'] = round(
            ($metrics['content_completeness'] + $metrics['content_depth'] + $metrics['structural_quality']) / 3
        );

        return $metrics;
    }

    private function calculateCompletenessScore(array $content): array
    {
        $score = 0;
        $issues = [];
        $requiredSections = ['introduction', 'detailed_schedule', 'faqs', 'conclusion'];

        foreach ($requiredSections as $section) {
            if (isset($content[$section]) && !empty($content[$section])) {
                $score += 25;
            } else {
                $issues[] = "Missing required section: {$section}";
            }
        }

        return ['score' => $score, 'issues' => $issues];
    }

    private function calculateDepthScore(array $content): array
    {
        $score = 0;
        $issues = [];

        // Verificar tamanho da introdução
        $introLength = strlen($content['introduction'] ?? '');
        if ($introLength >= self::QUALITY_THRESHOLDS['min_introduction_length']) {
            $score += 20;
        } else {
            $issues[] = "Introduction too short ({$introLength} chars, minimum " . self::QUALITY_THRESHOLDS['min_introduction_length'] . ")";
        }

        // Verificar tamanho da conclusão
        $conclusionLength = strlen($content['conclusion'] ?? '');
        if ($conclusionLength >= self::QUALITY_THRESHOLDS['min_conclusion_length']) {
            $score += 20;
        } else {
            $issues[] = "Conclusion too short ({$conclusionLength} chars, minimum " . self::QUALITY_THRESHOLDS['min_conclusion_length'] . ")";
        }

        // Verificar quantidade de FAQs
        $faqCount = count($content['faqs'] ?? []);
        if (
            $faqCount >= self::QUALITY_THRESHOLDS['min_faq_count'] &&
            $faqCount <= self::QUALITY_THRESHOLDS['max_faq_count']
        ) {
            $score += 30;
        } else {
            $issues[] = "FAQ count outside optimal range ({$faqCount}, optimal: " .
                self::QUALITY_THRESHOLDS['min_faq_count'] . "-" .
                self::QUALITY_THRESHOLDS['max_faq_count'] . ")";
        }

        // Verificar quantidade de revisões
        $revisionCount = count($content['detailed_schedule'] ?? []);
        if ($revisionCount >= self::QUALITY_THRESHOLDS['min_revision_count']) {
            $score += 30;
        } else {
            $issues[] = "Insufficient revision details ({$revisionCount}, minimum " .
                self::QUALITY_THRESHOLDS['min_revision_count'] . ")";
        }

        return ['score' => $score, 'issues' => $issues];
    }

    private function calculateStructuralScore(array $content): array
    {
        $score = 0;
        $issues = [];

        // Verificar presença de tabela de visão geral
        if (isset($content['overview_table']) && !empty($content['overview_table'])) {
            $score += 25;
        } else {
            $issues[] = "Missing overview table";
        }

        // Verificar presença de especificações técnicas
        if (isset($content['technical_specs']) && !empty($content['technical_specs'])) {
            $score += 25;
        } else {
            $issues[] = "Missing technical specifications";
        }

        // Verificar presença de peças críticas
        $criticalPartsCount = count($content['critical_parts'] ?? []);
        if ($criticalPartsCount >= self::QUALITY_THRESHOLDS['min_critical_parts']) {
            $score += 25;
        } else {
            $issues[] = "Insufficient critical parts information ({$criticalPartsCount}, minimum " .
                self::QUALITY_THRESHOLDS['min_critical_parts'] . ")";
        }

        // Verificar presença de informações de garantia
        if (isset($content['warranty_info']) && !empty($content['warranty_info'])) {
            $score += 25;
        } else {
            $issues[] = "Missing warranty information";
        }

        return ['score' => $score, 'issues' => $issues];
    }

    private function getVariedH2Tags(string $vehicleType): array
    {
        $variations = [
            'car' => [
                ['Visão Geral das Revisões Programadas', 'Cronograma de Revisões Programadas', 'Resumo das Revisões Necessárias'],
                ['Detalhamento das Revisões', 'Procedimentos por Quilometragem', 'Revisões Detalhadas por Intervalo'],
                ['Manutenção Preventiva Entre Revisões', 'Cuidados Preventivos Diários', 'Manutenção Preventiva Recomendada'],
                ['Peças que Exigem Atenção Especial', 'Componentes Críticos', 'Itens de Manutenção Essencial'],
                ['Garantia e Recomendações Adicionais', 'Informações de Garantia', 'Dicas Importantes de Manutenção']
            ],
            'motorcycle' => [
                ['Cronograma de Revisões da Motocicleta', 'Revisões Programadas para Motos', 'Intervalos de Manutenção da Moto'],
                ['Intervalos e Procedimentos por Quilometragem', 'Procedimentos de Revisão Detalhados', 'Manutenção por Fase de Uso'],
                ['Manutenção Preventiva e Cuidados Diários', 'Cuidados Entre as Revisões', 'Verificações Rotineiras Essenciais'],
                ['Componentes Críticos para Segurança', 'Peças Essenciais da Motocicleta', 'Sistemas Vitais de Segurança'],
                ['Garantia e Dicas de Pilotagem', 'Informações de Garantia e Cuidados', 'Recomendações para Motociclistas']
            ],
            'electric' => [
                ['Cronograma Específico para Veículos Elétricos', 'Revisões de Carros Elétricos', 'Manutenção Especializada EV'],
                ['Verificações da Bateria e Sistemas Elétricos', 'Cuidados com a Bateria Principal', 'Manutenção dos Sistemas Elétricos'],
                ['Manutenção Preventiva Especializada', 'Cuidados Específicos de Veículos Elétricos', 'Verificações Preventivas EV'],
                ['Componentes Críticos dos Sistemas Elétricos', 'Peças Essenciais de Veículos Elétricos', 'Sistemas de Alta Tensão'],
                ['Garantia da Bateria e Recomendações', 'Informações de Garantia EV', 'Dicas para Proprietários de Elétricos']
            ],
            'hybrid' => [
                ['Cronograma para Veículos Híbridos', 'Revisões de Carros Híbridos', 'Manutenção de Sistemas Dual'],
                ['Manutenção dos Sistemas Integrados', 'Cuidados com Motor Elétrico e Combustão', 'Procedimentos para Híbridos'],
                ['Cuidados com Motor Elétrico e Combustão', 'Manutenção Preventiva Híbrida', 'Verificações dos Sistemas Integrados'],
                ['Componentes Críticos da Tecnologia Híbrida', 'Peças Essenciais de Híbridos', 'Sistemas de Propulsão Dual'],
                ['Garantia e Otimização do Sistema', 'Informações de Garantia Híbrida', 'Dicas para Máxima Eficiência']
            ]
        ];

        $vehicleVariations = $variations[$vehicleType] ?? $variations['car'];
        $selectedH2s = [];

        foreach ($vehicleVariations as $options) {
            $selectedH2s[] = $options[array_rand($options)];
        }

        return $selectedH2s;
    }

    private function getContextualSecondaryKeywords(string $make, string $model, string $vehicleType, array $vehicleInfo): array
    {
        $baseMake = Str::lower($make);
        $baseModel = Str::lower($model);

        $baseKeywords = [
            "revisão {$baseMake} {$baseModel}",
            "manutenção {$baseMake} {$baseModel}",
            "cronograma manutenção {$baseMake}",
            "intervalos revisão {$baseModel}"
        ];

        // Keywords específicas por tipo de veículo
        $typeKeywords = [
            'motorcycle' => [
                "revisão moto {$baseMake}",
                "manutenção motocicleta {$baseModel}",
                "cronograma moto {$baseMake} {$baseModel}",
                "revisões motocicleta"
            ],
            'electric' => [
                "revisão carro elétrico {$baseMake}",
                "manutenção veículo elétrico {$baseModel}",
                "cronograma elétrico {$baseMake}",
                "cuidados bateria {$baseModel}"
            ],
            'hybrid' => [
                "revisão híbrido {$baseMake}",
                "manutenção veículo híbrido {$baseModel}",
                "cronograma híbrido {$baseMake}",
                "economia combustível {$baseModel}"
            ]
        ];

        $specificKeywords = $typeKeywords[$vehicleType] ?? [];

        // Adicionar keywords baseadas no segmento
        $segment = $vehicleInfo['segment'] ?? '';
        if ($segment === 'premium') {
            $specificKeywords[] = "manutenção premium {$baseMake}";
            $specificKeywords[] = "revisão {$baseModel} premium";
        }

        return array_merge($baseKeywords, $specificKeywords);
    }

    private function determineTargetAudience(array $vehicleInfo): string
    {
        $vehicleType = $vehicleInfo['vehicle_type'] ?? 'car';
        $segment = $vehicleInfo['segment'] ?? 'intermediario';
        $usageProfile = $vehicleInfo['usage_profile'] ?? 'geral';

        // Determinar audiência baseada no perfil do veículo
        $audienceMap = [
            'motorcycle_sport' => 'motociclistas esportivos',
            'motorcycle_scooter' => 'usuários urbanos de scooter',
            'electric' => 'proprietários de veículos elétricos',
            'hybrid' => 'proprietários de veículos híbridos'
        ];

        if (isset($audienceMap[$vehicleType]) || isset($audienceMap[$vehicleInfo['subcategory'] ?? ''])) {
            return $audienceMap[$vehicleType] ?? $audienceMap[$vehicleInfo['subcategory']];
        }

        // Audiência por segmento
        $segmentAudience = [
            'premium' => 'proprietários de veículos premium',
            'popular' => 'proprietários de veículos populares',
            'intermediario' => 'proprietários de veículos'
        ];

        return $segmentAudience[$segment] ?? 'proprietários de veículos';
    }

    private function getVehicleKeyForSeo(array $vehicleInfo): string
    {
        $make = strtolower($vehicleInfo['make'] ?? '');
        $vehicleType = $vehicleInfo['vehicle_type'] ?? 'car';
        $year = $vehicleInfo['year'] ?? date('Y');

        // Agrupar por faixas de ano menores para maior variação de SEO
        $yearGroup = floor($year / 2) * 2; // Grupos de 2 anos

        return "seo_{$vehicleType}_{$make}_{$yearGroup}";
    }

    private function selectUnusedVariation(array $variations, string $vehicleKey, string $type): string
    {
        $usedKey = "{$vehicleKey}_{$type}";
        $usedVariations = self::$usedSeoVariations[$usedKey] ?? [];
        $availableVariations = array_diff($variations, $usedVariations);

        // Se todas já foram usadas, resetar
        if (empty($availableVariations)) {
            self::$usedSeoVariations[$usedKey] = [];
            $availableVariations = $variations;
        }

        $selectedVariation = $availableVariations[array_rand($availableVariations)];

        // Marcar como usada
        if (!isset(self::$usedSeoVariations[$usedKey])) {
            self::$usedSeoVariations[$usedKey] = [];
        }
        self::$usedSeoVariations[$usedKey][] = $selectedVariation;

        return $selectedVariation;
    }

    private function generateSearchTerms(): array
    {
        $make = Str::lower($this->vehicleInfo['make'] ?? '');
        $model = Str::lower($this->vehicleInfo['model'] ?? '');
        $year = $this->vehicleInfo['year'] ?? '';
        $vehicleType = $this->vehicleInfo['vehicle_type'] ?? 'car';

        $searchTerms = [
            $make,
            $model,
            (string)$year,
            $vehicleType,
            'revisão',
            'manutenção',
            'cronograma'
        ];

        // Adicionar termos específicos
        if (isset($this->vehicleInfo['extracted_engine'])) {
            $searchTerms[] = Str::lower($this->vehicleInfo['extracted_engine']);
        }

        if (isset($this->vehicleInfo['extracted_fuel_type'])) {
            $searchTerms[] = Str::lower($this->vehicleInfo['extracted_fuel_type']);
        }

        // Remover termos vazios e duplicados
        return array_values(array_unique(array_filter($searchTerms)));
    }

    private function generateVehicleKey(): string
    {
        $make = $this->vehicleInfo['make'] ?? '';
        $model = $this->vehicleInfo['model'] ?? '';
        $year = $this->vehicleInfo['year'] ?? '';

        return Str::slug("{$make}-{$model}-{$year}");
    }

    private function generateContentHash(): string
    {
        // Gerar hash baseado no conteúdo principal
        $contentString = json_encode([
            'introduction' => $this->content['introduction'] ?? '',
            'conclusion' => $this->content['conclusion'] ?? '',
            'detailed_schedule' => $this->content['detailed_schedule'] ?? []
        ]);

        return hash('sha256', $contentString);
    }

    private function getVehicleTypeInPortuguese(string $type): string
    {
        return match ($type) {
            'motorcycle' => 'motocicleta',
            'electric' => 'veículo elétrico',
            'hybrid' => 'veículo híbrido',
            default => 'carro'
        };
    }

    private function getVehicleIdentifier(): string
    {
        return ($this->vehicleInfo['make'] ?? '') . ' ' .
            ($this->vehicleInfo['model'] ?? '') . ' ' .
            ($this->vehicleInfo['year'] ?? '');
    }

    private function logCreation(): void
    {
        Log::info("GeneratedArticleData created", [
            'slug' => $this->slug,
            'vehicle' => $this->getVehicleIdentifier(),
            'vehicle_type' => $this->vehicleInfo['vehicle_type'] ?? 'unknown',
            'template' => $this->template,
            'quality_score' => $this->qualityMetrics['overall_score'],
            'content_sections' => count($this->content),
            'seo_variations_used' => count(self::$usedSeoVariations)
        ]);
    }

    /**
     * Método público para validar a qualidade do artigo
     */
    public function validateQuality(): array
    {
        $validationResult = [
            'is_valid' => true,
            'issues' => [],
            'warnings' => [],
            'quality_score' => $this->qualityMetrics['overall_score']
        ];

        // Validações críticas
        if ($this->qualityMetrics['overall_score'] < 60) {
            $validationResult['is_valid'] = false;
            $validationResult['issues'][] = 'Overall quality score too low';
        }

        if (empty($this->content['introduction'])) {
            $validationResult['is_valid'] = false;
            $validationResult['issues'][] = 'Missing introduction';
        }

        if (empty($this->content['conclusion'])) {
            $validationResult['is_valid'] = false;
            $validationResult['issues'][] = 'Missing conclusion';
        }

        // Validações de aviso
        if ($this->qualityMetrics['overall_score'] < 80) {
            $validationResult['warnings'][] = 'Quality score could be improved';
        }

        if (count($this->content['faqs'] ?? []) < 3) {
            $validationResult['warnings'][] = 'Low FAQ count';
        }

        return $validationResult;
    }

    /**
     * Método público para atualizar conteúdo
     */
    public function updateContent(array $newContent): void
    {
        $this->content = array_merge($this->content, $newContent);
        $this->qualityMetrics = $this->calculateQualityMetrics($this->content);

        Log::info("Article content updated", [
            'slug' => $this->slug,
            'new_quality_score' => $this->qualityMetrics['overall_score'],
            'updated_sections' => array_keys($newContent)
        ]);
    }

    /**
     * Método estático para limpar cache de variações SEO
     */
    public static function clearSeoVariationCache(): void
    {
        self::$usedSeoVariations = [];
        Log::info("SEO variation cache cleared");
    }

    /**
     * Método público para obter estatísticas de uso
     */
    public static function getSeoUsageStatistics(): array
    {
        return [
            'total_variation_keys' => count(self::$usedSeoVariations),
            'variations_per_key' => array_map('count', self::$usedSeoVariations),
            'most_used_keys' => array_keys(
                array_slice(
                    array_map('count', self::$usedSeoVariations),
                    0,
                    5,
                    true
                )
            )
        ];
    }

    /**
     * Método público para exportar para diferentes formatos
     */
    public function export(string $format = 'array'): array|string
    {
        return match (strtolower($format)) {
            'json' => json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'mongo' => $this->toMongoDocument(),
            'minimal' => [
                'title' => $this->title,
                'slug' => $this->slug,
                'vehicle' => $this->getVehicleIdentifier(),
                'quality_score' => $this->qualityMetrics['overall_score'],
                'status' => $this->status
            ],
            default => $this->toArray()
        };
    }
}
