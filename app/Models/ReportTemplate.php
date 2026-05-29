<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportTemplate extends Model
{
    protected $fillable = [
        'name',
        'type',
        'required_parameters',
        'template_content',
        'regulatory_basis',
        'is_active',
    ];

    protected $casts = [
        'required_parameters' => 'array',
        'is_active' => 'boolean',
    ];

    public function reports(): HasMany
    {
        return $this->hasMany(ComplianceReport::class, 'template_id');
    }

    public static array $typeLabels = [
        'ukl_upl_quarterly' => 'UKL-UPL Triwulanan',
        'ukl_upl_annual' => 'UKL-UPL Tahunan',
        'sparing' => 'SPARING',
        'custom' => 'Kustom',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::$typeLabels[$this->type] ?? ucfirst($this->type);
    }
}
