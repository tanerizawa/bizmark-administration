<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetaAbTest extends Model
{
    protected $fillable = [
        'article_id', 'test_type', 'variant_a_title', 'variant_a_description',
        'variant_b_title', 'variant_b_description', 'variant_a_impressions',
        'variant_a_clicks', 'variant_b_impressions', 'variant_b_clicks',
        'winner', 'confidence', 'status', 'started_at', 'ended_at',
    ];

    protected $casts = [
        'confidence' => 'float',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    public function getCtrAAttribute(): float
    {
        return $this->variant_a_impressions > 0
            ? round(($this->variant_a_clicks / $this->variant_a_impressions) * 100, 2)
            : 0;
    }

    public function getCtrBAttribute(): float
    {
        return $this->variant_b_impressions > 0
            ? round(($this->variant_b_clicks / $this->variant_b_impressions) * 100, 2)
            : 0;
    }
}
