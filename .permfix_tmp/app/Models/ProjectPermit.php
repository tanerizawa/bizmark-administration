<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectPermit extends Model
{
    public const STATUS_NOT_STARTED = 'NOT_STARTED';
    public const STATUS_IN_PROGRESS = 'IN_PROGRESS';
    public const STATUS_WAITING_DOC = 'WAITING_DOC';
    public const STATUS_SUBMITTED = 'SUBMITTED';
    public const STATUS_UNDER_REVIEW = 'UNDER_REVIEW';
    public const STATUS_APPROVED = 'APPROVED';
    public const STATUS_REJECTED = 'REJECTED';
    public const STATUS_EXISTING = 'EXISTING';
    public const STATUS_CANCELLED = 'CANCELLED';

    public const STATUSES = [
        self::STATUS_NOT_STARTED,
        self::STATUS_IN_PROGRESS,
        self::STATUS_WAITING_DOC,
        self::STATUS_SUBMITTED,
        self::STATUS_UNDER_REVIEW,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
        self::STATUS_EXISTING,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'project_id',
        'permit_type_id',
        'custom_permit_name',
        'custom_institution_name',
        'sequence_order',
        'is_goal_permit',
        'status',
        'has_existing_document',
        'existing_document_id',
        'assigned_to_user_id',
        'started_at',
        'submitted_at',
        'approved_at',
        'rejected_at',
        'target_date',
        'estimated_cost',
        'actual_cost',
        'permit_number',
        'valid_until',
        'notes',
        'override_dependencies',
        'override_reason',
        'override_by_user_id',
        'override_at',
    ];

    protected $casts = [
        'sequence_order' => 'integer',
        'is_goal_permit' => 'boolean',
        'has_existing_document' => 'boolean',
        'override_dependencies' => 'boolean',
        'override_by_user_id' => 'integer',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'override_at' => 'datetime',
        'target_date' => 'date',
        'valid_until' => 'date',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
    ];

    /**
     * Get the project this permit belongs to.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the permit type (if not custom).
     */
    public function permitType(): BelongsTo
    {
        return $this->belongsTo(PermitType::class);
    }

    /**
     * Get the existing document.
     */
    public function existingDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'existing_document_id');
    }

    /**
     * Get the assigned user.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * Get dependencies where this permit is the child.
     */
    public function dependencies(): HasMany
    {
        return $this->hasMany(ProjectPermitDependency::class, 'project_permit_id');
    }

    /**
     * Get dependencies where this permit is required by others.
     */
    public function dependents(): HasMany
    {
        return $this->hasMany(ProjectPermitDependency::class, 'depends_on_permit_id');
    }

    /**
     * Get documents attached to this permit.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(PermitDocument::class);
    }

    /**
     * Get tasks related to this permit.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'project_permit_id');
    }

    /**
     * Get the permit name (custom or from permit type).
     */
    public function getNameAttribute(): string
    {
        return $this->custom_permit_name ?? $this->permitType?->name ?? 'Unnamed Permit';
    }

    /**
     * Get the institution name.
     */
    public function getInstitutionNameAttribute(): ?string
    {
        return $this->custom_institution_name ?? $this->permitType?->institution?->name;
    }

    /**
     * Check if permit can be started (all mandatory dependencies completed).
     */
    public function canStart(): bool
    {
        if ($this->status !== self::STATUS_NOT_STARTED) {
            return false;
        }

        $mandatoryDeps = $this->dependencies()
            ->where('dependency_type', ProjectPermitDependency::TYPE_MANDATORY)
            ->where('can_proceed_without', false)
            ->with('dependsOnPermit')
            ->get();

        foreach ($mandatoryDeps as $dep) {
            $requiredPermit = $dep->dependsOnPermit;
            if (!in_array($requiredPermit->status, [self::STATUS_APPROVED, self::STATUS_EXISTING], true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get blockers (dependencies that prevent this permit from starting).
     */
    public function getBlockers(): array
    {
        $blockers = [];
        
        $mandatoryDeps = $this->dependencies()
            ->where('dependency_type', ProjectPermitDependency::TYPE_MANDATORY)
            ->where('can_proceed_without', false)
            ->with('dependsOnPermit.permitType')
            ->get();

        foreach ($mandatoryDeps as $dep) {
            $requiredPermit = $dep->dependsOnPermit;
            if ($requiredPermit && !in_array($requiredPermit->status, [self::STATUS_APPROVED, self::STATUS_EXISTING], true)) {
                // Return permit name as string (consistent with Task model)
                $permitName = $requiredPermit->permitType
                    ? $requiredPermit->permitType->name
                    : ($requiredPermit->custom_permit_name ?? 'Permit #' . $requiredPermit->id);
                $blockers[] = $permitName;
            }
        }

        return $blockers;
    }

    /**
     * Check if permit is completed.
     */
    public function isCompleted(): bool
    {
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_EXISTING], true);
    }

    /**
     * Check if permit is in progress.
     */
    public function isInProgress(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_IN_PROGRESS,
                self::STATUS_WAITING_DOC,
                self::STATUS_SUBMITTED,
                self::STATUS_UNDER_REVIEW,
            ],
            true
        );
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_APPROVED, self::STATUS_EXISTING => 'green',
            self::STATUS_IN_PROGRESS, self::STATUS_SUBMITTED, self::STATUS_UNDER_REVIEW => 'blue',
            self::STATUS_WAITING_DOC => 'orange',
            self::STATUS_REJECTED, self::STATUS_CANCELLED => 'red',
            default => 'gray',
        };
    }

    /**
     * Get status label in Indonesian.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_NOT_STARTED => 'Belum Dimulai',
            self::STATUS_IN_PROGRESS => 'Sedang Diproses',
            self::STATUS_WAITING_DOC => 'Menunggu Dokumen',
            self::STATUS_SUBMITTED => 'Sudah Diajukan',
            self::STATUS_UNDER_REVIEW => 'Sedang Direview',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_EXISTING => 'Sudah Ada',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => $this->status,
        };
    }

    /**
     * Get formatted costs.
     */
    public function getFormattedEstimatedCostAttribute(): ?string
    {
        return $this->estimated_cost 
            ? 'Rp ' . number_format($this->estimated_cost, 0, ',', '.')
            : null;
    }

    public function getFormattedActualCostAttribute(): ?string
    {
        return $this->actual_cost 
            ? 'Rp ' . number_format($this->actual_cost, 0, ',', '.')
            : null;
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter completed permits.
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status', [self::STATUS_APPROVED, self::STATUS_EXISTING]);
    }

    /**
     * Scope to filter in progress permits.
     */
    public function scopeInProgress($query)
    {
        return $query->whereIn(
            'status',
            [
                self::STATUS_IN_PROGRESS,
                self::STATUS_WAITING_DOC,
                self::STATUS_SUBMITTED,
                self::STATUS_UNDER_REVIEW,
            ]
        );
    }

    /**
     * Scope to get goal permit.
     */
    public function scopeGoal($query)
    {
        return $query->where('is_goal_permit', true);
    }
}
