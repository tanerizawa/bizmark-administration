<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentRefreshLog extends Model
{
    protected $fillable = [
        'article_id', 'status', 'changes', 'before_snapshot', 'after_snapshot',
        'error_message', 'triggered_by', 'ai_tokens_used',
    ];

    protected $casts = [
        'changes' => 'array',
        'before_snapshot' => 'array',
        'after_snapshot' => 'array',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'refreshed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'error');
    }
}
