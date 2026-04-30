<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
        'language',
        'tags',
        'status',
        'published_at',
        'views_count',
        'author_id',
        'source_type',
        'topic_cluster_id',
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
                $article->slug = static::generateUniqueSlug($article->title);
            } else {
                // Ensure provided slug is unique
                $article->slug = static::generateUniqueSlug($article->slug, null, true);
            }

            // Auto-generate excerpt if not provided
            if (empty($article->excerpt) && ! empty($article->content)) {
                $article->excerpt = static::generateCleanExcerpt($article->content);
            }

            // Calculate reading time (average 200 words per minute)
            if (! empty($article->content)) {
                $wordCount = str_word_count(strip_tags($article->content));
                $article->reading_time = ceil($wordCount / 200);
            }
        });

        static::updating(function ($article) {
            // Update slug if title changed
            if ($article->isDirty('title')) {
                $article->slug = static::generateUniqueSlug($article->title, $article->id);
            }

            // Recalculate reading time if content changed
            if ($article->isDirty('content')) {
                $wordCount = str_word_count(strip_tags($article->content));
                $article->reading_time = ceil($wordCount / 200);
            }

            // Auto-set published_at when status changed to published
            if ($article->isDirty('status') && $article->status === 'published' && $article->published_at === null) {
                $article->published_at = now();
            }
        });
    }

    /**
     * Generate a clean excerpt from article content.
     * Strips HTML tags, Markdown syntax, headings, and extracts body text only.
     */
    public static function generateCleanExcerpt(string $content, int $limit = 200): string
    {
        // Remove code fence wrappers (```html ... ```)
        $text = preg_replace('/^```\w*\s*\n?/m', '', $content);
        $text = preg_replace('/```\s*$/m', '', $text);
        // Remove HTML headings first (they shouldn't be in excerpts)
        $text = preg_replace('/<h[1-6][^>]*>.*?<\/h[1-6]>/is', ' ', $text);
        // Strip all HTML tags
        $text = strip_tags($text);
        // Remove Markdown headings (## Heading)
        $text = preg_replace('/^#{1,6}\s+.+$/m', '', $text);
        // Remove Markdown bold/italic (**text**, *text*)
        $text = preg_replace('/\*{1,2}([^*]+?)\*{1,2}/', '$1', $text);
        // Remove Markdown links [text](url) → text
        $text = preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $text);
        // Remove Markdown list markers
        $text = preg_replace('/^[\-\*]\s+/m', '', $text);
        // Collapse whitespace and newlines
        $text = preg_replace('/[\r\n]+/', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        return Str::limit($text, $limit);
    }

    /**
     * Generate a unique slug for the article
     *
     * @param  string  $title  The title or base slug
     * @param  int|null  $excludeId  Article ID to exclude from uniqueness check (for updates)
     * @param  bool  $isSlug  Whether the input is already a slug
     * @return string Unique slug
     */
    public static function generateUniqueSlug(string $title, ?int $excludeId = null, bool $isSlug = false): string
    {
        $slug = $isSlug ? $title : Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        // Check for existing slugs and append counter if needed
        while (true) {
            $query = static::withTrashed()->where('slug', $slug);

            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (! $query->exists()) {
                break;
            }

            // Append counter to make unique
            $slug = $originalSlug.'-'.$counter;
            $counter++;

            // Safety limit to prevent infinite loop
            if ($counter > 100) {
                $slug = $originalSlug.'-'.uniqid();
                break;
            }
        }

        return $slug;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get a validated featured image URL.
     * Returns null if the file does not exist on disk.
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (empty($this->featured_image)) {
            return null;
        }

        if (! \Storage::disk('public')->exists($this->featured_image)) {
            return null;
        }

        return \Storage::url($this->featured_image);
    }

    /**
     * Relationships
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function topicCluster()
    {
        return $this->belongsTo(TopicCluster::class);
    }

    public function syndications()
    {
        return $this->hasMany(ContentSyndication::class);
    }

    public function seoScore()
    {
        return $this->hasOne(SeoScore::class);
    }

    public function viewLogs()
    {
        return $this->hasMany(ArticleViewLog::class);
    }

    public function refreshLogs()
    {
        return $this->hasMany(ContentRefreshLog::class);
    }

    public function metaAbTests()
    {
        return $this->hasMany(MetaAbTest::class);
    }

    public function socialPosts()
    {
        return $this->hasMany(SocialPost::class);
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

    public function scopeByLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
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
    public function getTagsAttribute($value)
    {
        if (is_null($value)) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        // Handle double-encoded JSON from PostgreSQL
        if (is_string($value)) {
            // First decode
            $decoded = json_decode($value, true);

            // If still string, decode again (double-encoded)
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    public function getFormattedPublishedAtAttribute()
    {
        return $this->published_at ? $this->published_at->format('d F Y') : null;
    }

    public function getReadingTimeTextAttribute()
    {
        return $this->reading_time.' min read';
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
        return static::getCategoryLabel($this->category);
    }

    /**
     * Get category label with translation support
     */
    public static function getCategoryLabel($category)
    {
        $key = "blog.category_labels.{$category}";
        $translated = __($key);

        // If translation not found, return fallback
        if ($translated === $key) {
            $fallbacks = [
                'general' => 'General',
                'news' => 'News',
                'case-study' => 'Case Study',
                'tips' => 'Tips & Guide',
                'regulation' => 'Regulation',
            ];

            return $fallbacks[$category] ?? ucfirst(str_replace('-', ' ', $category));
        }

        return $translated;
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
        return route('blog.article.id', $this->slug);
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
            ->where(function ($query) {
                $query->where('category', $this->category);

                if (! empty($this->tags)) {
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
