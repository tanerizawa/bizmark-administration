<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewFeedback extends Model
{
    protected $table = 'interview_feedback';

    protected $fillable = [
        'interview_schedule_id',
        'interviewer_id',
        'technical_skills',
        'communication',
        'problem_solving',
        'cultural_fit',
        'motivation',
        'overall_rating',
        'strengths',
        'weaknesses',
        'detailed_notes',
        'recommendation',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'overall_rating' => 'decimal:1',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the interview schedule.
     */
    public function interviewSchedule(): BelongsTo
    {
        return $this->belongsTo(InterviewSchedule::class);
    }

    /**
     * Get the interviewer who submitted feedback.
     */
    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    /**
     * Calculate overall rating from individual scores.
     */
    public function calculateOverallRating(): float
    {
        $scores = array_filter([
            $this->technical_skills,
            $this->communication,
            $this->problem_solving,
            $this->cultural_fit,
            $this->motivation,
        ]);

        if (empty($scores)) {
            return 0;
        }

        return round(array_sum($scores) / count($scores), 1);
    }

    /**
     * Get recommendation label.
     */
    public function getRecommendationLabel(): string
    {
        return match($this->recommendation) {
            'strong-hire' => 'Sangat Direkomendasikan',
            'hire' => 'Direkomendasikan',
            'maybe' => 'Pertimbangkan',
            'no-hire' => 'Tidak Direkomendasikan',
            default => ucfirst($this->recommendation),
        };
    }

    /**
     * Get recommendation color.
     */
    public function getRecommendationColor(): string
    {
        return match($this->recommendation) {
            'strong-hire' => 'green',
            'hire' => 'blue',
            'maybe' => 'yellow',
            'no-hire' => 'red',
            default => 'gray',
        };
    }

    /**
     * Check if feedback is complete.
     */
    public function isComplete(): bool
    {
        return !empty($this->recommendation) && 
               !empty($this->overall_rating) &&
               $this->submitted_at !== null;
    }

    /**
     * Scope: by recommendation type.
     */
    public function scopeByRecommendation($query, $recommendation)
    {
        return $query->where('recommendation', $recommendation);
    }

    /**
     * Scope: submitted feedback only.
     */
    public function scopeSubmitted($query)
    {
        return $query->whereNotNull('submitted_at');
    }
}
