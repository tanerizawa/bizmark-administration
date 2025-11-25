<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TechnicalTestSubmission extends Model
{
    protected $fillable = [
        'job_application_id',
        'test_title',
        'test_description',
        'original_file_path',
        'submission_file_path',
        'file_type',
        'format_score',
        'format_issues',
        'reviewer_id',
        'review_score',
        'review_notes',
        'reviewed_at',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'format_issues' => 'array',
        'format_score' => 'decimal:2',
        'review_score' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'submitted_at' => 'datetime',
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
     * Get the reviewer.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Get original file URL.
     */
    public function getOriginalFileUrl(): ?string
    {
        if (!$this->original_file_path) {
            return null;
        }

        return Storage::url($this->original_file_path);
    }

    /**
     * Get submission file URL.
     */
    public function getSubmissionFileUrl(): ?string
    {
        if (!$this->submission_file_path) {
            return null;
        }

        return Storage::url($this->submission_file_path);
    }

    /**
     * Download original file.
     */
    public function downloadOriginal()
    {
        if (!$this->original_file_path) {
            return null;
        }

        return Storage::download($this->original_file_path);
    }

    /**
     * Download submission file.
     */
    public function downloadSubmission()
    {
        if (!$this->submission_file_path) {
            return null;
        }

        return Storage::download($this->submission_file_path);
    }

    /**
     * Get combined score (format + review).
     */
    public function getCombinedScore(): ?float
    {
        if ($this->format_score !== null && $this->review_score !== null) {
            return round(($this->format_score * 0.3) + ($this->review_score * 0.7), 2);
        }

        return $this->review_score ?? $this->format_score;
    }

    /**
     * Check if submission needs review.
     */
    public function needsReview(): bool
    {
        return $this->status === 'submitted' || $this->status === 'under-review';
    }

    /**
     * Check if review is completed.
     */
    public function isReviewed(): bool
    {
        return $this->status === 'reviewed' && 
               $this->reviewed_at !== null &&
               $this->review_score !== null;
    }

    /**
     * Get status label.
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'submitted' => 'Menunggu Review',
            'under-review' => 'Sedang Direview',
            'reviewed' => 'Sudah Direview',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status color.
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'submitted' => 'yellow',
            'under-review' => 'blue',
            'reviewed' => 'green',
            default => 'gray',
        };
    }

    /**
     * Get file size in human readable format.
     */
    public function getSubmissionFileSize(): string
    {
        if (!$this->submission_file_path || !Storage::exists($this->submission_file_path)) {
            return 'N/A';
        }

        $bytes = Storage::size($this->submission_file_path);
        
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        
        return $bytes . ' bytes';
    }

    /**
     * Get file extension.
     */
    public function getFileExtension(): string
    {
        return strtoupper($this->file_type ?? 'Unknown');
    }

    /**
     * Scope: pending review.
     */
    public function scopePendingReview($query)
    {
        return $query->whereIn('status', ['submitted', 'under-review']);
    }

    /**
     * Scope: reviewed submissions.
     */
    public function scopeReviewed($query)
    {
        return $query->where('status', 'reviewed');
    }

    /**
     * Scope: by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
