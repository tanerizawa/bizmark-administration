<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestAnswer extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'test_session_id',
        'question_id',
        'answer_data',
        'is_correct',
        'points_earned',
        'time_spent_seconds',
        'answered_at',
    ];

    protected $casts = [
        'answer_data' => 'array',
        'is_correct' => 'boolean',
        'points_earned' => 'decimal:2',
        'answered_at' => 'datetime',
    ];

    /**
     * Get the test session.
     */
    public function testSession(): BelongsTo
    {
        return $this->belongsTo(TestSession::class);
    }

    /**
     * Get the question data from template.
     */
    public function getQuestion(): ?array
    {
        $template = $this->testSession->testTemplate;
        
        if (!$template || !is_array($template->questions_data)) {
            return null;
        }

        foreach ($template->questions_data as $question) {
            if ($question['id'] === $this->question_id) {
                return $question;
            }
        }

        return null;
    }

    /**
     * Check if answer is correct.
     */
    public function checkCorrectness(): bool
    {
        $question = $this->getQuestion();
        
        if (!$question) {
            return false;
        }

        // For multiple choice
        if ($question['type'] === 'multiple-choice') {
            $correctAnswer = $question['correct_answer'] ?? null;
            $candidateAnswer = $this->answer_data['answer'] ?? null;
            
            return $correctAnswer === $candidateAnswer;
        }

        // For true/false
        if ($question['type'] === 'true-false') {
            $correctAnswer = $question['correct_answer'] ?? null;
            $candidateAnswer = $this->answer_data['answer'] ?? null;
            
            return $correctAnswer === $candidateAnswer;
        }

        // For essay, requires manual grading
        return false;
    }

    /**
     * Calculate points earned.
     */
    public function calculatePoints(): float
    {
        $question = $this->getQuestion();
        
        if (!$question) {
            return 0;
        }

        $maxPoints = $question['points'] ?? 1;

        if ($this->is_correct) {
            return $maxPoints;
        }

        return 0;
    }

    /**
     * Get formatted time spent.
     */
    public function getFormattedTimeSpent(): string
    {
        if (!$this->time_spent_seconds) {
            return '0s';
        }

        $minutes = floor($this->time_spent_seconds / 60);
        $seconds = $this->time_spent_seconds % 60;

        if ($minutes > 0) {
            return "{$minutes}m {$seconds}s";
        }

        return "{$seconds}s";
    }
}
