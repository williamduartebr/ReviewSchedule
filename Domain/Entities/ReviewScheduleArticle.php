<?php

namespace Src\ContentGeneration\ReviewSchedule\Domain\Entities;

use Illuminate\Support\Str;

class ReviewScheduleArticle
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

    // Sistema de controle de variações SEO
    private static array $usedSeoVariations = [];

    public function __construct(
        string $title,
        array $vehicleInfo,
        array $content,
        string $status = 'draft'
    ) {
        $this->title = $title;
        $this->vehicleInfo = $vehicleInfo;
        $this->content = $content;
        $this->template = 'review_schedule';
        $this->status = $status;
        $this->source = 'manual_generator';
        $this->domain = 'review_schedule';

        // Gerar slug usando o padrão correto (sem "do/da")
        $this->slug = $this->generateCorrectSlug($vehicleInfo);
        $this->extractedEntities = $this->extractEntitiesFromVehicleInfo($vehicleInfo);
        $this->seoData = $this->generateSeoData($vehicleInfo);
    }

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

    public function publish(): void
    {
        $this->status = 'published';
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
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    private function generateCorrectSlug(array $vehicleInfo): string
    {
        $make = $vehicleInfo['make'] ?? '';
        $model = $vehicleInfo['model'] ?? '';
        $year = $vehicleInfo['year'] ?? '';

        // Padrão exato do generator.php: cronograma-revisoes-make-model-year (sem "do/da")
        return Str::slug("{$make}-{$model}-{$year}");
    }

    private function extractEntitiesFromVehicleInfo(array $vehicleInfo): array
    {
        return [
            'marca' => $vehicleInfo['make'] ?? '',
            'modelo' => $vehicleInfo['model'] ?? '',
            'ano' => (string)($vehicleInfo['year'] ?? ''),
            'motorizacao' => $vehicleInfo['engine'] ?? '',
            'versao' => $vehicleInfo['version'] ?? '',
            'tipo_veiculo' => $this->getVehicleTypeInPortuguese($vehicleInfo['vehicle_type'] ?? 'carro'),
            'categoria' => $vehicleInfo['subcategory'] ?? '',
            'combustivel' => $vehicleInfo['fuel_type'] ?? 'flex'
        ];
    }

    private function generateSeoData(array $vehicleInfo): array
    {
        $make = $vehicleInfo['make'] ?? '';
        $model = $vehicleInfo['model'] ?? '';
        $year = $vehicleInfo['year'] ?? '';
        $vehicleType = $vehicleInfo['vehicle_type'] ?? 'car';

        // Gerar variações de SEO baseadas no veículo
        $seoVariation = $this->getSeoVariation($vehicleInfo);

        return [
            'page_title' => $seoVariation['page_title'],
            'meta_description' => $seoVariation['meta_description'],
            'url_slug' => "revisao-" . $this->slug,
            'h1' => "Cronograma de Revisões do {$make} {$model} {$year}",
            'h2_tags' => $this->getVariedH2Tags($vehicleType),
            'primary_keyword' => "cronograma revisões " . Str::lower("{$make} {$model} {$year}"),
            'secondary_keywords' => $this->getVariedSecondaryKeywords($make, $model, $vehicleType)
        ];
    }

    private function getSeoVariation(array $vehicleInfo): array
    {
        $make = $vehicleInfo['make'] ?? '';
        $model = $vehicleInfo['model'] ?? '';
        $year = $vehicleInfo['year'] ?? '';
        $vehicleType = $vehicleInfo['vehicle_type'] ?? 'car';
        
        // Criar chave para agrupamento
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
            "Guia de Revisões {$make} {$model} {$year} - Cronograma Completo"
        ];

        // Variações específicas por tipo de veículo
        switch ($vehicleType) {
            case 'motorcycle':
                return array_merge($baseVariations, [
                    "Cronograma de Revisões da Moto {$make} {$model} {$year}",
                    "Revisões da Motocicleta {$make} {$model} {$year} - Guia Completo",
                    "Manutenção da Moto {$make} {$model} {$year} - Cronograma",
                    "Cronograma de Revisões para Moto {$make} {$model} {$year}"
                ]);
            
            case 'electric':
                return array_merge($baseVariations, [
                    "Cronograma de Revisões do Elétrico {$make} {$model} {$year}",
                    "Manutenção Veículo Elétrico {$make} {$model} {$year}",
                    "Revisões para Carro Elétrico {$make} {$model} {$year}",
                    "Cronograma de Manutenção Elétrica {$make} {$model} {$year}"
                ]);
            
            case 'hybrid':
                return array_merge($baseVariations, [
                    "Cronograma de Revisões do Híbrido {$make} {$model} {$year}",
                    "Manutenção Veículo Híbrido {$make} {$model} {$year}",
                    "Revisões para Carro Híbrido {$make} {$model} {$year}",
                    "Cronograma Híbrido {$make} {$model} {$year}"
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
            "Manutenção preventiva do {$make} {$model} {$year}: cronograma completo de revisões com dicas e procedimentos detalhados."
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

    private function getVariedH2Tags(string $vehicleType): array
    {
        $baseH2Tags = [
            'Visão Geral das Revisões Programadas',
            'Detalhamento das Revisões',
            'Manutenção Preventiva Entre Revisões',
            'Peças que Exigem Atenção Especial',
            'Garantia e Recomendações Adicionais'
        ];

        // Variações por tipo de veículo
        switch ($vehicleType) {
            case 'motorcycle':
                return [
                    'Cronograma de Revisões da Motocicleta',
                    'Intervalos e Procedimentos por Quilometragem',
                    'Manutenção Preventiva e Cuidados Diários',
                    'Componentes Críticos para Segurança',
                    'Garantia e Dicas de Pilotagem'
                ];
            
            case 'electric':
                return [
                    'Cronograma Específico para Veículos Elétricos',
                    'Verificações da Bateria e Sistemas Elétricos',
                    'Manutenção Preventiva Especializada',
                    'Componentes Críticos dos Sistemas Elétricos',
                    'Garantia da Bateria e Recomendações'
                ];
            
            case 'hybrid':
                return [
                    'Cronograma para Veículos Híbridos',
                    'Manutenção dos Sistemas Integrados',
                    'Cuidados com Motor Elétrico e Combustão',
                    'Componentes Críticos da Tecnologia Híbrida',
                    'Garantia e Otimização do Sistema'
                ];
            
            default:
                return $baseH2Tags;
        }
    }

    private function getVariedSecondaryKeywords(string $make, string $model, string $vehicleType): array
    {
        $baseMake = Str::lower($make);
        $baseModel = Str::lower($model);
        
        $baseKeywords = [
            "revisão {$baseMake} {$baseModel}",
            "manutenção {$baseMake} {$baseModel}",
            "cronograma manutenção {$baseMake}"
        ];

        // Keywords específicas por tipo de veículo
        switch ($vehicleType) {
            case 'motorcycle':
                return array_merge($baseKeywords, [
                    "revisão moto {$baseMake}",
                    "manutenção motocicleta {$baseModel}",
                    "cronograma moto {$baseMake} {$baseModel}"
                ]);
            
            case 'electric':
                return array_merge($baseKeywords, [
                    "revisão carro elétrico {$baseMake}",
                    "manutenção veículo elétrico {$baseModel}",
                    "cronograma elétrico {$baseMake}"
                ]);
            
            case 'hybrid':
                return array_merge($baseKeywords, [
                    "revisão híbrido {$baseMake}",
                    "manutenção veículo híbrido {$baseModel}",
                    "cronograma híbrido {$baseMake}"
                ]);
            
            default:
                return array_merge($baseKeywords, [
                    "intervalos revisão {$baseModel}"
                ]);
        }
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

    private function getVehicleTypeInPortuguese(string $type): string
    {
        return match ($type) {
            'motorcycle' => 'motocicleta',
            'electric' => 'veículo elétrico',
            'hybrid' => 'veículo híbrido',
            default => 'carro'
        };
    }
}