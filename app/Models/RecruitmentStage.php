<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RecruitmentStage extends Model
{
    protected $fillable = [
        'job_application_id',
        'stage_name',
        'stage_order',
        'status',
        'score',
        'notes',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the job application.
     */
    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    /**
     * Get the test session for this stage (if applicable).
     */
    public function testSession(): HasOne
    {
        return $this->hasOne(TestSession::class);
    }

    /**
     * Get the interview for this stage (if applicable).
     */
    public function interview(): HasOne
    {
        return $this->hasOne(InterviewSchedule::class);
    }

    /**
     * Get stage name label.
     */
    public function getStageNameLabel(): string
    {
        return match($this->stage_name) {
            'screening' => 'Screening CV',
            'psych-test' => 'Test Psikologi',
            'psychometric-test' => 'Test Psikometrik',
            'technical-test' => 'Test Teknis',
            'technical-file-test' => 'Test File Teknis',
            'interview-1' => 'Interview Tahap 1',
            'interview-2' => 'Interview Tahap 2',
            'interview-hr' => 'Interview HR',
            'interview-technical' => 'Interview Teknis',
            'interview-final' => 'Interview Final',
            'offering' => 'Penawaran',
            default => ucwords(str_replace('-', ' ', $this->stage_name)),
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'in-progress' => 'Sedang Berlangsung',
            'passed' => 'Lulus',
            'failed' => 'Tidak Lulus',
            'skipped' => 'Dilewati',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status color.
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'gray',
            'in-progress' => 'blue',
            'passed' => 'green',
            'failed' => 'red',
            'skipped' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Check if stage is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'in-progress';
    }

    /**
     * Check if stage is completed.
     */
    public function isCompleted(): bool
    {
        return in_array($this->status, ['passed', 'failed', 'skipped']);
    }

    /**
     * Get duration in days.
     */
    public function getDurationDays(): ?int
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        return $this->started_at->diffInDays($this->completed_at);
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDuration(): string
    {
        $days = $this->getDurationDays();
        
        if ($days === null) {
            return 'N/A';
        }

        if ($days === 0) {
            return 'Hari yang sama';
        }

        return $days . ' hari';
    }

    /**
     * Mark stage as started.
     */
    public function markAsStarted(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        return $this->update([
            'status' => 'in-progress',
            'started_at' => now(),
        ]);
    }

    /**
     * Mark stage as passed.
     */
    public function markAsPassed(?float $score = null, ?string $notes = null): bool
    {
        return $this->update([
            'status' => 'passed',
            'score' => $score,
            'notes' => $notes,
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark stage as failed.
     */
    public function markAsFailed(?string $notes = null): bool
    {
        return $this->update([
            'status' => 'failed',
            'notes' => $notes,
            'completed_at' => now(),
        ]);
    }

    /**
     * Scope: by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: active stages.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'in-progress');
    }

    /**
     * Scope: completed stages.
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['passed', 'failed', 'skipped']);
    }

    /**
     * Scope: ordered by stage order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('stage_order', 'asc');
    }
}
