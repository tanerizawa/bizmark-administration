<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'sop_notes',
        'assigned_user_id',
        'status',
        'priority',
        'due_date',
        'started_at',
        'completed_at',
        'completion_notes',
        'institution_id',
        'depends_on_task_id',
        'project_permit_id',
        'estimated_hours',
        'actual_hours',
        'sort_order',
    ];

    protected $casts = [
        'due_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====
    
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Task that this task depends on (prerequisite)
     */
    public function dependsOnTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'depends_on_task_id');
    }

    /**
     * Tasks that depend on this task
     */
    public function dependentTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'depends_on_task_id');
    }

    /**
     * Permit that this task is related to
     */
    public function permit(): BelongsTo
    {
        return $this->belongsTo(ProjectPermit::class, 'project_permit_id');
    }

    // ===== SCOPES =====

    public function scopePending($query)
    {
        return $query->where('status', 'todo');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'done');
    }

    public function scopeBlocked($query)
    {
        return $query->where('status', 'blocked');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', ['done']);
    }

    // ===== HELPER METHODS =====

    /**
     * Check if task can be started based on dependencies
     */
    public function canStart(): bool
    {
        // If no dependency, can always start
        if (!$this->depends_on_task_id) {
            return true;
        }

        // Check if dependency is completed
        $dependency = $this->dependsOnTask;
        return $dependency && $dependency->status === 'done';
    }

    /**
     * Get blocking tasks (dependencies that are not completed)
     */
    public function getBlockers(): array
    {
        $blockers = [];

        if ($this->depends_on_task_id && $this->dependsOnTask) {
            $dep = $this->dependsOnTask;
            if ($dep->status !== 'done') {
                $blockers[] = $dep->title;
            }
        }

        return $blockers;
    }

    /**
     * Get status badge color
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'todo' => 'rgba(142, 142, 147, 1)',
            'in_progress' => 'rgba(10, 132, 255, 1)',
            'done' => 'rgba(52, 199, 89, 1)',
            'blocked' => 'rgba(255, 59, 48, 1)',
            default => 'rgba(142, 142, 147, 1)',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'todo' => 'Belum Dimulai',
            'in_progress' => 'Dalam Proses',
            'done' => 'Selesai',
            'blocked' => 'Terblokir',
            default => $this->status,
        };
    }

    /**
     * Get priority badge color
     */
    public function getPriorityColor(): string
    {
        return match($this->priority) {
            'urgent' => 'rgba(255, 59, 48, 1)',
            'high' => 'rgba(255, 149, 0, 1)',
            'normal' => 'rgba(10, 132, 255, 1)',
            'low' => 'rgba(142, 142, 147, 1)',
            default => 'rgba(142, 142, 147, 1)',
        };
    }

    /**
     * Get priority label
     */
    public function getPriorityLabel(): string
    {
        return match($this->priority) {
            'urgent' => 'Mendesak',
            'high' => 'Tinggi',
            'normal' => 'Normal',
            'low' => 'Rendah',
            default => $this->priority,
        };
    }

    /**
     * Check if task is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               $this->status !== 'done';
    }

    /**
     * Get progress percentage (0-100)
     */
    public function getProgress(): int
    {
        return match($this->status) {
            'todo' => 0,
            'in_progress' => 50,
            'done' => 100,
            'blocked' => 25,
            default => 0,
        };
    }
}
