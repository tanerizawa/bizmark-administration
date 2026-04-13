<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleViewLog extends Model
{
    protected $fillable = [
        'article_id', 'date', 'views', 'unique_views',
        'top_referrer', 'top_country',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function scopeForArticle($query, int $articleId)
    {
        return $query->where('article_id', $articleId);
    }

    public function scopeInRange($query, $start, $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }
}
