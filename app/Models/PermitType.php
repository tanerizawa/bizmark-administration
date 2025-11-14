<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermitType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'category',
        'institution_id',
        'avg_processing_days',
        'description',
        'required_documents',
        'estimated_cost_min',
        'estimated_cost_max',
        'is_active',
    ];

    protected $casts = [
        'required_documents' => 'array',
        'estimated_cost_min' => 'decimal:2',
        'estimated_cost_max' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the institution that issues this permit type.
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Get template items using this permit type.
     */
    public function templateItems(): HasMany
    {
        return $this->hasMany(PermitTemplateItem::class);
    }

    /**
     * Get project permits using this permit type.
     */
    public function projectPermits(): HasMany
    {
        return $this->hasMany(ProjectPermit::class);
    }

    /**
     * Get applications for this permit type.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(PermitApplication::class, 'permit_type_id');
    }

    /**
     * Get the full name with category.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->name} ({$this->category})";
    }

    /**
     * Get estimated cost range as formatted string.
     */
    public function getEstimatedCostRangeAttribute(): ?string
    {
        if ($this->estimated_cost_min && $this->estimated_cost_max) {
            return 'Rp ' . number_format($this->estimated_cost_min, 0, ',', '.') . 
                   ' - Rp ' . number_format($this->estimated_cost_max, 0, ',', '.');
        }
        return null;
    }

    /**
     * Scope to filter by category.
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter active permit types only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
