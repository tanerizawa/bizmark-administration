<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BetaTesterActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'beta_tester_id',
        'activity_type',
        'activity_description',
        'activity_data',
        'ip_address',
        'user_agent',
        'referrer',
    ];

    protected $casts = [
        'activity_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship dengan beta tester
     */
    public function betaTester(): BelongsTo
    {
        return $this->belongsTo(BetaTester::class);
    }

    /**
     * Get activity icon
     */
    public function getActivityIconAttribute(): string
    {
        return match($this->activity_type) {
            'registration' => 'user-plus',
            'document_view' => 'eye',
            'document_signed' => 'pen',
            'document_verified' => 'check-circle',
            'status_change' => 'arrow-right',
            'approved' => 'check',
            'rejected' => 'x',
            'login' => 'log-in',
            'logout' => 'log-out',
            'profile_update' => 'edit',
            default => 'activity'
        };
    }

    /**
     * Get activity color
     */
    public function getActivityColorAttribute(): string
    {
        return match($this->activity_type) {
            'registration' => 'blue',
            'document_view' => 'gray',
            'document_signed' => 'green',
            'document_verified' => 'green',
            'status_change' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'login' => 'blue',
            'logout' => 'gray',
            'profile_update' => 'yellow',
            default => 'gray'
        };
    }

    /**
     * Get formatted time ago
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get formatted created at
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->isoFormat('DD MMMM YYYY, HH:mm') . ' WIB';
    }

    /**
     * Parse browser from user agent
     */
    public function getBrowserAttribute(): string
    {
        if (!$this->user_agent) {
            return 'Unknown';
        }

        $ua = $this->user_agent;
        
        if (preg_match('/MSIE/i', $ua) || preg_match('/Trident/i', $ua)) {
            return 'Internet Explorer';
        } elseif (preg_match('/Edge/i', $ua)) {
            return 'Microsoft Edge';
        } elseif (preg_match('/Chrome/i', $ua)) {
            return 'Google Chrome';
        } elseif (preg_match('/Safari/i', $ua)) {
            return 'Safari';
        } elseif (preg_match('/Firefox/i', $ua)) {
            return 'Mozilla Firefox';
        } elseif (preg_match('/Opera/i', $ua)) {
            return 'Opera';
        }
        
        return 'Unknown Browser';
    }

    /**
     * Get device type
     */
    public function getDeviceTypeAttribute(): string
    {
        if (!$this->user_agent) {
            return 'Unknown';
        }

        $ua = strtolower($this->user_agent);
        
        if (preg_match('/(android|webos|iphone|ipad|ipod|blackberry|windows phone)/i', $ua)) {
            return 'Mobile';
        } elseif (preg_match('/(tablet|ipad)/i', $ua)) {
            return 'Tablet';
        }
        
        return 'Desktop';
    }

    /**
     * Scope: By activity type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Scope: Recent activities (last 30 days)
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subDays(30))
                     ->orderBy('created_at', 'desc');
    }

    /**
     * Scope: Today's activities
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today())
                     ->orderBy('created_at', 'desc');
    }

    /**
     * Scope: This week's activities
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                     ->orderBy('created_at', 'desc');
    }

    /**
     * Scope: This month's activities
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year)
                     ->orderBy('created_at', 'desc');
    }
}
