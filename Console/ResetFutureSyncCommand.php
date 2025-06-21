<?php

namespace Src\ContentGeneration\ReviewSchedule\Console;

use Illuminate\Console\Command;
use Src\ContentGeneration\ReviewSchedule\Infrastructure\Eloquent\ReviewScheduleArticle;

class ResetFutureSyncCommand extends Command
{
    protected $signature = 'review-schedule:reset-future-sync {--limit=100} {--dry-run : Show what would be reset without making changes}';
    protected $description = 'Reset blog_synced for articles with blog_status = future';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');

        // Buscar artigos com status "future" que já foram sincronizados
        $articles = ReviewScheduleArticle::where('blog_status', 'future')
            ->whereNotNull('blog_synced')
            ->limit($limit)
            ->get();

        if ($articles->isEmpty()) {
            $this->info('Nenhum artigo com status "future" encontrado para reset.');
            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->info("=== DRY RUN - Nenhuma alteração será feita ===");
            $this->info("Encontrados {$articles->count()} artigos que seriam resetados:");
            $this->newLine();
            
            $this->table(
                ['ID', 'Slug', 'Blog ID', 'Blog Status', 'Blog Synced', 'Modified Time'],
                $articles->map(function ($article) {
                    return [
                        $article->_id ?? $article->id,
                        $article->slug,
                        $article->blog_id,
                        $article->blog_status,
                        $article->blog_synced ? 'TRUE' : 'FALSE',
                        $article->blog_modified_time ?? 'N/A'
                    ];
                })->toArray()
            );
            
            $this->newLine();
            $this->warn("Para executar as alterações, rode o comando sem --dry-run");
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($articles as $article) {
            $article->update(['blog_synced' => null]);
            $count++;
        }

        $this->info("Reset realizado em {$count} artigos com status 'future'.");
        
        return self::SUCCESS;
    }
}