<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentDraft extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'template_id',
        'ai_log_id',
        'title',
        'content',
        'sections',
        'status',
        'created_by',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'sections' => 'array',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the project associated with this draft
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Get the template used for this draft
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class, 'template_id');
    }

    /**
     * Get the AI processing log
     */
    public function aiLog(): BelongsTo
    {
        return $this->belongsTo(AIProcessingLog::class, 'ai_log_id');
    }

    /**
     * Get the user who created this draft
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this draft (nullable)
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the latest compliance check for this draft
     */
    public function complianceCheck(): BelongsTo
    {
        return $this->belongsTo(ComplianceCheck::class, 'id', 'draft_id')->latest();
    }

    /**
     * Get all compliance checks for this draft
     */
    public function complianceChecks()
    {
        return $this->hasMany(ComplianceCheck::class, 'draft_id');
    }

    /**
     * Scope draft status
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope approved status
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope by project
     */
    public function scopeForProject($query, int $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Mark as reviewed
     */
    public function markAsReviewed(): bool
    {
        return $this->update(['status' => 'reviewed']);
    }

    /**
     * Approve the draft
     */
    public function approve(int $userId): bool
    {
        return $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $userId,
        ]);
    }

    /**
     * Reject the draft
     */
    public function reject(): bool
    {
        return $this->update(['status' => 'rejected']);
    }

    /**
     * Get word count
     */
    public function getWordCountAttribute(): int
    {
        return str_word_count(strip_tags($this->content));
    }

    /**
     * Get character count
     */
    public function getCharCountAttribute(): int
    {
        return mb_strlen(strip_tags($this->content));
    }
}
