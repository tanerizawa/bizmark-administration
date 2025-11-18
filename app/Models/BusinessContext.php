<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessContext extends Model
{
    protected $fillable = [
        'client_id',
        'kbli_code',
        // Project Scale
        'land_area',
        'building_area',
        'number_of_floors',
        'investment_value',
        // Location Details
        'province',
        'city',
        'district',
        'zone_type',
        'coordinates',
        'location_category',
        // Business Details
        'number_of_employees',
        'production_capacity',
        'annual_revenue_target',
        'business_scale',
        // Environmental Factors
        'environmental_impact',
        'waste_management',
        'near_protected_area',
        'environmental_notes',
        // Legal Status
        'ownership_status',
        'existing_permits',
        'urgency_level',
        // Additional
        'additional_notes',
        'custom_fields',
        // Metadata
        'ip_address',
        'user_agent',
        'submitted_at',
    ];

    protected $casts = [
        'land_area' => 'decimal:2',
        'building_area' => 'decimal:2',
        'number_of_floors' => 'integer',
        'investment_value' => 'decimal:2',
        'number_of_employees' => 'integer',
        'annual_revenue_target' => 'decimal:2',
        'near_protected_area' => 'boolean',
        'existing_permits' => 'array',
        'custom_fields' => 'array',
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the client that owns this context
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the KBLI associated with this context
     */
    public function kbli(): BelongsTo
    {
        return $this->belongsTo(Kbli::class, 'kbli_code', 'code');
    }

    /**
     * Get the maximum project area (land or building)
     */
    public function getMaxAreaAttribute(): float
    {
        return max($this->land_area ?? 0, $this->building_area ?? 0);
    }

    /**
     * Get complexity level based on scale factors
     */
    public function getComplexityLevelAttribute(): string
    {
        $area = $this->max_area;
        $investment = $this->investment_value ?? 0;

        if ($area >= 5000 || $investment >= 100000000000) {
            return 'very_high';
        } elseif ($area >= 500 || $investment >= 10000000000) {
            return 'high';
        } elseif ($area >= 50 || $investment >= 1000000000) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Check if project is in a premium location
     */
    public function isPremiumLocationAttribute(): bool
    {
        $premiumCities = [
            'Jakarta', 'DKI Jakarta', 'Surabaya', 'Bandung', 'Medan',
            'Tangerang', 'Bekasi', 'Depok', 'Tangerang Selatan'
        ];

        foreach ($premiumCities as $city) {
            if (stripos($this->city ?? '', $city) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get formatted investment value
     */
    public function getFormattedInvestmentAttribute(): string
    {
        if (!$this->investment_value) {
            return 'Tidak disebutkan';
        }

        $billions = $this->investment_value / 1000000000;
        if ($billions >= 1) {
            return 'Rp ' . number_format($billions, 2, ',', '.') . ' Miliar';
        }

        $millions = $this->investment_value / 1000000;
        return 'Rp ' . number_format($millions, 2, ',', '.') . ' Juta';
    }

    /**
     * Get formatted area range
     */
    public function getFormattedAreaAttribute(): string
    {
        $parts = [];
        
        if ($this->land_area) {
            $parts[] = 'Tanah: ' . number_format($this->land_area, 0, ',', '.') . ' m²';
        }
        
        if ($this->building_area) {
            $parts[] = 'Bangunan: ' . number_format($this->building_area, 0, ',', '.') . ' m²';
        }

        return implode(' | ', $parts) ?: 'Tidak disebutkan';
    }

    /**
     * Get full location string
     */
    public function getFullLocationAttribute(): string
    {
        $parts = array_filter([
            $this->district,
            $this->city,
            $this->province,
        ]);

        return implode(', ', $parts) ?: 'Tidak disebutkan';
    }

    /**
     * Convert to array for fee calculation
     */
    public function toContextArray(): array
    {
        return [
            'land_area' => $this->land_area ?? 0,
            'building_area' => $this->building_area ?? 0,
            'investment_value' => $this->investment_value ?? 0,
            'city' => $this->city,
            'province' => $this->province,
            'location_type' => $this->location_category,
            'employees' => $this->number_of_employees ?? 0,
            'environmental_impact' => $this->environmental_impact ?? 'low',
            'urgency_level' => $this->urgency_level ?? 'standard',
            'business_scale' => $this->business_scale,
            'zone_type' => $this->zone_type,
            'complexity_level' => $this->complexity_level,
        ];
    }

    /**
     * Scope to get recent contexts
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('submitted_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to get by KBLI
     */
    public function scopeForKbli($query, string $kbliCode)
    {
        return $query->where('kbli_code', $kbliCode);
    }

    /**
     * Scope to get by client
     */
    public function scopeForClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope to get high investment projects
     */
    public function scopeHighInvestment($query, float $minInvestment = 10000000000)
    {
        return $query->where('investment_value', '>=', $minInvestment);
    }

    /**
     * Scope to get by environmental impact
     */
    public function scopeWithEnvironmentalImpact($query, string $level)
    {
        return $query->where('environmental_impact', $level);
    }
}
