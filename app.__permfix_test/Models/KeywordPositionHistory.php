<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KeywordPositionHistory extends Model
{
    protected $table = 'keyword_position_history';

    protected $fillable = [
        'keyword',
        'our_url',
        'position',
        'previous_position',
        'position_change',
        'data_source',
        'top_competitors',
        'search_volume',
        'search_intent',
        'tracked_at',
    ];

    protected $casts = [
        'top_competitors' => 'array',
        'tracked_at' => 'date',
        'position' => 'integer',
        'previous_position' => 'integer',
        'position_change' => 'integer',
        'search_volume' => 'integer',
    ];

    /**
     * A position history can have multiple alerts.
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(RankingAlert::class, 'position_history_id');
    }

    /**
     * Check if this represents a ranking gain.
     */
    public function isGain(): bool
    {
        return $this->position_change > 0;
    }

    /**
     * Check if this represents a ranking drop.
     */
    public function isDrop(): bool
    {
        return $this->position_change < 0;
    }

    /**
     * Check if we're ranking for this keyword.
     */
    public function isRanking(): bool
    {
        return ! is_null($this->position) && $this->position > 0;
    }

    /**
     * Check if we're on page 1 (top 10).
     */
    public function isOnPageOne(): bool
    {
        return $this->position !== null && $this->position <= 10;
    }

    /**
     * Get the rank tier description.
     */
    public function getRankTierAttribute(): string
    {
        if (! $this->isRanking()) {
            return 'Not Ranking';
        }

        return match (true) {
            $this->position <= 3 => 'Top 3',
            $this->position <= 10 => 'Page 1',
            $this->position <= 20 => 'Page 2',
            $this->position <= 30 => 'Page 3',
            default => 'Beyond Page 3',
        };
    }

    /**
     * Get the change description with arrow.
     */
    public function getChangeDescriptionAttribute(): string
    {
        if ($this->position_change === 0) {
            return '→ No change';
        }

        $absChange = abs($this->position_change);

        if ($this->position_change > 0) {
            return "↑ +{$absChange} (improved)";
        }

        return "↓ -{$absChange} (dropped)";
    }

    /**
     * Human-readable data source label.
     */
    public function getDataSourceLabel(): string
    {
        return match ($this->data_source) {
            'searxng' => 'SearXNG',
            'google_serp' => 'Google SERP',
            'google_search_console' => 'Search Console',
            default => 'AI Estimate',
        };
    }

    // ─────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────

    /**
     * Filter by date range.
     */
    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('tracked_at', [$start, $end]);
    }

    /**
     * Filter to today's records.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tracked_at', now()->toDateString());
    }

    /**
     * Filter to last N days.
     */
    public function scopeLastDays($query, $days = 7)
    {
        return $query->where('tracked_at', '>=', now()->subDays($days)->toDateString());
    }

    /**
     * Filter to records with ranking drops.
     */
    public function scopeDrops($query)
    {
        return $query->where('position_change', '<', 0);
    }

    /**
     * Filter to records with ranking gains.
     */
    public function scopeGains($query)
    {
        return $query->where('position_change', '>', 0);
    }

    /**
     * Filter to records on page 1.
     */
    public function scopeOnPageOne($query)
    {
        return $query->where('position', '<=', 10);
    }

    /**
     * Filter to records with significant changes (>3 positions).
     */
    public function scopeSignificantChanges($query, $threshold = 3)
    {
        return $query->where(function ($q) use ($threshold) {
            $q->where('position_change', '>=', $threshold)
                ->orWhere('position_change', '<=', -$threshold);
        });
    }

    /**
     * Filter by specific keyword.
     */
    public function scopeForKeyword($query, $keyword)
    {
        return $query->where('keyword', $keyword);
    }

    /**
     * Get the latest record for each keyword.
     */
    public function scopeLatestPerKeyword($query)
    {
        return $query->whereIn('id', function ($sub) {
            $sub->selectRaw('MAX(id)')
                ->from('keyword_position_history')
                ->groupBy('keyword');
        });
    }

    // ─────────────────────────────────────────
    // STATIC METHODS
    // ─────────────────────────────────────────

    /**
     * Get trend data for a specific keyword over time.
     */
    public static function getTrendFor(string $keyword, int $days = 30): array
    {
        return static::forKeyword($keyword)
            ->lastDays($days)
            ->orderBy('tracked_at')
            ->get(['tracked_at', 'position', 'position_change'])
            ->map(fn ($r) => [
                'date' => $r->tracked_at->format('Y-m-d'),
                'position' => $r->position,
                'change' => $r->position_change,
            ])
            ->toArray();
    }

    /**
     * Get summary statistics for dashboard.
     */
    public static function getDashboardStats(): array
    {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        $todayData = static::whereDate('tracked_at', $today)->get();
        $yesterdayData = static::whereDate('tracked_at', $yesterday)->get();

        // Count keywords by tier
        $tiers = [
            'top3' => $todayData->where('position', '<=', 3)->count(),
            'page1' => $todayData->whereBetween('position', [4, 10])->count(),
            'page2' => $todayData->whereBetween('position', [11, 20])->count(),
            'page3Plus' => $todayData->where('position', '>', 20)->count(),
            'notRanking' => $todayData->whereNull('position')->count(),
        ];

        // Changes summary
        $todayChanges = [
            'gains' => $todayData->where('position_change', '>', 0)->count(),
            'drops' => $todayData->where('position_change', '<', 0)->count(),
            'stable' => $todayData->where('position_change', 0)->count(),
            'avgChange' => round($todayData->avg('position_change') ?? 0, 2),
        ];

        // Big movers (>5 position change)
        $bigMovers = $todayData->filter(fn ($r) => abs($r->position_change) >= 5)
            ->sortByDesc(fn ($r) => abs($r->position_change))
            ->take(10)
            ->values()
            ->toArray();

        return [
            'tracked_keywords' => $todayData->count(),
            'yesterday_keywords' => $yesterdayData->count(),
            'tiers' => $tiers,
            'changes' => $todayChanges,
            'big_movers' => $bigMovers,
            'last_tracked' => static::latest()->first()?->tracked_at?->toDateTimeString(),
        ];
    }

    /**
     * Get keywords at risk (dropped 3+ positions or fell off page 1).
     */
    public static function getAtRiskKeywords(int $days = 7): array
    {
        return static::lastDays($days)
            ->latestPerKeyword()
            ->where(function ($q) {
                // Dropped 3+ positions
                $q->where('position_change', '<=', -3)
                  // OR fell off page 1
                    ->orWhere(function ($q2) {
                        $q2->whereNotNull('previous_position')
                            ->where('previous_position', '<=', 10)
                            ->where('position', '>', 10);
                    });
            })
            ->orderBy('position_change')
            ->limit(20)
            ->get()
            ->toArray();
    }
}
