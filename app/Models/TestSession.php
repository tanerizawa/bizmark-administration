<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TestSession extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'job_application_id',
        'test_template_id',
        'recruitment_stage_id',
        'session_token',
        'status',
        'starts_at',
        'expires_at',
        'started_at',
        'completed_at',
        'score',
        'passed',
        'time_taken_minutes',
        'ip_address',
        'user_agent',
        'tab_switches',
        'submitted_file_path',
        'submitted_at',
        'evaluation_scores',
        'evaluator_id',
        'evaluator_notes',
        'evaluated_at',
        'requires_manual_review',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'submitted_at' => 'datetime',
        'evaluated_at' => 'datetime',
        'passed' => 'boolean',
        'requires_manual_review' => 'boolean',
        'score' => 'decimal:2',
        'evaluation_scores' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($session) {
            if (empty($session->session_token)) {
                $session->session_token = Str::random(64);
            }
        });
    }

    /**
     * Get the job application.
     */
    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    /**
     * Get the test template.
     */
    public function testTemplate(): BelongsTo
    {
        return $this->belongsTo(TestTemplate::class);
    }

    /**
     * Get the recruitment stage this test is linked to.
     */
    public function recruitmentStage(): BelongsTo
    {
        return $this->belongsTo(RecruitmentStage::class);
    }

    /**
     * Get all answers for this session.
     */
    public function testAnswers(): HasMany
    {
        return $this->hasMany(TestAnswer::class);
    }

    /**
     * Check if session is active (can take test).
     */
    public function isActive(): bool
    {
        return $this->status === 'in-progress' &&
               $this->expires_at->isFuture();
    }

    /**
     * Check if session is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast() && 
               $this->status !== 'completed';
    }

    /**
     * Check if session can be started.
     */
    public function canStart(): bool
    {
        return in_array($this->status, ['pending', 'not-started']) &&
               $this->expires_at->isFuture();
    }

    /**
     * Get remaining time in minutes.
     */
    public function getRemainingMinutes(): int
    {
        // If not started yet, return full duration
        if ($this->status === 'not-started' || !$this->started_at) {
            return $this->testTemplate->duration_minutes;
        }

        // If completed or expired, return 0
        if (in_array($this->status, ['completed', 'expired', 'cancelled'])) {
            return 0;
        }

        // Calculate remaining time based on started_at
        $elapsed = now()->diffInMinutes($this->started_at);
        $duration = $this->testTemplate->duration_minutes;
        
        return max(0, $duration - $elapsed);
    }

    /**
     * Get progress percentage.
     */
    public function getProgressPercentage(): float
    {
        $totalQuestions = $this->testTemplate->getQuestionsCount();
        
        if ($totalQuestions === 0) {
            return 0;
        }

        $answeredCount = $this->testAnswers()->count();
        
        return round(($answeredCount / $totalQuestions) * 100, 1);
    }

    /**
     * Get status label.
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'in-progress' => 'Sedang Berlangsung',
            'completed' => 'Selesai',
            'expired' => 'Kadaluarsa',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status color.
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'in-progress' => 'blue',
            'completed' => 'green',
            'expired' => 'red',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Start the test session.
     */
    public function start(): bool
    {
        if (!$this->canStart()) {
            return false;
        }

        return $this->update([
            'status' => 'in-progress',
            'started_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Complete the test session with score.
     */
    public function complete(float $score, bool $passed): bool
    {
        $updated = $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'score' => $score,
            'passed' => $passed,
            'time_taken_minutes' => $this->started_at ? 
                now()->diffInMinutes($this->started_at) : null,
        ]);

        // Dispatch event for recruitment workflow automation
        if ($updated && $this->recruitment_stage_id) {
            event(new \App\Events\TestCompleted($this, $passed, $score));
        }

        return $updated;
    }

    /**
     * Complete the test session without scoring (requires manual review).
     * Now with auto-grading for objective questions.
     */
    public function completeWithoutScore(): bool
    {
        // Calculate time taken safely
        $timeTaken = null;
        if ($this->started_at) {
            // Use diffInMinutes with absolute=false to detect negative values
            $minutes = $this->started_at->diffInMinutes(now(), false);
            
            // Only use positive values, and ensure it's an integer
            if ($minutes > 0) {
                $timeTaken = (int) round($minutes);
            }
        }
        
        // Auto-grade the session
        $grading = $this->autoGrade();
        
        $updated = $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'time_taken_minutes' => $timeTaken,
            'score' => $grading['score'],
            'passed' => $grading['passed'],
            'requires_manual_review' => $grading['requires_manual_review'],
        ]);
        
        // Dispatch event for recruitment workflow automation
        // Only if has score and passed (fully auto-graded)
        if ($updated && $this->recruitment_stage_id && $grading['score'] !== null && !$grading['requires_manual_review']) {
            event(new \App\Events\TestCompleted($this, $grading['passed'], $grading['score']));
        }
        
        return $updated;
    }

    /**
     * Auto-grade the test session.
     * Grades objective questions (multiple-choice) automatically.
     * Flags for manual review if has subjective questions (essay/rating).
     * 
     * @return array ['score' => float|null, 'passed' => bool, 'requires_manual_review' => bool]
     */
    public function autoGrade(): array
    {
        $questions = $this->testTemplate->questions_data ?? [];
        $answers = $this->testAnswers->keyBy('question_id');
        
        $totalPoints = 0;
        $earnedPoints = 0;
        $autoGradeableCount = 0;
        $requiresManualReview = false;
        
        foreach ($questions as $index => $question) {
            $questionType = $question['question_type'] ?? null;
            $points = $question['points'] ?? 1;
            $answer = $answers->get($index);
            
            // Skip if no answer
            if (!$answer) {
                continue;
            }
            
            $answerValue = $answer->answer_data['answer_value'] ?? null;
            
            // Auto-grade multiple choice questions
            if ($questionType === 'multiple-choice' && isset($question['correct_answer'])) {
                $autoGradeableCount++;
                $totalPoints += $points;
                
                // Check if answer is correct
                if ($answerValue == $question['correct_answer']) {
                    $earnedPoints += $points;
                }
            }
            // Flag subjective questions for manual review
            elseif (in_array($questionType, ['essay', 'rating', 'rating-scale', 'document-editing'])) {
                $requiresManualReview = true;
                $totalPoints += $points;
            }
        }
        
        // Calculate score
        $score = null;
        $passed = false;
        
        if ($autoGradeableCount > 0 && !$requiresManualReview) {
            // All questions are auto-gradeable
            $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;
            $passed = $score >= $this->testTemplate->passing_score;
        } elseif ($autoGradeableCount > 0 && $requiresManualReview) {
            // Mix of auto-gradeable and manual review
            // Set partial score, but keep requires_manual_review
            $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;
            // Don't set passed yet - wait for manual review to complete
        }
        
        return [
            'score' => $score,
            'passed' => $passed,
            'requires_manual_review' => $requiresManualReview,
            'auto_gradeable_count' => $autoGradeableCount,
            'total_points' => $totalPoints,
            'earned_points' => $earnedPoints,
        ];
    }

    /**
     * Increment tab switches counter (anti-cheat).
     */
    public function incrementTabSwitches(): int
    {
        $this->increment('tab_switches');
        return $this->tab_switches;
    }

    /**
     * Scope: active sessions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'in-progress')
                    ->where('expires_at', '>', now());
    }

    /**
     * Scope: completed sessions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Relation: evaluator user.
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    /**
     * Get submitted file URL.
     */
    public function getSubmittedFileUrl(): ?string
    {
        if (!$this->submitted_file_path) {
            return null;
        }
        
        return \Storage::url($this->submitted_file_path);
    }

    /**
     * Check if evaluation is pending.
     */
    public function isPendingEvaluation(): bool
    {
        return $this->requires_manual_review && !$this->evaluated_at;
    }

    /**
     * Complete manual evaluation for subjective questions.
     * 
     * @param int $evaluatorId - User ID who evaluated
     * @param array $manualScores - ['question_index' => score]
     * @param string|null $notes - Evaluator notes
     * @return bool
     */
    public function completeManualEvaluation(int $evaluatorId, array $manualScores, ?string $notes = null): bool
    {
        $questions = $this->testTemplate->questions_data ?? [];
        $answers = $this->testAnswers->keyBy('question_id');
        
        // Get auto-grading results first
        $grading = $this->autoGrade();
        $totalPoints = 0;
        $earnedPoints = $grading['earned_points']; // Start with auto-graded points
        
        // Add manual scores for subjective questions
        foreach ($questions as $index => $question) {
            $questionType = $question['question_type'] ?? null;
            $points = $question['points'] ?? 1;
            $totalPoints += $points;
            
            // Add manual scores for subjective questions
            if (in_array($questionType, ['essay', 'rating', 'rating-scale', 'document-editing'])) {
                if (isset($manualScores[$index])) {
                    $earnedPoints += $manualScores[$index];
                }
            }
        }
        
        // Calculate final score
        $finalScore = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;
        $passed = $finalScore >= $this->testTemplate->passing_score;
        
        $updated = $this->update([
            'score' => $finalScore,
            'passed' => $passed,
            'requires_manual_review' => false,
            'evaluator_id' => $evaluatorId,
            'evaluator_notes' => $notes,
            'evaluated_at' => now(),
            'evaluation_scores' => [
                'manual_scores' => $manualScores,
                'total_points' => $totalPoints,
                'earned_points' => $earnedPoints,
                'final_score' => $finalScore,
            ],
        ]);
        
        // Dispatch event for recruitment workflow automation
        if ($updated && $this->recruitment_stage_id) {
            event(new \App\Events\TestCompleted($this, $passed, $finalScore));
        }
        
        return $updated;
    }

    /**
     * Calculate score from evaluation.
     */
    public function calculateScoreFromEvaluation(): float
    {
        if (!$this->evaluation_scores || !isset($this->evaluation_scores['criteria_scores'])) {
            return 0;
        }
        
        $totalScore = collect($this->evaluation_scores['criteria_scores'])->sum('score');
        $template = $this->testTemplate;
        $totalPoints = $template->getTotalEvaluationPoints();
        
        if ($totalPoints == 0) {
            return 0;
        }
        
        return round(($totalScore / $totalPoints) * 100, 2);
    }
}
