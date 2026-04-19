<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermitTemplateDependency extends Model
{
    protected $fillable = [
        'template_id',
        'permit_item_id',
        'depends_on_item_id',
        'dependency_type',
        'notes',
    ];

    /**
     * Get the template this dependency belongs to.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(PermitTemplate::class, 'template_id');
    }

    /**
     * Get the permit item (child).
     */
    public function permitItem(): BelongsTo
    {
        return $this->belongsTo(PermitTemplateItem::class, 'permit_item_id');
    }

    /**
     * Get the required permit item (parent/prerequisite).
     */
    public function dependsOnItem(): BelongsTo
    {
        return $this->belongsTo(PermitTemplateItem::class, 'depends_on_item_id');
    }

    /**
     * Get the permit type that this depends on (via item).
     */
    public function dependsOnPermitType()
    {
        return $this->hasOneThrough(
            PermitType::class,
            PermitTemplateItem::class,
            'id', // Foreign key on permit_template_items
            'id', // Foreign key on permit_types
            'depends_on_item_id', // Local key on this model
            'permit_type_id' // Local key on permit_template_items
        );
    }

    /**
     * Check if this is a mandatory dependency.
     */
    public function isMandatory(): bool
    {
        return $this->dependency_type === 'MANDATORY';
    }

    /**
     * Check if this is an optional dependency.
     */
    public function isOptional(): bool
    {
        return $this->dependency_type === 'OPTIONAL';
    }

    /**
     * Get can_proceed_without attribute for backward compatibility.
     */
    public function getCanProceedWithoutAttribute(): bool
    {
        return $this->dependency_type === 'OPTIONAL';
    }
}
