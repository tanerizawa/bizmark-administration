<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchConsoleData extends Model
{
    protected $table = 'search_console_data';

    protected $fillable = [
        'page_url', 'query', 'date', 'clicks', 'impressions', 'ctr', 'position',
    ];

    protected $casts = [
        'date' => 'date',
        'ctr' => 'float',
        'position' => 'float',
    ];

    public function scopeForPage($query, string $url)
    {
        return $query->where('page_url', $url);
    }

    public function scopeInRange($query, $start, $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }

    public function scopeTopQueries($query, int $limit = 20)
    {
        return $query->selectRaw('query, SUM(clicks) as total_clicks, SUM(impressions) as total_impressions, AVG(position) as avg_position')
            ->groupBy('query')
            ->orderByDesc('total_clicks')
            ->limit($limit);
    }
}
