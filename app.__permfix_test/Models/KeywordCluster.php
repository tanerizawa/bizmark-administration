<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeywordCluster extends Model
{
    protected $fillable = [
        'seed_keyword',
        'cluster_name',
        'search_intent',
        'keywords',
        'long_tail_keywords',
        'language',
        'category',
        'service_slug',
        'estimated_volume',
        'difficulty_score',
        'priority',
        'articles_count',
        'status',
        'last_researched_at',
        // GSC real-data columns (populated by seo:gsc-import --crossref)
        'gsc_clicks',
        'gsc_impressions',
        'gsc_avg_position',
        'gsc_ctr',
        'gsc_synced_at',
    ];

    protected $casts = [
        'keywords' => 'array',
        'long_tail_keywords' => 'array',
        'estimated_volume' => 'integer',
        'difficulty_score' => 'integer',
        'priority' => 'integer',
        'articles_count' => 'integer',
        'last_researched_at' => 'datetime',
        'gsc_clicks' => 'integer',
        'gsc_impressions' => 'integer',
        'gsc_avg_position' => 'float',
        'gsc_ctr' => 'float',
        'gsc_synced_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByService($query, string $serviceSlug)
    {
        return $query->where('service_slug', $serviceSlug);
    }

    public function scopeByIntent($query, string $intent)
    {
        return $query->where('search_intent', $intent);
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', '>=', 70)->orderByDesc('priority');
    }

    public function scopeUncovered($query)
    {
        return $query->where('articles_count', 0);
    }

    public function getTopKeywords(int $limit = 10): array
    {
        return array_slice($this->long_tail_keywords ?? $this->keywords ?? [], 0, $limit);
    }
}
