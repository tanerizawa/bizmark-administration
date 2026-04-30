<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KbliPermitRecommendation extends Model
{
    protected $fillable = [
        'kbli_code',
        'business_scale',
        'location_type',
        'recommended_permits',
        'required_documents',
        'risk_assessment',
        'estimated_timeline',
        'additional_notes',
        'ai_model',
        'ai_prompt_hash',
        'confidence_score',
        'cache_hits',
        'last_used_at',
        'expires_at',
    ];

    protected $casts = [
        'recommended_permits' => 'array',
        'required_documents' => 'array',
        'risk_assessment' => 'array',
        'estimated_timeline' => 'array',
        'confidence_score' => 'decimal:2',
        'cache_hits' => 'integer',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the KBLI associated with this recommendation
     */
    public function kbli(): BelongsTo
    {
        return $this->belongsTo(Kbli::class, 'kbli_code', 'code');
    }

    /**
     * Get all permit applications using this recommendation
     */
    public function permitApplications(): HasMany
    {
        return $this->hasMany(PermitApplication::class, 'ai_recommendation_id');
    }

    /**
     * Increment cache hits counter
     */
    public function incrementCacheHits(): void
    {
        $this->increment('cache_hits');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Check if recommendation is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if recommendation needs refresh
     */
    public function needsRefresh(): bool
    {
        // Refresh if expired, low confidence, or old with low usage
        return $this->isExpired() ||
               ($this->confidence_score && $this->confidence_score < 0.7) ||
               ($this->cache_hits < 5 && $this->created_at < now()->subDays(90));
    }

    /**
     * Get total mandatory permits count
     */
    public function getMandatoryPermitsCountAttribute(): int
    {
        if (!$this->recommended_permits) {
            return 0;
        }
        
        return collect($this->recommended_permits)
            ->where('type', 'mandatory')
            ->count();
    }

    /**
     * Get total estimated cost range
     */
    public function getTotalCostRangeAttribute(): array
    {
        if (!$this->recommended_permits) {
            return ['min' => 0, 'max' => 0];
        }
        
        $permits = collect($this->recommended_permits);
        
        return [
            'min' => $permits->sum('estimated_cost_range.min'),
            'max' => $permits->sum('estimated_cost_range.max'),
        ];
    }

    /**
     * Scope to get active (non-expired) recommendations
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope to find by KBLI and context
     */
    public function scopeForContext($query, string $kbliCode, ?string $scale = null, ?string $location = null)
    {
        return $query->where('kbli_code', $kbliCode)
            ->where('business_scale', $scale)
            ->where('location_type', $location);
    }
}
