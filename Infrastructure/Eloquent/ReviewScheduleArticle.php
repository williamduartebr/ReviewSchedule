<?php

namespace Src\ContentGeneration\ReviewSchedule\Infrastructure\Eloquent;

use MongoDB\Laravel\Eloquent\Model;

class ReviewScheduleArticle extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'review_schedule_temp_articles';
    protected $guarded = ['_id'];

    protected $casts = [
        'vehicle_info' => 'array',
        'extracted_entities' => 'array',
        'seo_data' => 'array',
        'content' => 'array',
        'blog_published_time' => 'datetime',
        'blog_modified_time' => 'datetime',
        'blog_synced' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $fillable = [
        'title',
        'slug',
        'new_slug',
        'vehicle_info',
        'extracted_entities',
        'seo_data',
        'content',
        'template',
        'status',
        'source',
        'domain',
        // Campos do blog
        'blog_id',
        'blog_status',
        'blog_published_time',
        'blog_modified_time',
        'blog_synced',
        'created_at',
        'updated_at'
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeByVehicleType($query, string $vehicleType)
    {
        return $query->where('vehicle_info.vehicle_type', $vehicleType);
    }

    public function scopeByMake($query, string $make)
    {
        return $query->where('vehicle_info.make', $make);
    }

    public function scopeByYear($query, int $year)
    {
        return $query->where('vehicle_info.year', $year);
    }

    public function getVehicleFullNameAttribute(): string
    {
        $vehicleInfo = $this->vehicle_info ?? [];
        return trim(sprintf('%s %s %s', 
            $vehicleInfo['make'] ?? '', 
            $vehicleInfo['model'] ?? '', 
            $vehicleInfo['year'] ?? ''
        ));
    }

    public function getEstimatedReadingTimeAttribute(): int
    {
        $content = $this->content ?? [];
        $wordCount = 0;
        
        foreach ($content as $section) {
            if (is_string($section)) {
                $wordCount += str_word_count(strip_tags($section));
            } elseif (is_array($section)) {
                $wordCount += $this->countWordsInArray($section);
            }
        }
        
        return max(1, ceil($wordCount / 200)); // 200 words per minute
    }

    private function countWordsInArray(array $data): int
    {
        $wordCount = 0;
        
        foreach ($data as $item) {
            if (is_string($item)) {
                $wordCount += str_word_count(strip_tags($item));
            } elseif (is_array($item)) {
                $wordCount += $this->countWordsInArray($item);
            }
        }
        
        return $wordCount;
    }
}