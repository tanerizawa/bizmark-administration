<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegulatoryChange extends Model
{
    protected $fillable = [
        'source_url',
        'document_number',
        'title',
        'published_at',
        'summary_id',
        'summary_en',
        'affected_service_categories',
        'relevance_score',
        'notified',
        'document_hash',
    ];

    protected $casts = [
        'published_at' => 'date',
        'affected_service_categories' => 'array',
        'relevance_score' => 'float',
        'notified' => 'boolean',
    ];

    public function isHighRelevance(): bool
    {
        return $this->relevance_score >= 0.7;
    }

    public function scopeUnnotified($query)
    {
        return $query->where('notified', false)->where('relevance_score', '>=', 0.3);
    }

    public function scopeRelevant($query, float $threshold = 0.3)
    {
        return $query->where('relevance_score', '>=', $threshold);
    }
}
