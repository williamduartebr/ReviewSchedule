<?php

namespace Src\ContentGeneration\ReviewSchedule\Console;

use Illuminate\Console\Command;
use Src\ContentGeneration\ReviewSchedule\Infrastructure\Eloquent\ReviewScheduleArticle;

class DebugArticlesStructure extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'review-schedule:debug-structure {--limit=5 : Number of articles to debug}';

    /**
     * The console command description.
     */
    protected $description = 'Debug the structure of ReviewScheduleArticle records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int)$this->option('limit');
        
        $this->info('🔍 Debugando estrutura dos artigos...');

        try {
            $articles = ReviewScheduleArticle::limit($limit)->get();
            
            $this->info("📊 Encontrados {$articles->count()} artigos para debug");
            
            foreach ($articles->take(3) as $index => $article) {
                $this->newLine();
                $this->info("=== ARTIGO " . ($index + 1) . " ===");
                $this->line("ID: " . ($article->_id ?? $article->id ?? 'N/A'));
                $this->line("Title: " . ($article->title ?? 'N/A'));
                $this->line("Slug: " . ($article->slug ?? 'N/A'));
                
                // Debug do campo content
                $this->line("Content type: " . gettype($article->content));
                
                if (is_string($article->content)) {
                    $this->line("Content length: " . strlen($article->content));
                    $this->line("First 100 chars: " . substr($article->content, 0, 100) . "...");
                    
                    $decoded = json_decode($article->content, true);
                    if ($decoded) {
                        $this->line("JSON valid: Yes");
                        $this->line("Main sections: " . implode(', ', array_keys($decoded)));
                        
                        // Verificar estruturas específicas
                        $this->checkContentStructure($decoded);
                    } else {
                        $this->error("JSON valid: No");
                        $this->line("JSON error: " . json_last_error_msg());
                    }
                } elseif (is_array($article->content)) {
                    $this->line("Content is array with keys: " . implode(', ', array_keys($article->content)));
                    $this->checkContentStructure($article->content);
                } else {
                    $this->error("Content is neither string nor array");
                }
                
                $this->line(str_repeat('-', 60));
            }
            
            // Estatísticas gerais
            $this->newLine();
            $this->info('📊 ESTATÍSTICAS GERAIS:');
            $this->analyzeAllArticles($articles);
            
        } catch (\Exception $e) {
            $this->error("Erro durante debug: " . $e->getMessage());
            $this->line("Stack trace: " . $e->getTraceAsString());
        }
    }

    private function checkContentStructure(array $content): void
    {
        $requiredSections = [
            'introducao',
            'visao_geral_revisoes', 
            'cronograma_detalhado',
            'manutencao_preventiva',
            'pecas_atencao',
            'perguntas_frequentes'
        ];

        $this->line("Seções encontradas:");
        foreach ($requiredSections as $section) {
            $exists = isset($content[$section]);
            $status = $exists ? '✅' : '❌';
            
            if ($exists) {
                $type = gettype($content[$section]);
                $count = is_array($content[$section]) ? count($content[$section]) : strlen($content[$section]);
                $this->line("  {$status} {$section} ({$type}, size: {$count})");
                
                // Análise específica por seção
                $this->analyzeSection($section, $content[$section]);
            } else {
                $this->line("  {$status} {$section}");
            }
        }
    }

    private function analyzeSection(string $sectionName, $sectionData): void
    {
        switch ($sectionName) {
            case 'cronograma_detalhado':
                if (is_array($sectionData)) {
                    $this->line("    → " . count($sectionData) . " revisões detalhadas");
                    if (count($sectionData) > 0) {
                        $firstRevision = $sectionData[0];
                        $hasServices = isset($firstRevision['servicos_principais']) ? '✅' : '❌';
                        $hasChecks = isset($firstRevision['verificacoes_complementares']) ? '✅' : '❌';
                        $this->line("    → Primeira revisão: serviços {$hasServices}, verificações {$hasChecks}");
                    }
                }
                break;
                
            case 'manutencao_preventiva':
                if (is_array($sectionData)) {
                    $monthly = isset($sectionData['verificacoes_mensais']) ? '✅' : '❌';
                    $quarterly = isset($sectionData['verificacoes_trimestrais']) ? '✅' : '❌';
                    $annual = isset($sectionData['verificacoes_anuais']) ? '✅' : '❌';
                    $this->line("    → Mensais {$monthly}, Trimestrais {$quarterly}, Anuais {$annual}");
                }
                break;
                
            case 'pecas_atencao':
                if (is_array($sectionData)) {
                    $this->line("    → " . count($sectionData) . " peças críticas");
                }
                break;
                
            case 'perguntas_frequentes':
                if (is_array($sectionData)) {
                    $this->line("    → " . count($sectionData) . " FAQs");
                }
                break;
        }
    }

    private function analyzeAllArticles($articles): void
    {
        $stats = [
            'total' => $articles->count(),
            'content_types' => [],
            'valid_json' => 0,
            'invalid_json' => 0,
            'has_detailed_schedule' => 0,
            'missing_annual_checks' => 0,
            'insufficient_critical_parts' => 0
        ];

        foreach ($articles as $article) {
            $contentType = gettype($article->content);
            $stats['content_types'][$contentType] = ($stats['content_types'][$contentType] ?? 0) + 1;

            $content = null;
            if (is_string($article->content)) {
                $content = json_decode($article->content, true);
                if ($content) {
                    $stats['valid_json']++;
                } else {
                    $stats['invalid_json']++;
                }
            } elseif (is_array($article->content)) {
                $content = $article->content;
                $stats['valid_json']++;
            }

            if ($content) {
                // Verificar cronograma detalhado
                if (isset($content['cronograma_detalhado']) && 
                    is_array($content['cronograma_detalhado']) && 
                    count($content['cronograma_detalhado']) >= 3) {
                    $stats['has_detailed_schedule']++;
                }

                // Verificar verificações anuais
                if (!isset($content['manutencao_preventiva']['verificacoes_anuais'])) {
                    $stats['missing_annual_checks']++;
                }

                // Verificar peças críticas
                if (!isset($content['pecas_atencao']) || 
                    !is_array($content['pecas_atencao']) || 
                    count($content['pecas_atencao']) < 4) {
                    $stats['insufficient_critical_parts']++;
                }
            }
        }

        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Total de Artigos', $stats['total']],
                ['JSON Válido', $stats['valid_json']],
                ['JSON Inválido', $stats['invalid_json']],
                ['Com Cronograma Detalhado', $stats['has_detailed_schedule']],
                ['Sem Verificações Anuais', $stats['missing_annual_checks']],
                ['Peças Críticas Insuficientes', $stats['insufficient_critical_parts']]
            ]
        );

        $this->newLine();
        $this->info('📁 TIPOS DE CONTEÚDO:');
        foreach ($stats['content_types'] as $type => $count) {
            $this->line("- {$type}: {$count}");
        }
    }
}