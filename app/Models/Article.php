<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'category',
        'tags',
        'status',
        'published_at',
        'views_count',
        'author_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_featured',
        'reading_time',
    ];

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
    ];

    protected $dates = [
        'published_at',
        'deleted_at',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
            
            // Auto-generate excerpt if not provided
            if (empty($article->excerpt) && !empty($article->content)) {
                $article->excerpt = Str::limit(strip_tags($article->content), 200);
            }
            
            // Calculate reading time (average 200 words per minute)
            if (!empty($article->content)) {
                $wordCount = str_word_count(strip_tags($article->content));
                $article->reading_time = ceil($wordCount / 200);
            }
        });

        static::updating(function ($article) {
            // Update slug if title changed
            if ($article->isDirty('title')) {
                $article->slug = Str::slug($article->title);
            }
            
            // Recalculate reading time if content changed
            if ($article->isDirty('content')) {
                $wordCount = str_word_count(strip_tags($article->content));
                $article->reading_time = ceil($wordCount / 200);
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Relationships
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scopes
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', Carbon::now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%")
              ->orWhere('excerpt', 'like', "%{$search}%");
        });
    }

    public function scopeRecent($query, $limit = 5)
    {
        return $query->published()
            ->orderBy('published_at', 'desc')
            ->limit($limit);
    }

    public function scopePopular($query, $limit = 5)
    {
        return $query->published()
            ->orderBy('views_count', 'desc')
            ->limit($limit);
    }

    /**
     * Accessors
     */
    public function getFormattedPublishedAtAttribute()
    {
        return $this->published_at ? $this->published_at->format('d F Y') : null;
    }

    public function getReadingTimeTextAttribute()
    {
        return $this->reading_time . ' min read';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-500 text-white">Draft</span>',
            'published' => '<span class="px-2 py-1 text-xs rounded-full bg-green-500 text-white">Published</span>',
            'archived' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-500 text-white">Archived</span>',
        ];

        return $badges[$this->status] ?? $badges['draft'];
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
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function publish()
    {
        $this->update([
            'status' => 'published',
            'published_at' => Carbon::now(),
        ]);
    }

    public function unpublish()
    {
        $this->update([
            'status' => 'draft',
        ]);
    }

    public function archive()
    {
        $this->update([
            'status' => 'archived',
        ]);
    }

    public function isPublished()
    {
        return $this->status === 'published' && 
               $this->published_at !== null && 
               $this->published_at->isPast();
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isArchived()
    {
        return $this->status === 'archived';
    }

    public function getUrl()
    {
        return route('blog.article', $this->slug);
    }

    /**
     * Get all available categories
     */
    public static function getCategories()
    {
        return [
            'general' => 'Umum',
            'news' => 'Berita',
            'case-study' => 'Studi Kasus',
            'tips' => 'Tips & Panduan',
            'regulation' => 'Regulasi',
        ];
    }

    /**
     * Get related articles
     */
    public function getRelatedArticles($limit = 3)
    {
        return self::published()
            ->where('id', '!=', $this->id)
            ->where(function($query) {
                $query->where('category', $this->category);
                
                if (!empty($this->tags)) {
                    foreach ($this->tags as $tag) {
                        $query->orWhereJsonContains('tags', $tag);
                    }
                }
            })
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
