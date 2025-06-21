<?php

namespace Src\ContentGeneration\ReviewSchedule\Console;

use Illuminate\Console\Command;
use Src\ContentGeneration\ReviewSchedule\Infrastructure\Eloquent\ReviewScheduleArticle;

class AnalyzeArticleQuality extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'review-schedule:analyze-simple 
                            {--limit=1000 : Limit number of articles to analyze}
                            {--output=broken_articles.log : Output file for results}';

    /**
     * The console command description.
     */
    protected $description = 'Simple analysis of broken ReviewScheduleArticle JSONs';

    private array $brokenArticles = [];
    private array $statistics = [
        'total_analyzed' => 0,
        'total_broken' => 0,
        'issues_by_type' => [],
        'broken_by_vehicle_type' => []
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int)$this->option('limit');
        $outputFile = $this->option('output');

        $this->info('🔍 Iniciando análise simplificada...');

        // Buscar artigos usando Eloquent diretamente
        $articles = ReviewScheduleArticle::limit($limit)->get();
        
        $this->info("📊 Analisando {$articles->count()} artigos...");
        
        $progressBar = $this->output->createProgressBar($articles->count());
        $progressBar->start();

        foreach ($articles as $article) {
            $this->analyzeArticle($article);
            $this->statistics['total_analyzed']++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->generateReport($outputFile);
        $this->displayStatistics();
    }

    private function analyzeArticle($article): void
    {
        $issues = [];
        
        // Content é sempre um array baseado no debug
        $content = $article->content;
        
        if (!is_array($content) || empty($content)) {
            $issues[] = [
                'type' => 'INVALID_CONTENT',
                'severity' => 'CRITICAL',
                'description' => 'Conteúdo inválido ou vazio'
            ];
            $this->recordBrokenArticle($article, $issues);
            return;
        }

        // Verificações baseadas nos problemas identificados
        $issues = array_merge($issues, $this->checkDetailedSchedule($content));
        $issues = array_merge($issues, $this->checkPreventiveMaintenance($content));
        $issues = array_merge($issues, $this->checkCriticalParts($content));
        $issues = array_merge($issues, $this->checkRequiredSections($content));

        if (!empty($issues)) {
            $this->recordBrokenArticle($article, $issues);
        }
    }

    private function checkDetailedSchedule(array $content): array
    {
        $issues = [];
        
        // Verificar se existe cronograma_detalhado
        if (!isset($content['cronograma_detalhado']) || empty($content['cronograma_detalhado'])) {
            $issues[] = [
                'type' => 'MISSING_DETAILED_SCHEDULE',
                'severity' => 'HIGH',
                'description' => 'Cronograma detalhado ausente ou vazio'
            ];
            return $issues;
        }

        $schedule = $content['cronograma_detalhado'];
        
        // Verificar quantidade de revisões
        if (count($schedule) < 3) {
            $issues[] = [
                'type' => 'INSUFFICIENT_REVISIONS',
                'severity' => 'HIGH',
                'description' => 'Menos de 3 revisões detalhadas (' . count($schedule) . ')'
            ];
        }

        // Verificar estrutura das revisões
        foreach ($schedule as $index => $revision) {
            if (!isset($revision['servicos_principais']) || 
                !is_array($revision['servicos_principais']) || 
                count($revision['servicos_principais']) < 2) {
                
                $issues[] = [
                    'type' => 'INCOMPLETE_REVISION_SERVICES',
                    'severity' => 'MEDIUM',
                    'description' => "Revisão {$index}: serviços principais insuficientes"
                ];
            }

            if (!isset($revision['verificacoes_complementares']) || 
                !is_array($revision['verificacoes_complementares']) || 
                empty($revision['verificacoes_complementares'])) {
                
                $issues[] = [
                    'type' => 'MISSING_COMPLEMENTARY_CHECKS',
                    'severity' => 'MEDIUM',
                    'description' => "Revisão {$index}: verificações complementares ausentes"
                ];
            }
        }

        return $issues;
    }

    private function checkPreventiveMaintenance(array $content): array
    {
        $issues = [];
        
        if (!isset($content['manutencao_preventiva'])) {
            $issues[] = [
                'type' => 'MISSING_PREVENTIVE_MAINTENANCE',
                'severity' => 'HIGH',
                'description' => 'Manutenção preventiva ausente'
            ];
            return $issues;
        }

        $maintenance = $content['manutencao_preventiva'];
        
        // Verificar verificações mensais
        if (!isset($maintenance['verificacoes_mensais']) || 
            !is_array($maintenance['verificacoes_mensais']) || 
            count($maintenance['verificacoes_mensais']) < 3) {
            
            $issues[] = [
                'type' => 'INSUFFICIENT_MONTHLY_CHECKS',
                'severity' => 'MEDIUM',
                'description' => 'Verificações mensais insuficientes'
            ];
        }

        // Verificar verificações trimestrais
        if (!isset($maintenance['verificacoes_trimestrais']) || 
            !is_array($maintenance['verificacoes_trimestrais']) || 
            count($maintenance['verificacoes_trimestrais']) < 3) {
            
            $issues[] = [
                'type' => 'INSUFFICIENT_QUARTERLY_CHECKS',
                'severity' => 'MEDIUM',
                'description' => 'Verificações trimestrais insuficientes'
            ];
        }

        // Verificar verificações anuais (problema específico identificado)
        if (!isset($maintenance['verificacoes_anuais'])) {
            $issues[] = [
                'type' => 'MISSING_ANNUAL_CHECKS',
                'severity' => 'LOW',
                'description' => 'Verificações anuais ausentes'
            ];
        }

        return $issues;
    }

    private function checkCriticalParts(array $content): array
    {
        $issues = [];
        
        if (!isset($content['pecas_atencao']) || empty($content['pecas_atencao'])) {
            $issues[] = [
                'type' => 'MISSING_CRITICAL_PARTS',
                'severity' => 'HIGH',
                'description' => 'Peças críticas ausentes'
            ];
            return $issues;
        }

        if (count($content['pecas_atencao']) < 4) {
            $issues[] = [
                'type' => 'INSUFFICIENT_CRITICAL_PARTS',
                'severity' => 'MEDIUM',
                'description' => 'Menos de 4 peças críticas (' . count($content['pecas_atencao']) . ')'
            ];
        }

        return $issues;
    }

    private function checkRequiredSections(array $content): array
    {
        $issues = [];
        $requiredSections = [
            'introducao' => 'Introdução',
            'visao_geral_revisoes' => 'Visão geral',
            'perguntas_frequentes' => 'FAQs',
            'consideracoes_finais' => 'Considerações finais'
        ];

        foreach ($requiredSections as $key => $name) {
            if (!isset($content[$key]) || empty($content[$key])) {
                $issues[] = [
                    'type' => 'MISSING_SECTION',
                    'severity' => 'MEDIUM',
                    'description' => "Seção '{$name}' ausente"
                ];
            }
        }

        // Verificar quantidade de FAQs
        if (isset($content['perguntas_frequentes']) && 
            is_array($content['perguntas_frequentes']) && 
            count($content['perguntas_frequentes']) < 4) {
            
            $issues[] = [
                'type' => 'INSUFFICIENT_FAQS',
                'severity' => 'LOW',
                'description' => 'Menos de 4 FAQs (' . count($content['perguntas_frequentes']) . ')'
            ];
        }

        return $issues;
    }

    private function recordBrokenArticle($article, array $issues): void
    {
        // Content é sempre um array
        $content = $article->content;
        $vehicleInfo = $content['extracted_entities'] ?? [];
        $vehicleType = $vehicleInfo['tipo_veiculo'] ?? 'unknown';
        
        $this->brokenArticles[] = [
            'id' => $article->_id ?? $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'vehicle' => [
                'marca' => $vehicleInfo['marca'] ?? 'N/A',
                'modelo' => $vehicleInfo['modelo'] ?? 'N/A',
                'ano' => $vehicleInfo['ano'] ?? 'N/A',
                'tipo' => $vehicleType
            ],
            'issues' => $issues,
            'created_at' => $article->created_at ?? null
        ];

        $this->statistics['total_broken']++;
        
        // Contar por tipo de veículo
        if (!isset($this->statistics['broken_by_vehicle_type'][$vehicleType])) {
            $this->statistics['broken_by_vehicle_type'][$vehicleType] = 0;
        }
        $this->statistics['broken_by_vehicle_type'][$vehicleType]++;

        // Contar por tipo de problema
        foreach ($issues as $issue) {
            $type = $issue['type'];
            if (!isset($this->statistics['issues_by_type'][$type])) {
                $this->statistics['issues_by_type'][$type] = 0;
            }
            $this->statistics['issues_by_type'][$type]++;
        }
    }

    private function generateReport(string $outputFile): void
    {
        $logContent = "=== RELATÓRIO DE ANÁLISE DE ARTIGOS QUEBRADOS ===\n";
        $logContent .= "Data: " . now()->format('d/m/Y H:i:s') . "\n\n";
        
        $logContent .= "ESTATÍSTICAS GERAIS:\n";
        $logContent .= "- Total analisado: {$this->statistics['total_analyzed']}\n";
        $logContent .= "- Total com problemas: {$this->statistics['total_broken']}\n";
        
        if ($this->statistics['total_analyzed'] > 0) {
            $percentage = round(($this->statistics['total_broken'] / $this->statistics['total_analyzed']) * 100, 2);
            $logContent .= "- Percentual de problemas: {$percentage}%\n\n";
        }
        
        $logContent .= "PROBLEMAS POR TIPO:\n";
        arsort($this->statistics['issues_by_type']);
        foreach ($this->statistics['issues_by_type'] as $type => $count) {
            $logContent .= "- {$type}: {$count}\n";
        }
        
        $logContent .= "\nPROBLEMAS POR TIPO DE VEÍCULO:\n";
        arsort($this->statistics['broken_by_vehicle_type']);
        foreach ($this->statistics['broken_by_vehicle_type'] as $vehicleType => $count) {
            $logContent .= "- {$vehicleType}: {$count}\n";
        }

        $logContent .= "\n=== DETALHES DOS ARTIGOS COM PROBLEMAS ===\n\n";
        
        foreach ($this->brokenArticles as $article) {
            $logContent .= "ID: {$article['id']}\n";
            $logContent .= "Título: {$article['title']}\n";
            $logContent .= "Slug: {$article['slug']}\n";
            $logContent .= "Veículo: {$article['vehicle']['marca']} {$article['vehicle']['modelo']} {$article['vehicle']['ano']}\n";
            $logContent .= "Tipo: {$article['vehicle']['tipo']}\n";
            $logContent .= "Problemas:\n";
            
            foreach ($article['issues'] as $issue) {
                $logContent .= "  - [{$issue['severity']}] {$issue['type']}: {$issue['description']}\n";
            }
            
            $logContent .= str_repeat('-', 80) . "\n";
        }

        file_put_contents($outputFile, $logContent);
        
        $this->info("📄 Relatório salvo em: {$outputFile}");
    }

    private function displayStatistics(): void
    {
        $this->newLine();
        $this->info('📊 ESTATÍSTICAS FINAIS:');
        
        $percentage = $this->statistics['total_analyzed'] > 0 ? 
            round(($this->statistics['total_broken'] / $this->statistics['total_analyzed']) * 100, 2) : 0;
        
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Total Analisado', $this->statistics['total_analyzed']],
                ['Total com Problemas', $this->statistics['total_broken']],
                ['Percentual de Problemas', $percentage . '%']
            ]
        );

        if (!empty($this->statistics['issues_by_type'])) {
            $this->newLine();
            $this->info('🔥 TIPOS DE PROBLEMAS ENCONTRADOS:');
            $topIssues = array_slice($this->statistics['issues_by_type'], 0, 10, true);
            $issueTable = [];
            foreach ($topIssues as $type => $count) {
                $issueTable[] = [$type, $count];
            }
            $this->table(['Tipo de Problema', 'Ocorrências'], $issueTable);
        }

        if (!empty($this->statistics['broken_by_vehicle_type'])) {
            $this->newLine();
            $this->info('🚗 PROBLEMAS POR TIPO DE VEÍCULO:');
            $vehicleTable = [];
            foreach ($this->statistics['broken_by_vehicle_type'] as $type => $count) {
                $vehicleTable[] = [$type, $count];
            }
            $this->table(['Tipo de Veículo', 'Artigos com Problemas'], $vehicleTable);
        }

        if ($this->statistics['total_broken'] > 0) {
            $this->newLine();
            $this->warn("⚠️  Encontrados {$this->statistics['total_broken']} artigos com problemas!");
            $this->info("💡 Use o comando de correção para resolver automaticamente:");
            $this->line("   php artisan review-schedule:auto-fix --limit=1000 --dry-run");
        } else {
            $this->newLine();
            $this->info("✅ Todos os artigos analisados estão corretos!");
        }
    }
}