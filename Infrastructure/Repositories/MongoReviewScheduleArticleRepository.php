<?php

namespace Src\ContentGeneration\ReviewSchedule\Infrastructure\Repositories;

use Illuminate\Support\Facades\Log;
use Src\ContentGeneration\ReviewSchedule\Domain\Entities\ReviewScheduleArticle;
use Src\ContentGeneration\ReviewSchedule\Infrastructure\Eloquent\ReviewScheduleArticle as EloquentReviewScheduleArticle;

class MongoReviewScheduleArticleRepository
{
    public function save(ReviewScheduleArticle $article): bool
    {
        try {
            // Verificar se já existe artigo com o mesmo slug
            if ($this->exists($article->getSlug())) {
                Log::warning('Tentativa de salvar artigo com slug duplicado', [
                    'slug' => $article->getSlug(),
                    'title' => $article->getTitle()
                ]);
                return false; // Não salva se slug já existe
            }

            $eloquentArticle = new EloquentReviewScheduleArticle();
            $eloquentArticle->fill($article->toArray());
            
            return $eloquentArticle->save();
        } catch (\Exception $e) {
            Log::error('Error saving review schedule article: ' . $e->getMessage(), [
                'article_title' => $article->getTitle(),
                'article_slug' => $article->getSlug(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function saveBatch(array $articles): array
    {
        $results = [
            'saved' => 0,
            'failed' => 0,
            'duplicated' => 0,
            'errors' => []
        ];

        foreach ($articles as $article) {
            try {
                // Verificar se é uma instância válida
                if (!($article instanceof ReviewScheduleArticle)) {
                    $results['failed']++;
                    $results['errors'][] = "Invalid article instance provided";
                    continue;
                }

                // Verificar se já existe antes de tentar salvar
                if ($this->exists($article->getSlug())) {
                    $results['duplicated']++;
                    $results['errors'][] = "Slug duplicado (ignorado): {$article->getSlug()}";
                    continue;
                }

                if ($this->save($article)) {
                    $results['saved']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = "Failed to save: {$article->getTitle()}";
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Exception during save: " . $e->getMessage();
                Log::error('Exception in saveBatch: ' . $e->getMessage());
            }
        }

        return $results;
    }

    public function findBySlug(string $slug): ?array
    {
        try {
            $article = EloquentReviewScheduleArticle::where('slug', $slug)->first();
            return $article ? $article->toArray() : null;
        } catch (\Exception $e) {
            Log::error('Error finding article by slug: ' . $e->getMessage(), [
                'slug' => $slug
            ]);
            return null;
        }
    }

    public function findByStatus(string $status, int $limit = 100): array
    {
        try {
            return EloquentReviewScheduleArticle::where('status', $status)
                ->limit($limit)
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error finding articles by status: ' . $e->getMessage(), [
                'status' => $status
            ]);
            return [];
        }
    }

    public function updateStatus(string $slug, string $status): bool
    {
        try {
            return EloquentReviewScheduleArticle::where('slug', $slug)
                ->update([
                    'status' => $status,
                    'updated_at' => now()
                ]) > 0;
        } catch (\Exception $e) {
            Log::error('Error updating article status: ' . $e->getMessage(), [
                'slug' => $slug,
                'status' => $status
            ]);
            return false;
        }
    }

    public function exists(string $slug): bool
    {
        try {
            return EloquentReviewScheduleArticle::where('slug', $slug)->exists();
        } catch (\Exception $e) {
            Log::error('Error checking article existence: ' . $e->getMessage(), [
                'slug' => $slug,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function count(): int
    {
        try {
            return EloquentReviewScheduleArticle::count();
        } catch (\Exception $e) {
            Log::error('Error counting articles: ' . $e->getMessage());
            return 0;
        }
    }

    public function countByStatus(string $status): int
    {
        try {
            return EloquentReviewScheduleArticle::where('status', $status)->count();
        } catch (\Exception $e) {
            Log::error('Error counting articles by status: ' . $e->getMessage(), [
                'status' => $status
            ]);
            return 0;
        }
    }

    public function deleteBySlug(string $slug): bool
    {
        try {
            return EloquentReviewScheduleArticle::where('slug', $slug)->delete() > 0;
        } catch (\Exception $e) {
            Log::error('Error deleting article: ' . $e->getMessage(), [
                'slug' => $slug
            ]);
            return false;
        }
    }

    public function findDuplicatedSlugs(): array
    {
        try {
            return EloquentReviewScheduleArticle::select('slug')
                ->groupBy('slug')
                ->havingRaw('count(*) > 1')
                ->pluck('slug')
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error finding duplicated slugs: ' . $e->getMessage());
            return [];
        }
    }

    public function getSlugVariations(string $baseSlug): array
    {
        try {
            return EloquentReviewScheduleArticle::where('slug', 'like', $baseSlug . '%')
                ->pluck('slug')
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting slug variations: ' . $e->getMessage(), [
                'base_slug' => $baseSlug
            ]);
            return [];
        }
    }

    /**
     * Método para debug - verificar problemas na base
     */
    public function debugDatabase(): array
    {
        try {
            $stats = [
                'total_articles' => $this->count(),
                'draft_articles' => $this->countByStatus('draft'),
                'published_articles' => $this->countByStatus('published'),
                'duplicated_slugs' => $this->findDuplicatedSlugs(),
                'recent_articles' => EloquentReviewScheduleArticle::latest()->take(5)->pluck('slug')->toArray(),
                'connection_info' => [
                    'connection' => config('database.connections.mongodb.connection'),
                    'database' => config('database.connections.mongodb.database'),
                    'collection' => 'review_schedule_temp_articles'
                ]
            ];

            Log::info('Database debug info', $stats);
            return $stats;
        } catch (\Exception $e) {
            Log::error('Error in debugDatabase: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}