<?php

namespace Src\ContentGeneration\ReviewSchedule\Console;

use Illuminate\Console\Command;
use Src\AutoInfoCenter\Domain\Eloquent\Article;

class CleanupArticleTemplateReviewTicker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticker:cleanup-template-review 
                            {--dry-run : Visualiza os registros sem deletar}
                            {--batch-size=50 : Quantidade de registros por lote}
                            {--force : Pula a confirmação de exclusão}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove artigos com template = "review_schedule"';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $batchSize = (int) $this->option('batch-size');
        $force = $this->option('force');

        $this->line('');
        $this->info('🔍 Iniciando limpeza de artigos com template "review_schedule"...');
        
        if ($isDryRun) {
            $this->warn('⚠️  MODO PREVIEW: Nenhum artigo será removido');
        }

        // Conta artigos a serem processados
        $articlesCount = Article::where('template', 'review_schedule')->count();
        
        if ($articlesCount === 0) {
            $this->info('✅ Nenhum artigo encontrado com template = "review_schedule"');
            return Command::SUCCESS;
        }

        $this->info("📊 Artigos encontrados: {$articlesCount}");
        
        if ($isDryRun) {
            $this->showPreviewArticles();
            return Command::SUCCESS;
        }

        // Solicita confirmação se não for forçado
        if (!$force && !$this->confirmDeletion($articlesCount)) {
            $this->info('❌ Operação cancelada');
            return Command::FAILURE;
        }

        // Executa a exclusão
        $this->performDeletion($articlesCount, $batchSize);

        return Command::SUCCESS;
    }

    /**
     * Mostra uma prévia dos artigos que seriam deletados
     */
    private function showPreviewArticles()
    {
        $previewArticles = Article::where('template', 'review_schedule')
            ->take(10)
            ->get(['_id', 'title', 'template', 'status', 'created_at']);

        if ($previewArticles->isEmpty()) {
            return;
        }

        $this->line('');
        $this->info('📋 Prévia dos artigos (primeiros 10):');
        
        $tableData = $previewArticles->map(function ($article) {
            return [
                'ID' => substr($article->_id, -8),
                'Título' => $this->truncateText($article->title ?? 'Sem título', 40),
                'Template' => $article->template,
                'Status' => $article->status ?? 'N/A',
                'Criado em' => $article->created_at?->format('d/m/Y H:i') ?? 'N/A'
            ];
        })->toArray();

        $this->table(
            ['ID', 'Título', 'Template', 'Status', 'Criado em'],
            $tableData
        );

        if (Article::where('template', 'review_schedule')->count() > 10) {
            $remaining = Article::where('template', 'review_schedule')->count() - 10;
            $this->info("... e mais {$remaining} artigos");
        }
    }

    /**
     * Solicita confirmação para a exclusão
     */
    private function confirmDeletion(int $count): bool
    {
        $this->line('');
        $this->warn("⚠️  Esta operação irá DELETAR {$count} artigos permanentemente!");
        $this->warn('Esta ação NÃO pode ser desfeita.');
        
        return $this->confirm('Tem certeza que deseja continuar?', false);
    }

    /**
     * Executa a exclusão dos artigos
     */
    private function performDeletion(int $totalCount, int $batchSize)
    {
        $this->line('');
        $this->info('🗑️  Iniciando exclusão...');

        $deletedCount = 0;
        $errorCount = 0;
        $progressBar = $this->output->createProgressBar($totalCount);
        $progressBar->setFormat('verbose');
        $progressBar->start();

        do {
            // Busca artigos em lotes
            $articles = Article::where('template', 'review_schedule')
                ->take($batchSize)
                ->get(['_id', 'title']);

            if ($articles->isEmpty()) {
                break;
            }

            // Deleta cada artigo do lote
            foreach ($articles as $article) {
                try {
                    $article->delete();
                    $deletedCount++;
                    $progressBar->advance();
                } catch (\Exception $e) {
                    $errorCount++;
                    $this->error("\nErro ao deletar artigo {$article->_id}: " . $e->getMessage());
                }
            }

            // Pausa breve para não sobrecarregar o banco
            if ($articles->count() === $batchSize) {
                usleep(50000); // 0.05 segundos
            }

        } while ($articles->count() === $batchSize);

        $progressBar->finish();
        $this->line('');
        $this->line('');

        // Relatório final
        $this->info("✅ Limpeza concluída!");
        $this->info("📊 Artigos deletados: {$deletedCount}");
        
        if ($errorCount > 0) {
            $this->warn("⚠️  Erros encontrados: {$errorCount}");
        }

        // Verifica se ainda existem artigos
        $remainingArticles = Article::where('template', 'review_schedule')->count();
        if ($remainingArticles > 0) {
            $this->warn("⚠️  Ainda restam {$remainingArticles} artigos com template 'review_schedule'");
            $this->info("💡 Execute o comando novamente para tentar remover os restantes");
        } else {
            $this->info("🎉 Todos os artigos com template 'review_schedule' foram removidos!");
        }
    }

    /**
     * Trunca texto para exibição
     */
    private function truncateText(?string $text, int $limit): string
    {
        if (empty($text)) {
            return 'N/A';
        }
        
        return strlen($text) > $limit 
            ? substr($text, 0, $limit) . '...' 
            : $text;
    }
}