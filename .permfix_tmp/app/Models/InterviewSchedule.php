<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InterviewSchedule extends Model
{
    protected $fillable = [
        'job_application_id',
        'recruitment_stage_id',
        'interview_type',
        'interview_stage',
        'scheduled_at',
        'duration_minutes',
        'location',
        'meeting_type',
        'meeting_link',
        'meeting_password',
        'interviewer_ids',
        'status',
        'notes',
        'reminder_sent_at',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'completed_at' => 'datetime',
        'interviewer_ids' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the job application for this interview.
     */
    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    /**
     * Get the recruitment stage this interview is linked to.
     */
    public function recruitmentStage(): BelongsTo
    {
        return $this->belongsTo(RecruitmentStage::class);
    }

    /**
     * Get the user who created this schedule.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all feedback for this interview.
     */
    public function feedback(): HasMany
    {
        return $this->hasMany(InterviewFeedback::class);
    }

    /**
     * Get interviewers (users).
     */
    public function interviewers()
    {
        // Handle empty or null interviewer_ids
        if (empty($this->interviewer_ids)) {
            return collect();
        }
        
        // Ensure interviewer_ids is an array
        $ids = $this->interviewer_ids;
        if (is_string($ids)) {
            // Handle JSON string or comma-separated string
            $ids = json_decode($ids, true) ?? explode(',', $ids);
            $ids = array_filter(array_map('intval', $ids));
        }
        
        // If still empty after processing, return empty collection
        if (empty($ids) || !is_array($ids)) {
            return collect();
        }
        
        return User::whereIn('id', $ids)->get();
    }

    /**
     * Check if interview is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->scheduled_at->isFuture() && 
               in_array($this->status, ['scheduled', 'confirmed']);
    }

    /**
     * Get meeting type label.
     */
    public function getMeetingTypeLabel(): string
    {
        return match($this->meeting_type) {
            'in-person' => 'Tatap Muka',
            'video-call' => 'Video Call',
            'phone' => 'Telepon',
            default => ucfirst($this->meeting_type),
        };
    }

    /**
     * Get interview type label.
     */
    public function getInterviewTypeLabel(): string
    {
        return match($this->interview_type) {
            'preliminary' => 'Screening Awal',
            'technical' => 'Teknis',
            'hr' => 'HR',
            'final' => 'Final',
            default => ucfirst($this->interview_type),
        };
    }

    /**
     * Scope: upcoming interviews.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now())
                    ->whereIn('status', ['scheduled', 'confirmed'])
                    ->orderBy('scheduled_at', 'asc');
    }

    /**
     * Scope: by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
