<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestTemplate extends Model
{
    protected $fillable = [
        'title',
        'test_type',
        'description',
        'duration_minutes',
        'passing_score',
        'questions_data',
        'instructions',
        'is_active',
        'created_by',
        'template_file_path',
        'evaluation_criteria',
    ];

    protected $casts = [
        'questions_data' => 'array',
        'evaluation_criteria' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the creator of this template.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all test sessions using this template.
     */
    public function testSessions(): HasMany
    {
        return $this->hasMany(TestSession::class);
    }

    /**
     * Get completed sessions count.
     */
    public function completedSessionsCount(): int
    {
        return $this->testSessions()->where('status', 'completed')->count();
    }

    /**
     * Get total number of questions.
     */
    public function getTotalQuestionsAttribute(): int
    {
        if (is_array($this->questions_data)) {
            return count($this->questions_data);
        }
        return 0;
    }

    /**
     * Get average score from completed sessions.
     */
    public function averageScore(): float
    {
        return $this->testSessions()
                    ->where('status', 'completed')
                    ->avg('score') ?? 0;
    }

    /**
     * Get pass rate percentage.
     */
    public function passRate(): float
    {
        $total = $this->completedSessionsCount();
        
        if ($total === 0) {
            return 0;
        }

        $passed = $this->testSessions()
                       ->where('status', 'completed')
                       ->where('passed', true)
                       ->count();

        return round(($passed / $total) * 100, 1);
    }

    /**
     * Get test type label.
     */
    public function getTestTypeLabel(): string
    {
        return match($this->test_type) {
            'psychology' => 'Psikologi',
            'psychometric' => 'Psikometrik',
            'technical' => 'Teknis',
            'aptitude' => 'Kemampuan',
            'personality' => 'Kepribadian',
            default => ucfirst($this->test_type),
        };
    }

    /**
     * Get total questions count.
     */
    public function getQuestionsCount(): int
    {
        return is_array($this->questions_data) ? count($this->questions_data) : 0;
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDuration(): string
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return "{$hours} jam " . ($minutes > 0 ? "{$minutes} menit" : "");
        }

        return "{$minutes} menit";
    }

    /**
     * Scope: active templates only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: by test type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('test_type', $type);
    }

    /**
     * Check if this is a document editing test.
     */
    public function isDocumentEditingTest(): bool
    {
        return $this->test_type === 'document-editing';
    }

    /**
     * Get template file URL for download.
     */
    public function getTemplateFileUrl(): ?string
    {
        if (!$this->template_file_path) {
            return null;
        }
        
        return \Storage::url($this->template_file_path);
    }

    /**
     * Get total evaluation points.
     */
    public function getTotalEvaluationPoints(): int
    {
        if (!$this->evaluation_criteria || !isset($this->evaluation_criteria['criteria'])) {
            return 100;
        }
        
        return collect($this->evaluation_criteria['criteria'])->sum('points');
    }
}
