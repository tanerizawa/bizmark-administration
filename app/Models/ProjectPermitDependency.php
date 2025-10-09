<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectPermitDependency extends Model
{
    public const TYPE_MANDATORY = 'MANDATORY';
    public const TYPE_OPTIONAL = 'OPTIONAL';

    protected $fillable = [
        'project_permit_id',
        'depends_on_permit_id',
        'dependency_type',
        'can_proceed_without',
        'override_reason',
        'override_document_path',
        'overridden_by_user_id',
        'overridden_at',
        'created_by_user_id',
    ];

    protected $casts = [
        'can_proceed_without' => 'boolean',
        'overridden_at' => 'datetime',
    ];

    /**
     * Get the project permit (child).
     */
    public function projectPermit(): BelongsTo
    {
        return $this->belongsTo(ProjectPermit::class, 'project_permit_id');
    }

    /**
     * Get the required permit (parent/prerequisite).
     */
    public function dependsOnPermit(): BelongsTo
    {
        return $this->belongsTo(ProjectPermit::class, 'depends_on_permit_id');
    }

    /**
     * Get the user who overrode this dependency.
     */
    public function overriddenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'overridden_by_user_id');
    }

    /**
     * Get the user who created this dependency.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Check if this is a mandatory dependency.
     */
    public function isMandatory(): bool
    {
        return $this->dependency_type === self::TYPE_MANDATORY;
    }

    /**
     * Check if this is an optional dependency.
     */
    public function isOptional(): bool
    {
        return $this->dependency_type === self::TYPE_OPTIONAL;
    }

    /**
     * Check if this dependency has been overridden.
     */
    public function isOverridden(): bool
    {
        return $this->can_proceed_without && $this->overridden_at !== null;
    }

    /**
     * Check if the prerequisite is completed.
     */
    public function isPrerequisiteCompleted(): bool
    {
        return in_array(
            $this->dependsOnPermit->status,
            [ProjectPermit::STATUS_APPROVED, ProjectPermit::STATUS_EXISTING],
            true
        );
    }
}
