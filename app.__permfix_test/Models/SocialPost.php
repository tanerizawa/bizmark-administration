<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialPost extends Model
{
    protected $fillable = [
        'article_id', 'platform', 'caption', 'platform_post_id',
        'platform_url', 'status', 'posted_at', 'scheduled_for',
        'metrics', 'error_message',
    ];

    protected $casts = [
        'posted_at' => 'datetime',
        'scheduled_for' => 'datetime',
        'metrics' => 'array',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_for', '<=', now());
    }

    public function scopeForPlatform($query, string $platform)
    {
        return $query->where('platform', $platform);
    }
}
