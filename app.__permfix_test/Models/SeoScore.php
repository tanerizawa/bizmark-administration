<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoScore extends Model
{
    protected $fillable = [
        'article_id', 'total_score', 'factors',
        'recommendations', 'scored_at',
    ];

    protected $casts = [
        'factors' => 'array',
        'recommendations' => 'array',
        'scored_at' => 'datetime',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function scopeExcellent($query)
    {
        return $query->where('total_score', '>=', 80);
    }

    public function scopeNeedsWork($query)
    {
        return $query->where('total_score', '<', 60);
    }

    public function getGradeAttribute(): string
    {
        return match (true) {
            $this->total_score >= 90 => 'A+',
            $this->total_score >= 80 => 'A',
            $this->total_score >= 70 => 'B',
            $this->total_score >= 60 => 'C',
            $this->total_score >= 50 => 'D',
            default => 'F',
        };
    }
}
