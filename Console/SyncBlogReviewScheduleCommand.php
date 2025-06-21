<?php

namespace Src\ContentGeneration\ReviewSchedule\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Src\ContentGeneration\ReviewSchedule\Infrastructure\Eloquent\ReviewScheduleArticle;

class SyncBlogReviewScheduleCommand extends Command
{
    protected $signature = 'review-schedule:sync-blog {--limit=10}';
    protected $description = 'Sync blog data for review schedule articles';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        // Buscar artigos que não foram sincronizados
        $articles = ReviewScheduleArticle::whereNotNull('slug')
            ->whereNull('blog_synced')
            ->limit($limit)
            ->get();

        if ($articles->isEmpty()) {
            return self::SUCCESS;
        }

        foreach ($articles as $article) {
            $this->syncArticle($article);
            usleep(100000); // 0.1 segundo para não sobrecarregar
        }

        return self::SUCCESS;
    }

    private function syncArticle(ReviewScheduleArticle $article): void
    {
        try {
            $url = "https://blog.mercadoveiculos.com/?get_json=1&slug=cronograma-revisoes-{$article->slug}";
            $response = Http::timeout(10)->get($url);

            if (!$response->successful() || !$response->json('success')) {
                return;
            }

            $data = $response->json('data');

            // Determinar as datas de publicação e modificação
            $publishedTime = $this->getPublishedTime($data);
            $modifiedTime = $this->getModifiedTime($data);

            $article->update([
                'blog_id' => $data['id'] ?? null,
                'blog_status' => $data['status'] ?? null,
                'blog_published_time' => $this->fixTimezoneDate($publishedTime),
                'blog_modified_time' => $this->fixTimezoneDate($modifiedTime),
                'blog_synced' => true
            ]);
        } catch (\Exception $e) {
            // Falha silenciosa para não quebrar o schedule
        }
    }


    /**
     * Corrige datas adicionando 4 horas para compensar diferença de fuso horário
     */
    private function fixTimezoneDate($dateString): ?string
    {
        if (!$dateString) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($dateString)->addHours(1)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            Log::warning('Erro ao corrigir data', ['date' => $dateString, 'error' => $e->getMessage()]);
            return $dateString;
        }
    }

    private function getPublishedTime(array $data): ?string
    {
        // Tentar pegar do SEO primeiro
        if (isset($data['seo']['_yoast_wpseo_article_published_time'])) {
            return $data['seo']['_yoast_wpseo_article_published_time'];
        }

        // Tentar pegar do metadata
        if (isset($data['metadata']['_yoast_wpseo_article_published_time'])) {
            return $data['metadata']['_yoast_wpseo_article_published_time'];
        }

        // Se não tem, usar o campo date e converter para UTC ISO
        if (isset($data['date'])) {
            return $this->convertToUtcIso($data['date']);
        }

        return null;
    }

    private function getModifiedTime(array $data): ?string
    {
        // Tentar pegar do SEO primeiro
        if (isset($data['seo']['_yoast_wpseo_article_modified_time'])) {
            return $data['seo']['_yoast_wpseo_article_modified_time'];
        }

        // Tentar pegar do metadata
        if (isset($data['metadata']['_yoast_wpseo_article_modified_time'])) {
            return $data['metadata']['_yoast_wpseo_article_modified_time'];
        }

        // Se não tem, usar o campo date e converter para UTC ISO
        if (isset($data['date'])) {
            return $this->convertToUtcIso($data['date']);
        }

        return null;
    }

    private function convertToUtcIso(string $date): string
    {
        // Converter data do formato "2025-05-01 22:10:56" para "2025-05-01T22:10:56+00:00"
        try {
            $carbon = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date, 'UTC');
            return $carbon->toAtomString();
        } catch (\Exception $e) {
            // Se falhar, tentar parse direto
            try {
                $carbon = \Carbon\Carbon::parse($date)->utc();
                return $carbon->toAtomString();
            } catch (\Exception $e) {
                return $date; // Retorna original se não conseguir converter
            }
        }
    }
}
