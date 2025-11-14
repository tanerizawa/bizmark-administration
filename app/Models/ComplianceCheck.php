<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplianceCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'draft_id',
        'document_type',
        'overall_score',
        'structure_score',
        'compliance_score',
        'formatting_score',
        'completeness_score',
        'issues',
        'status',
        'error_message',
        'total_issues',
        'critical_issues',
        'warning_issues',
        'info_issues',
        'checked_at',
    ];

    protected $casts = [
        'issues' => 'array',
        'overall_score' => 'decimal:2',
        'structure_score' => 'decimal:2',
        'compliance_score' => 'decimal:2',
        'formatting_score' => 'decimal:2',
        'completeness_score' => 'decimal:2',
        'checked_at' => 'datetime',
    ];

    /**
     * Get the draft that owns this compliance check.
     */
    public function draft(): BelongsTo
    {
        return $this->belongsTo(DocumentDraft::class);
    }

    /**
     * Scope: Get only completed checks.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Get checks with critical issues.
     */
    public function scopeWithCriticalIssues($query)
    {
        return $query->where('critical_issues', '>', 0);
    }

    /**
     * Scope: Get recent checks (within last 24 hours).
     */
    public function scopeRecent($query)
    {
        return $query->where('checked_at', '>=', now()->subHours(24));
    }

    /**
     * Check if the document passes compliance (score >= 80).
     */
    public function isPassed(): bool
    {
        return $this->overall_score >= 80;
    }

    /**
     * Get compliance status label.
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->overall_score >= 90) {
            return 'Excellent';
        } elseif ($this->overall_score >= 80) {
            return 'Good';
        } elseif ($this->overall_score >= 70) {
            return 'Fair';
        } elseif ($this->overall_score >= 60) {
            return 'Poor';
        } else {
            return 'Critical';
        }
    }

    /**
     * Get compliance status color.
     */
    public function getStatusColorAttribute(): string
    {
        if ($this->overall_score >= 80) {
            return '#34C759'; // Green
        } elseif ($this->overall_score >= 70) {
            return '#FF9500'; // Orange
        } else {
            return '#FF3B30'; // Red
        }
    }

    /**
     * Get issues grouped by category.
     */
    public function getIssuesByCategoryAttribute(): array
    {
        if (!$this->issues) {
            return [];
        }

        $grouped = [];
        foreach ($this->issues as $issue) {
            $category = $issue['category'] ?? 'general';
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $issue;
        }

        return $grouped;
    }

    /**
     * Get critical issues only (as array of issue objects).
     */
    public function getCriticalIssuesListAttribute(): array
    {
        if (!$this->issues) {
            return [];
        }

        return array_filter($this->issues, function ($issue) {
            return ($issue['severity'] ?? '') === 'critical';
        });
    }

    /**
     * Check if needs recheck (older than 24 hours).
     */
    public function needsRecheck(): bool
    {
        if (!$this->checked_at) {
            return true;
        }

        return $this->checked_at->lt(now()->subHours(24));
    }
}
