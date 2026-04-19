<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermitTemplateItem extends Model
{
    protected $fillable = [
        'template_id',
        'permit_type_id',
        'custom_permit_name',
        'sequence_order',
        'is_goal_permit',
        'estimated_days',
        'estimated_cost',
        'notes',
    ];

    protected $casts = [
        'is_goal_permit' => 'boolean',
        'sequence_order' => 'integer',
        'estimated_days' => 'integer',
        'estimated_cost' => 'decimal:2',
    ];

    /**
     * Get the template this item belongs to.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(PermitTemplate::class, 'template_id');
    }

    /**
     * Get the permit type (if not custom).
     */
    public function permitType(): BelongsTo
    {
        return $this->belongsTo(PermitType::class);
    }

    /**
     * Get dependencies where this item is the child.
     */
    public function dependencies(): HasMany
    {
        return $this->hasMany(PermitTemplateDependency::class, 'permit_item_id');
    }

    /**
     * Get dependencies where this item is required by others.
     */
    public function dependents(): HasMany
    {
        return $this->hasMany(PermitTemplateDependency::class, 'depends_on_item_id');
    }

    /**
     * Get the permit name (custom or from permit type).
     */
    public function getNameAttribute(): string
    {
        return $this->custom_permit_name ?? $this->permitType?->name ?? 'Unnamed Permit';
    }

    /**
     * Get formatted estimated cost.
     */
    public function getFormattedCostAttribute(): ?string
    {
        return $this->estimated_cost 
            ? 'Rp ' . number_format($this->estimated_cost, 0, ',', '.')
            : null;
    }
}
