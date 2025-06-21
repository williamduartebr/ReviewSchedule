<?php

namespace Src\ContentGeneration\ReviewSchedule\Application\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Src\ArticleGenerator\Infrastructure\Eloquent\TempArticle;
use Src\ContentGeneration\ReviewSchedule\Application\DTOs\VehicleData;
use Src\ContentGeneration\ReviewSchedule\Application\DTOs\GeneratedArticleData;
use Src\ContentGeneration\ReviewSchedule\Domain\Services\ArticleContentGeneratorService;
use Src\ContentGeneration\ReviewSchedule\Infrastructure\Repositories\CsvVehicleRepository;
use Src\ContentGeneration\ReviewSchedule\Infrastructure\Repositories\MongoReviewScheduleArticleRepository;

class ReviewScheduleApplicationService
{
    private ArticleContentGeneratorService $contentGenerator;
    private CsvVehicleRepository $vehicleRepository;
    private MongoReviewScheduleArticleRepository $articleRepository;
    private bool $strictValidation = false;

    public function __construct(
        ArticleContentGeneratorService $contentGenerator,
        CsvVehicleRepository $vehicleRepository,
        MongoReviewScheduleArticleRepository $articleRepository
    ) {
        $this->contentGenerator = $contentGenerator;
        $this->vehicleRepository = $vehicleRepository;
        $this->articleRepository = $articleRepository;
    }

    public function setStrictValidation(bool $strict): void
    {
        $this->strictValidation = $strict;
    }

    /**
     * Método original mantido para compatibilidade
     */
    public function generateArticlesFromCsv(
        string $csvFilePath,
        int $batchSize = 50,
        bool $dryRun = false,
        ?callable $progressCallback = null
    ): object {
        return $this->generateArticlesFromCsvWithFilters(
            $csvFilePath,
            $batchSize,
            [],
            $dryRun,
            false,
            $progressCallback
        );
    }

    /**
     * Novo método com suporte a filtros
     */
    public function generateArticlesFromCsvWithFilters(
        string $csvFilePath,
        int $batchSize = 50,
        array $filters = [],
        bool $dryRun = false,
        bool $force = false,
        ?callable $progressCallback = null
    ): object {
        // Limpar estado dos templates antes de iniciar
        $this->clearTemplateStates();

        // Carregar veículos com filtros aplicados
        $vehicles = $this->vehicleRepository->loadVehiclesFromCsvWithFilters($csvFilePath, $filters);
        $totalVehicles = count($vehicles);

        $results = (object)[
            'generated' => 0,
            'failed' => 0,
            'skipped' => 0,
            'errors' => [],
            'filtered_total' => $totalVehicles
        ];

        if ($totalVehicles === 0) {
            $results->errors[] = 'Nenhum veículo válido encontrado no CSV com os filtros aplicados.';
            return $results;
        }

        $batches = array_chunk($vehicles, $batchSize);
        $processed = 0;

        foreach ($batches as $batchIndex => $batch) {
            $articles = [];

            foreach ($batch as $vehicleData) {
                try {
                    // Verificar se os dados básicos estão presentes
                    if (empty($vehicleData) || !is_array($vehicleData)) {
                        $results->skipped++;
                        continue;
                    }

                    $vehicle = new VehicleData($vehicleData);

                    if (!$vehicle->isValid()) {
                        $results->skipped++;
                        Log::debug('Veículo inválido pulado', [
                            'issues' => $vehicle->getValidationIssues(),
                            'data' => $vehicleData
                        ]);
                        continue;
                    }

                    // Verificar se já existe (a menos que force esteja ativo)
                    if (!$force) {
                        $slug = $this->generateSlugForVehicle($vehicle);

                        try {
                            $existingArticle = $this->articleRepository->findBySlug($slug);
                            if ($existingArticle) {
                                $results->skipped++;
                                Log::debug('Artigo já existe, pulando', [
                                    'slug' => $slug,
                                    'vehicle' => $vehicle->getFullName()
                                ]);
                                continue;
                            }
                        } catch (\Exception $e) {
                            Log::warning('Erro ao verificar artigo existente', [
                                'slug' => $slug,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }

                    // Gerar artigo
                    try {
                        $article = $this->contentGenerator->generateArticle($vehicle->toArray());

                        // Validação adicional se strict mode estiver ativo
                        if ($this->strictValidation) {
                            $this->validateArticleContent($article, $vehicle);
                        }

                        // Converter para GeneratedArticleData usando estrutura original
                        $generatedArticle = new GeneratedArticleData(
                            $article->getTitle(),
                            $vehicle->toArray(),
                            $article->getContent(),
                            'draft'
                        );

                        $articles[] = $generatedArticle;
                    } catch (\Exception $e) {
                        $results->failed++;
                        $vehicleName = $vehicle->getFullName() ?? 'veículo desconhecido';
                        $errorMessage = "Erro na geração de conteúdo para {$vehicleName}: " . $e->getMessage();
                        $results->errors[] = $errorMessage;
                        Log::error($errorMessage, [
                            'vehicle_data' => $vehicle->toArray(),
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        continue;
                    }
                } catch (\Exception $e) {
                    $results->failed++;
                    $errorMessage = "Erro no processamento do veículo: " . $e->getMessage();
                    $results->errors[] = $errorMessage;
                    Log::error($errorMessage, [
                        'vehicle_data' => $vehicleData ?? [],
                        'error' => $e->getMessage(),
                        'batch_index' => $batchIndex
                    ]);
                }
            }

            // Salvar articles do batch
            if (!$dryRun && !empty($articles)) {
                try {
                    $saved = $this->saveArticlesBatch($articles, $force);
                    $results->generated += $saved;
                } catch (\Exception $e) {
                    $results->failed += count($articles);
                    $results->errors[] = "Erro ao salvar batch: " . $e->getMessage();
                    Log::error("Erro ao salvar batch de artigos", [
                        'batch_size' => count($articles),
                        'error' => $e->getMessage()
                    ]);
                }
            } else {
                // Em dry run, contar como gerados para relatório
                $results->generated += count($articles);
            }

            $processed += count($batch);

            // Callback de progresso
            if ($progressCallback) {
                $progressCallback($processed, $totalVehicles);
            }
        }

        return $results;
    }

    /**
     * Preview dos veículos que serão processados
     */
    public function previewVehiclesWithFilters(string $csvFilePath, array $filters = []): array
    {
        return $this->vehicleRepository->previewVehiclesWithFilters($csvFilePath, $filters);
    }

    /**
     * Validação rigorosa do conteúdo do artigo
     */
    private function validateArticleContent($article, VehicleData $vehicle): void
    {
        $content = $article->getContent();
        $vehicleType = $this->detectVehicleType($vehicle->toArray());

        // Converter conteúdo para texto para validação
        $contentText = $this->flattenContentToText($content);

        // Termos proibidos para carros elétricos
        if ($vehicleType === 'electric') {
            $forbiddenTerms = [
                'óleo do motor',
                'troca de óleo',
                'filtro de óleo',
                'velas de ignição',
                'sistema de injeção',
                'filtro de combustível',
                'escapamento',
                'combustível'
            ];

            foreach ($forbiddenTerms as $term) {
                if (stripos($contentText, $term) !== false) {
                    throw new \Exception("Conteúdo de veículo elétrico contém termo inválido: {$term}");
                }
            }
        }

        // Termos proibidos para carros convencionais
        if ($vehicleType === 'car') {
            $forbiddenTerms = [
                'bateria de alta tensão',
                'conectores de carregamento',
                'sistema de gerenciamento da bateria',
                'refrigeração da bateria',
                'carregador homologado'
            ];

            foreach ($forbiddenTerms as $term) {
                if (stripos($contentText, $term) !== false) {
                    throw new \Exception("Conteúdo de veículo a combustão contém termo inválido: {$term}");
                }
            }
        }
    }

    private function detectVehicleType(array $vehicleData): string
    {
        $category = strtolower($vehicleData['category'] ?? '');

        if (strpos($category, 'electric') !== false || strpos($category, 'elétrico') !== false) {
            return 'electric';
        }

        if (strpos($category, 'hybrid') !== false || strpos($category, 'híbrido') !== false) {
            return 'hybrid';
        }

        if (strpos($category, 'motorcycle') !== false || strpos($category, 'moto') !== false) {
            return 'motorcycle';
        }

        return 'car';
    }

    private function flattenContentToText(array $content): string
    {
        $text = '';

        foreach ($content as $section) {
            if (is_string($section)) {
                $text .= ' ' . $section;
            } elseif (is_array($section)) {
                $text .= ' ' . $this->flattenContentToText($section);
            }
        }

        return $text;
    }

    private function saveArticlesBatch(array $articles, bool $force = false): int
    {
        $saved = 0;

        foreach ($articles as $articleData) {
            try {
                // Usar o Eloquent diretamente em vez de métodos que não existem
                $dataToSave = [
                    'title' => $articleData->getTitle(),
                    'slug' => $articleData->getSlug(),
                    'vehicle_info' => $articleData->getVehicleInfo(),
                    'extracted_entities' => $articleData->getExtractedEntities(),
                    'seo_data' => $articleData->getSeoData(),
                    'content' => $articleData->getContent(),
                    'template' => $articleData->getTemplate(),
                    'status' => $articleData->getStatus(),
                    'source' => $articleData->getSource(),
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // Usar a classe Eloquent diretamente
                $eloquentClass = '\Src\ContentGeneration\ReviewSchedule\Infrastructure\Eloquent\ReviewScheduleArticle';

                if ($force) {
                    // Em modo force, atualizar se já existir
                    if ($this->articleRepository->exists($articleData->getSlug())) {
                        $eloquentClass::where('slug', $articleData->getSlug())->update($dataToSave);
                    } else {
                        $eloquentClass::create($dataToSave);
                    }
                } else {
                    // Verificar se já existe antes de criar
                    if (!$this->articleRepository->exists($articleData->getSlug())) {
                        $eloquentClass::create($dataToSave);
                    } else {
                        Log::warning("Artigo já existe, pulando", [
                            'slug' => $articleData->getSlug()
                        ]);
                        continue;
                    }
                }
                $saved++;
            } catch (\Exception $e) {
                Log::error("Erro ao salvar artigo individual", [
                    'slug' => $articleData->getSlug() ?? 'unknown',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        return $saved;
    }

    private function generateSlugForVehicle(VehicleData $vehicle): string
    {
        $make = Str::slug($vehicle->make);
        $model = Str::slug($vehicle->model);
        $year = $vehicle->year;

        return "cronograma-revisoes-{$make}-{$model}-{$year}";
    }

    public function getArticleStats(): array
    {
        try {
            return [
                'total' => $this->articleRepository->count(),
                'draft' => $this->articleRepository->countByStatus('draft'),
                'published' => $this->articleRepository->countByStatus('published'),
            ];
        } catch (\Exception $e) {
            Log::error('Erro ao obter estatísticas: ' . $e->getMessage());
            return [
                'total' => 0,
                'draft' => 0,
                'published' => 0,
                'error' => $e->getMessage()
            ];
        }
    }

    public function getTempArticleStats(): array
    {
        $total = TempArticle::count();
        $reviewSchedule = TempArticle::where('source', 'review_schedule')->count();

        return [
            'total' => $total,
            'review_schedule' => $reviewSchedule
        ];
    }

    /**
     * Método para acessar o repositório de artigos (usado pelos comandos)
     */
    public function getArticleRepository(): MongoReviewScheduleArticleRepository
    {
        return $this->articleRepository;
    }

    /**
     * Método para obter estatísticas do CSV
     */
    public function getCsvStats(string $csvFilePath): array
    {
        return $this->vehicleRepository->getVehicleStatsByType($csvFilePath);
    }

    /**
     * Método para limpar estado dos templates
     */
    private function clearTemplateStates(): void
    {
        // Limpar estado do template elétrico
        if (class_exists('\Src\ContentGeneration\ReviewSchedule\Infrastructure\ContentTemplates\ElectricVehicleMaintenanceTemplate')) {
            \Src\ContentGeneration\ReviewSchedule\Infrastructure\ContentTemplates\ElectricVehicleMaintenanceTemplate::clearState();
        }

        // Tentar limpar outros templates se tiverem método clearState
        $templateClasses = [
            '\Src\ContentGeneration\ReviewSchedule\Infrastructure\ContentTemplates\CarMaintenanceTemplate',
            '\Src\ContentGeneration\ReviewSchedule\Infrastructure\ContentTemplates\MotorcycleMaintenanceTemplate',
            '\Src\ContentGeneration\ReviewSchedule\Infrastructure\ContentTemplates\HybridVehicleMaintenanceTemplate'
        ];

        foreach ($templateClasses as $templateClass) {
            if (class_exists($templateClass)) {
                try {
                    $reflection = new \ReflectionClass($templateClass);
                    if ($reflection->hasMethod('clearState')) {
                        $templateClass::clearState();
                    }
                } catch (\Exception $e) {
                    // Ignore if method doesn't exist
                }
            }
        }
    }

    /**
     * Métodos para importação de veículos (compatibilidade com comandos existentes)
     */
    public function importVehiclesBatch(string $csvFilePath, int $batchSize, int $offset): array
    {
        try {
            $vehicles = $this->vehicleRepository->loadVehiclesBatch($csvFilePath, $batchSize, $offset);
            return [
                'vehicles' => $vehicles,
                'total' => count($vehicles)
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'vehicles' => []
            ];
        }
    }

    /**
     * Gerar um único artigo (para debug)
     */
    public function generateSingleArticle(array $vehicleData): ?GeneratedArticleData
    {
        try {
            $vehicle = new VehicleData($vehicleData);

            if (!$vehicle->isValid()) {
                throw new \InvalidArgumentException('Dados do veículo são inválidos: ' . implode(', ', $vehicle->getValidationIssues()));
            }

            $article = $this->contentGenerator->generateArticle($vehicle->toArray());

            return new GeneratedArticleData(
                $article->getTitle(),
                $vehicle->toArray(),
                $article->getContent(),
                'draft'
            );
        } catch (\Exception $e) {
            Log::error('Erro ao gerar artigo único', [
                'vehicle_data' => $vehicleData,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function publishToTempArticles(
        string $status = 'draft',
        int $limit = 100,
        bool $dryRun = false,
        ?callable $progressCallback = null
    ): object {
        $results = (object)[
            'published' => 0,
            'failed' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        try {
            $articles = $this->articleRepository->findByStatus($status, $limit);
            $totalArticles = count($articles);
            $processed = 0;

            foreach ($articles as $article) {
                try {
                    // Verificar se já existe no TempArticle
                    if ($this->tempArticleExists($article['slug'])) {
                        $results->skipped++;
                        $results->errors[] = "Artigo já existe no TempArticle: {$article['slug']}";
                        continue;
                    }

                    if (!$dryRun) {
                        $tempArticleData = $this->convertToTempArticleFormat($article);

                        $tempArticle = new TempArticle();
                        $tempArticle->fill($tempArticleData);

                        if ($tempArticle->save()) {
                            $results->published++;
                        } else {
                            $results->failed++;
                            $results->errors[] = "Falha ao salvar no TempArticle: {$article['slug']}";
                        }
                    } else {
                        $results->published++; // Simular para dry run
                    }
                } catch (\Exception $e) {
                    $results->failed++;
                    $results->errors[] = "Erro ao publicar {$article['slug']}: " . $e->getMessage();
                }

                $processed++;
                if ($progressCallback) {
                    $progressCallback($processed, $totalArticles);
                }
            }
        } catch (\Exception $e) {
            $results->errors[] = "Erro ao buscar artigos: " . $e->getMessage();
        }

        return $results;
    }

    public function publishArticlesByStatus(string $status = 'draft'): object
    {
        $results = (object)[
            'published' => 0,
            'failed' => 0,
            'errors' => []
        ];

        try {
            $articles = $this->articleRepository->findByStatus($status, 100);

            foreach ($articles as $article) {
                try {
                    if ($this->articleRepository->updateStatus($article['slug'], 'published')) {
                        $results->published++;
                    } else {
                        $results->failed++;
                        $results->errors[] = "Falha ao publicar artigo: {$article['slug']}";
                    }
                } catch (\Exception $e) {
                    $results->failed++;
                    $results->errors[] = "Erro ao publicar {$article['slug']}: " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            $results->errors[] = "Erro ao buscar artigos: " . $e->getMessage();
        }

        return $results;
    }

    private function tempArticleExists(string $slug): bool
    {
        try {
            return TempArticle::where('slug', $slug)->exists();
        } catch (\Exception $e) {
            Log::error('Erro ao verificar existência no TempArticle: ' . $e->getMessage());
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
            'url_slug' => 'revisao-'. $article['slug'],
            'h1' => $seoData['h1'] ?? $article['title'],
            'h2_tags' => $seoData['h2_tags'] ?? $this->extractH2Tags($content),
            'primary_keyword' => $seoData['primary_keyword'] ?? $this->generatePrimaryKeyword($vehicleInfo),
            'secondary_keywords' => $seoData['secondary_keywords'] ?? $this->generateSecondaryKeywords($vehicleInfo),
            'meta_robots' => 'index,follow',
            'canonical_url' => config('app.url') . '/info/revisao-'. $article['slug'],
            'schema_type' => 'Article',
            'focus_keywords' => $this->generateFocusKeywords($vehicleInfo)
        ];

        return [
            'original_post_id' => $article['blog_id'] ?? null,
            'title' => $article['title'],
            'slug' => $article['slug'],
            'new_slug' => 'revisao-'. $article['slug'],
            'content' => $content,
            'extracted_entities' => $extractedEntities,
            'seo_data' => $seoFormatted,
            'source' => 'review_schedule',
            'category_id' => 21,
            'category_name' => 'Revisões Programadas',
            'category_slug' => 'revisoes-programadas',
            'published_at' => $article['blog_published_time'],
            'modified_at' => $article['blog_modified_time'],
            'blog_status' => $article['blog_status'] ?? null,
            'domain' => $article['domain'] ?? 'review_schedule',
            'status' => 'draft',
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
        // Extrair seções do conteúdo como H2 tags
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

        // Verificar completude das seções
        $requiredSections = ['introducao', 'cronograma_detalhado', 'perguntas_frequentes', 'consideracoes_finais'];
        foreach ($requiredSections as $section) {
            if (isset($content[$section]) && !empty($content[$section])) {
                $score += 25;
            }
        }

        return min(100, $score);
    }
}
