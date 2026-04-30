<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ArticleTopic extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'category',
        'language',
        'target_market',
        'keywords',
        'tags',
        'status',
        'priority',
        'article_id',
        'published_at',
        'scheduled_for',
        'generation_notes',
        'views_count',
        'similarity_score',
        'topic_cluster_id',
    ];

    protected $casts = [
        'keywords' => 'array',
        'tags' => 'array',
        'published_at' => 'datetime',
        'scheduled_for' => 'datetime',
        'views_count' => 'integer',
        'priority' => 'integer',
        'similarity_score' => 'float',
    ];

    /**
     * Boot method - Auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($topic) {
            if (empty($topic->slug)) {
                $topic->slug = Str::slug($topic->title);
            }
        });

        static::updating(function ($topic) {
            if ($topic->isDirty('title')) {
                $topic->slug = Str::slug($topic->title);
            }
        });
    }

    /**
     * Relationships
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function topicCluster()
    {
        return $this->belongsTo(TopicCluster::class);
    }

    public function schedules()
    {
        return $this->hasMany(AutoPostSchedule::class, 'topic_id');
    }

    public function logs()
    {
        return $this->hasMany(AutoPostLog::class, 'topic_id');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    public function scopeByMarket($query, $market)
    {
        return $query->where(function ($q) use ($market) {
            $q->where('target_market', $market)
                ->orWhere('target_market', 'both');
        });
    }

    public function scopeHighPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('scheduled_for')
                    ->orWhere('scheduled_for', '<', now()->subHours(24)); // Topics stuck in scheduling for >24h
            });
    }

    /**
     * Accessors
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-500 text-white">Pending</span>',
            'processing' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-500 text-white">Processing</span>',
            'published' => '<span class="px-2 py-1 text-xs rounded-full bg-green-500 text-white">Published</span>',
            'failed' => '<span class="px-2 py-1 text-xs rounded-full bg-red-500 text-white">Failed</span>',
            'archived' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-500 text-white">Archived</span>',
        ];

        return $badges[$this->status] ?? $badges['pending'];
    }

    public function getCategoryLabelAttribute()
    {
        $labels = [
            'general' => 'Umum',
            'news' => 'Berita',
            'case-study' => 'Studi Kasus',
            'tips' => 'Tips & Panduan',
            'regulation' => 'Regulasi',
        ];

        return $labels[$this->category] ?? 'Umum';
    }

    /**
     * Helper Methods
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isPublished()
    {
        return $this->status === 'published';
    }

    public function markAsProcessing()
    {
        $this->update(['status' => 'processing']);
    }

    public function markAsPublished($articleId)
    {
        $this->update([
            'status' => 'published',
            'article_id' => $articleId,
            'published_at' => now(),
            'scheduled_for' => null, // Clear scheduled time after publishing
        ]);
    }

    public function markAsFailed()
    {
        $this->update([
            'status' => 'failed',
            'scheduled_for' => null, // Clear scheduling on failure
        ]);
    }

    public function markAsScheduled($scheduledAt = null)
    {
        $this->update([
            'scheduled_for' => $scheduledAt ?? now(),
        ]);
    }

    public function clearScheduling()
    {
        $this->update(['scheduled_for' => null]);
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
