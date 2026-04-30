<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentSyndication extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'platform',
        'platform_url',
        'status',
        'published_at',
        'metrics',
        'error_message',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'metrics' => 'array',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
