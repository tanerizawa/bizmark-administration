<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RankingAlert extends Model
{
    protected $table = 'ranking_alerts';

    protected $fillable = [
        'position_history_id',
        'keyword',
        'alert_type',
        'severity',
        'old_position',
        'new_position',
        'message',
        'details',
        'is_read',
        'is_actioned',
        'actioned_at',
    ];

    protected $casts = [
        'details' => 'array',
        'is_read' => 'boolean',
        'is_actioned' => 'boolean',
        'severity' => 'integer',
        'old_position' => 'integer',
        'new_position' => 'integer',
        'actioned_at' => 'datetime',
    ];

    // Alert types
    const TYPE_RANKING_DROP = 'ranking_drop';

    const TYPE_RANKING_GAIN = 'ranking_gain';

    const TYPE_NEW_RANKING = 'new_ranking';

    const TYPE_LOST_RANKING = 'lost_ranking';

    const TYPE_PAGE_ONE_LOST = 'page_one_lost';

    const TYPE_PAGE_ONE_GAINED = 'page_one_gained';

    const TYPE_TOP3_ACHIEVED = 'top3_achieved';

    // Severity levels
    const SEVERITY_INFO = 1;

    const SEVERITY_WARNING = 2;

    const SEVERITY_CRITICAL = 3;

    /**
     * The position history this alert belongs to.
     */
    public function positionHistory(): BelongsTo
    {
        return $this->belongsTo(KeywordPositionHistory::class, 'position_history_id');
    }

    /**
     * Get severity label with color.
     */
    public function getSeverityLabelAttribute(): string
    {
        return match ($this->severity) {
            self::SEVERITY_INFO => 'Info',
            self::SEVERITY_WARNING => 'Warning',
            self::SEVERITY_CRITICAL => 'Critical',
            default => 'Unknown',
        };
    }

    /**
     * Get severity color class.
     */
    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity) {
            self::SEVERITY_INFO => 'blue',
            self::SEVERITY_WARNING => 'yellow',
            self::SEVERITY_CRITICAL => 'red',
            default => 'gray',
        };
    }

    /**
     * Get alert type icon.
     */
    public function getAlertIconAttribute(): string
    {
        return match ($this->alert_type) {
            self::TYPE_RANKING_DROP => '↓',
            self::TYPE_RANKING_GAIN => '↑',
            self::TYPE_NEW_RANKING => '★',
            self::TYPE_LOST_RANKING => '✕',
            self::TYPE_PAGE_ONE_LOST => '⚠',
            self::TYPE_PAGE_ONE_GAINED => '✓',
            self::TYPE_TOP3_ACHIEVED => '🏆',
            default => '•',
        };
    }

    /**
     * Get human-readable alert type.
     */
    public function getAlertTypeLabelAttribute(): string
    {
        return match ($this->alert_type) {
            self::TYPE_RANKING_DROP => 'Ranking Dropped',
            self::TYPE_RANKING_GAIN => 'Ranking Improved',
            self::TYPE_NEW_RANKING => 'New Ranking',
            self::TYPE_LOST_RANKING => 'Lost Ranking',
            self::TYPE_PAGE_ONE_LOST => 'Fell Off Page 1',
            self::TYPE_PAGE_ONE_GAINED => 'Reached Page 1',
            self::TYPE_TOP3_ACHIEVED => 'Top 3 Achieved',
            default => 'Unknown',
        };
    }

    /**
     * Mark alert as read.
     */
    public function markAsRead(): bool
    {
        return $this->update(['is_read' => true]);
    }

    /**
     * Mark alert as actioned with timestamp.
     */
    public function markAsActioned(): bool
    {
        return $this->update([
            'is_actioned' => true,
            'actioned_at' => now(),
        ]);
    }

    // ─────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopePending($query)
    {
        return $query->where('is_actioned', false);
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', self::SEVERITY_CRITICAL);
    }

    public function scopeWarnings($query)
    {
        return $query->where('severity', self::SEVERITY_WARNING);
    }

    public function scopeDrops($query)
    {
        return $query->whereIn('alert_type', [
            self::TYPE_RANKING_DROP,
            self::TYPE_LOST_RANKING,
            self::TYPE_PAGE_ONE_LOST,
        ]);
    }

    public function scopeGains($query)
    {
        return $query->whereIn('alert_type', [
            self::TYPE_RANKING_GAIN,
            self::TYPE_NEW_RANKING,
            self::TYPE_PAGE_ONE_GAINED,
            self::TYPE_TOP3_ACHIEVED,
        ]);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // ─────────────────────────────────────────
    // STATIC FACTORY METHODS
    // ─────────────────────────────────────────

    /**
     * Create a ranking drop alert.
     */
    public static function createDropAlert(KeywordPositionHistory $history): ?self
    {
        $change = abs($history->position_change);

        // Determine severity based on drop magnitude
        $severity = match (true) {
            $change >= 10 => self::SEVERITY_CRITICAL,
            $change >= 5 => self::SEVERITY_WARNING,
            default => self::SEVERITY_INFO,
        };

        // Was on page 1 but now isn't
        if ($history->previous_position <= 10 && $history->position > 10) {
            return self::create([
                'position_history_id' => $history->id,
                'keyword' => $history->keyword,
                'alert_type' => self::TYPE_PAGE_ONE_LOST,
                'severity' => self::SEVERITY_CRITICAL,
                'old_position' => $history->previous_position,
                'new_position' => $history->position,
                'message' => "'{$history->keyword}' dropped off page 1 (#{$history->previous_position} → #{$history->position})",
                'details' => [
                    'change' => $history->position_change,
                    'url' => $history->our_url,
                    'competitors' => $history->top_competitors,
                ],
            ]);
        }

        // Only alert for significant drops (3+ positions)
        if ($change < 3) {
            return null;
        }

        return self::create([
            'position_history_id' => $history->id,
            'keyword' => $history->keyword,
            'alert_type' => self::TYPE_RANKING_DROP,
            'severity' => $severity,
            'old_position' => $history->previous_position,
            'new_position' => $history->position,
            'message' => "'{$history->keyword}' dropped {$change} positions (#{$history->previous_position} → #{$history->position})",
            'details' => [
                'change' => $history->position_change,
                'url' => $history->our_url,
            ],
        ]);
    }

    /**
     * Create a ranking gain alert.
     */
    public static function createGainAlert(KeywordPositionHistory $history): ?self
    {
        $change = $history->position_change;

        // Top 3 achieved
        if ($history->position <= 3 && ($history->previous_position > 3 || is_null($history->previous_position))) {
            return self::create([
                'position_history_id' => $history->id,
                'keyword' => $history->keyword,
                'alert_type' => self::TYPE_TOP3_ACHIEVED,
                'severity' => self::SEVERITY_INFO,
                'old_position' => $history->previous_position,
                'new_position' => $history->position,
                'message' => "🏆 '{$history->keyword}' reached Top 3! (#{$history->position})",
                'details' => [
                    'change' => $change,
                    'url' => $history->our_url,
                ],
            ]);
        }

        // Page 1 achieved
        if ($history->position <= 10 && ($history->previous_position > 10 || is_null($history->previous_position))) {
            return self::create([
                'position_history_id' => $history->id,
                'keyword' => $history->keyword,
                'alert_type' => self::TYPE_PAGE_ONE_GAINED,
                'severity' => self::SEVERITY_INFO,
                'old_position' => $history->previous_position,
                'new_position' => $history->position,
                'message' => "'{$history->keyword}' reached page 1! (#{$history->position})",
                'details' => [
                    'change' => $change,
                    'url' => $history->our_url,
                ],
            ]);
        }

        // Only alert for significant gains (5+ positions)
        if ($change < 5) {
            return null;
        }

        return self::create([
            'position_history_id' => $history->id,
            'keyword' => $history->keyword,
            'alert_type' => self::TYPE_RANKING_GAIN,
            'severity' => self::SEVERITY_INFO,
            'old_position' => $history->previous_position,
            'new_position' => $history->position,
            'message' => "'{$history->keyword}' improved {$change} positions! (#{$history->previous_position} → #{$history->position})",
            'details' => [
                'change' => $change,
                'url' => $history->our_url,
            ],
        ]);
    }

    /**
     * Create a new ranking alert (first time ranking).
     */
    public static function createNewRankingAlert(KeywordPositionHistory $history): self
    {
        $severity = $history->position <= 10 ? self::SEVERITY_INFO : self::SEVERITY_INFO;

        return self::create([
            'position_history_id' => $history->id,
            'keyword' => $history->keyword,
            'alert_type' => self::TYPE_NEW_RANKING,
            'severity' => $severity,
            'old_position' => null,
            'new_position' => $history->position,
            'message' => "'{$history->keyword}' started ranking at #{$history->position}",
            'details' => [
                'url' => $history->our_url,
            ],
        ]);
    }

    /**
     * Create a lost ranking alert (no longer ranking).
     */
    public static function createLostRankingAlert(KeywordPositionHistory $history): self
    {
        return self::create([
            'position_history_id' => $history->id,
            'keyword' => $history->keyword,
            'alert_type' => self::TYPE_LOST_RANKING,
            'severity' => self::SEVERITY_WARNING,
            'old_position' => $history->previous_position,
            'new_position' => null,
            'message' => "'{$history->keyword}' lost ranking (was #{$history->previous_position})",
            'details' => [
                'last_url' => $history->our_url,
            ],
        ]);
    }

    /**
     * Get dashboard summary of alerts.
     */
    public static function getDashboardSummary(): array
    {
        $recent = static::recent(7)->get();

        return [
            'total' => $recent->count(),
            'unread' => static::unread()->count(),
            'critical' => $recent->where('severity', self::SEVERITY_CRITICAL)->count(),
            'warnings' => $recent->where('severity', self::SEVERITY_WARNING)->count(),
            'drops' => $recent->whereIn('alert_type', [
                self::TYPE_RANKING_DROP,
                self::TYPE_LOST_RANKING,
                self::TYPE_PAGE_ONE_LOST,
            ])->count(),
            'gains' => $recent->whereIn('alert_type', [
                self::TYPE_RANKING_GAIN,
                self::TYPE_NEW_RANKING,
                self::TYPE_PAGE_ONE_GAINED,
                self::TYPE_TOP3_ACHIEVED,
            ])->count(),
            'recent_critical' => static::critical()
                ->unread()
                ->latest()
                ->limit(5)
                ->get()
                ->toArray(),
        ];
    }
}
