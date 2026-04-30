<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleTopicSimilarity extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'topic_a_id',
        'topic_b_id',
        'similarity_score',
        'calculated_at',
    ];

    protected $casts = [
        'similarity_score' => 'float',
        'calculated_at' => 'datetime',
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($similarity) {
            if (empty($similarity->calculated_at)) {
                $similarity->calculated_at = now();
            }
        });
    }

    /**
     * Relationships
     */
    public function topicA()
    {
        return $this->belongsTo(ArticleTopic::class, 'topic_a_id');
    }

    public function topicB()
    {
        return $this->belongsTo(ArticleTopic::class, 'topic_b_id');
    }

    /**
     * Scopes
     */
    public function scopeHighSimilarity($query, $threshold = 0.75)
    {
        return $query->where('similarity_score', '>=', $threshold);
    }

    public function scopeForTopic($query, $topicId)
    {
        return $query->where('topic_a_id', $topicId)
            ->orWhere('topic_b_id', $topicId);
    }

    /**
     * Helper Methods
     */
    public function isHighSimilarity($threshold = 0.75)
    {
        return $this->similarity_score >= $threshold;
    }

    public static function findSimilarity($topicAId, $topicBId)
    {
        return static::where(function ($query) use ($topicAId, $topicBId) {
            $query->where('topic_a_id', $topicAId)
                ->where('topic_b_id', $topicBId);
        })->orWhere(function ($query) use ($topicAId, $topicBId) {
            $query->where('topic_a_id', $topicBId)
                ->where('topic_b_id', $topicAId);
        })->first();
    }
}
