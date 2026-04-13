<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopicCluster extends Model
{
    protected $fillable = [
        'pillar_title',
        'pillar_slug',
        'pillar_description',
        'service_slug',
        'language',
        'subtopics',
        'article_ids',
        'keyword_cluster_ids',
        'status',
        'internal_links_built',
    ];

    protected $casts = [
        'subtopics' => 'array',
        'article_ids' => 'array',
        'keyword_cluster_ids' => 'array',
        'internal_links_built' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getArticles()
    {
        if (empty($this->article_ids)) {
            return collect();
        }
        return \App\Models\Article::whereIn('id', $this->article_ids)->published()->get();
    }

    public function getKeywordClusters()
    {
        if (empty($this->keyword_cluster_ids)) {
            return collect();
        }
        return KeywordCluster::whereIn('id', $this->keyword_cluster_ids)->active()->get();
    }
}
