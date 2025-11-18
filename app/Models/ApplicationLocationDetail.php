<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationLocationDetail extends Model
{
    protected $fillable = [
        'application_id',
        'province',
        'city_regency',
        'district',
        'sub_district',
        'full_address',
        'postal_code',
        'latitude',
        'longitude',
        'zone_type',
        'land_status',
        'land_certificate_number',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Relationship to application
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(PermitApplication::class, 'application_id');
    }

    /**
     * Get full formatted address
     */
    public function getFormattedAddressAttribute(): string
    {
        $parts = array_filter([
            $this->full_address,
            $this->sub_district,
            $this->district,
            $this->city_regency,
            $this->province,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get Google Maps URL
     */
    public function getGoogleMapsUrlAttribute(): ?string
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }

        return null;
    }
}
