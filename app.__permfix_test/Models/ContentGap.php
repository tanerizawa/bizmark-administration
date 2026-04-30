<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentGap extends Model
{
    protected $fillable = [
        'suggested_title',
        'suggested_slug',
        'description',
        'target_keyword',
        'search_intent',
        'category',
        'service_slug',
        'language',
        'priority',
        'status',
        'article_topic_id',
        'topic_cluster_id',
    ];

    protected $casts = [
        'priority' => 'integer',
    ];

    public function articleTopic()
    {
        return $this->belongsTo(ArticleTopic::class);
    }

    public function topicCluster()
    {
        return $this->belongsTo(TopicCluster::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', '>=', 70)->orderByDesc('priority');
    }
}
