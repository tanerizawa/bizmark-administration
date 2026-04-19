<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermitTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'use_case',
        'category',
        'created_by_user_id',
        'is_public',
        'usage_count',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'usage_count' => 'integer',
    ];

    /**
     * Get the user who created this template.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Get the items in this template.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PermitTemplateItem::class, 'template_id')->orderBy('sequence_order');
    }

    /**
     * Get the dependencies in this template.
     */
    public function dependencies(): HasMany
    {
        return $this->hasMany(PermitTemplateDependency::class, 'template_id');
    }

    /**
     * Increment usage count.
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Get goal permit (izin target akhir).
     */
    public function getGoalPermit()
    {
        return $this->items()->where('is_goal_permit', true)->first();
    }

    /**
     * Scope to filter public templates.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
