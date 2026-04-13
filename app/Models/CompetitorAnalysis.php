<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitorAnalysis extends Model
{
    protected $table = 'competitor_analyses';

    protected $fillable = [
        'keyword', 'our_url', 'our_position', 'top_competitors',
        'content_gaps', 'recommendations', 'search_volume', 'difficulty',
        'data_source', 'analyzed_at',
    ];

    protected $casts = [
        'top_competitors' => 'array',
        'content_gaps' => 'array',
        'recommendations' => 'array',
        'analyzed_at' => 'date',
    ];

    /**
     * Is this analysis based on real SERP data?
     */
    public function isRealData(): bool
    {
        return in_array($this->data_source, ['google_serp', 'searxng']);
    }

    /**
     * Human-readable data source label.
     */
    public function getDataSourceLabel(): string
    {
        return match ($this->data_source) {
            'searxng' => 'SearXNG (Open-Source)',
            'google_serp' => 'Google SERP',
            default => 'AI Estimasi',
        };
    }

    public function scopeRecent($query)
    {
        return $query->where('analyzed_at', '>=', now()->subDays(30));
    }

    public function scopeHighPriority($query)
    {
        return $query->whereNotNull('our_position')->where('our_position', '>', 10);
    }
}
