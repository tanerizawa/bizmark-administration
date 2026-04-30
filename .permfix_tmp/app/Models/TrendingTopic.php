<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trending Topic Model
 *
 * Stores trending topics discovered via SearXNG for content generation.
 *
 * @property int $id
 * @property string $topic
 * @property string $category
 * @property string $language
 * @property string $data_source
 * @property int $trend_score
 * @property int|null $search_volume
 * @property array|null $related_keywords
 * @property array|null $top_sources
 * @property string|null $sample_headline
 * @property bool $is_processed
 * @property int|null $article_id
 * @property \Carbon\Carbon $discovered_at
 * @property \Carbon\Carbon|null $expires_at
 */
class TrendingTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic',
        'category',
        'language',
        'data_source',
        'trend_score',
        'search_volume',
        'related_keywords',
        'top_sources',
        'sample_headline',
        'is_processed',
        'article_id',
        'discovered_at',
        'expires_at',
    ];

    protected $casts = [
        'trend_score' => 'integer',
        'search_volume' => 'integer',
        'related_keywords' => 'array',
        'top_sources' => 'array',
        'is_processed' => 'boolean',
        'discovered_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Relationship: Article generated from this trending topic
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Scope: Unprocessed topics (no article generated yet)
     */
    public function scopeUnprocessed($query)
    {
        return $query->where('is_processed', false);
    }

    /**
     * Scope: Active trending topics (not expired)
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope: High priority topics (score >= 70)
     */
    public function scopeHighPriority($query)
    {
        return $query->where('trend_score', '>=', 70);
    }

    /**
     * Scope: Filter by category
     */
    public function scopeInCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Recently discovered (last 7 days)
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('discovered_at', '>=', now()->subDays($days));
    }

    /**
     * Mark topic as processed with article generated
     */
    public function markProcessed(int $articleId): void
    {
        $this->update([
            'is_processed' => true,
            'article_id' => $articleId,
        ]);
    }

    /**
     * Check if topic is still trending (not expired)
     */
    public function isTrending(): bool
    {
        return $this->expires_at === null || $this->expires_at->isFuture();
    }

    /**
     * Get human-readable category label
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'umkm' => 'UMKM & Bisnis',
            'perizinan' => 'Perizinan & Regulasi',
            'legal' => 'Hukum & Legal',
            'marketing' => 'Marketing & Digital',
            'technology' => 'Teknologi',
            'finance' => 'Keuangan & Pajak',
            default => ucfirst($this->category),
        };
    }

    /**
     * Get human-readable data source label
     */
    public function getDataSourceLabelAttribute(): string
    {
        return match ($this->data_source) {
            'searxng' => 'SearXNG',
            'google_trends' => 'Google Trends',
            'ai_analysis' => 'AI Analysis',
            default => ucfirst($this->data_source),
        };
    }

    /**
     * Get trend badge color
     */
    public function getTrendBadgeAttribute(): string
    {
        return match (true) {
            $this->trend_score >= 80 => 'red',      // Hot
            $this->trend_score >= 60 => 'orange',   // Warm
            $this->trend_score >= 40 => 'yellow',   // Rising
            default => 'blue',                       // Normal
        };
    }
}
