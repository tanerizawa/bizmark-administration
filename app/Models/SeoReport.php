<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoReport extends Model
{
    protected $fillable = [
        'period', 'period_start', 'period_end',
        'metrics', 'top_articles', 'alerts', 'emailed',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'metrics' => 'array',
        'top_articles' => 'array',
        'alerts' => 'array',
        'emailed' => 'boolean',
    ];

    public function scopeWeekly($query)
    {
        return $query->where('period', 'weekly');
    }

    public function scopeMonthly($query)
    {
        return $query->where('period', 'monthly');
    }
}
