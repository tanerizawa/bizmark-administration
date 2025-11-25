<?php

namespace App\Observers;

use App\Models\Project;
use App\Models\ProjectStatus;

class ProjectObserver
{
    /**
     * Handle the Project "creating" event.
     * Runs BEFORE the project is saved to database
     */
    public function creating(Project $project): void
    {
        // Auto-calculate progress from tasks if not set
        if (is_null($project->progress_percentage)) {
            $project->progress_percentage = $this->calculateProgressFromTasks($project);
        }
    }

    /**
     * Handle the Project "created" event.
     */
    public function created(Project $project): void
    {
        // Log project creation
        $project->logs()->create([
            'action' => 'created',
            'description' => "Proyek '{$project->name}' dibuat",
            'new_values' => $project->toArray(),
        ]);
    }

    /**
     * Handle the Project "updating" event.
     * Runs BEFORE changes are saved
     */
    public function updating(Project $project): void
    {
        // Get what changed
        $dirty = $project->getDirty();
        
        // If progress changed, check if we need to auto-update status
        if (isset($dirty['progress_percentage'])) {
            $this->autoUpdateStatusFromProgress($project);
        }
        
        // If status changed to "Selesai", ensure progress is 100%
        if (isset($dirty['status_id'])) {
            $this->validateStatusProgress($project);
        }
    }

    /**
     * Handle the Project "updated" event.
     * Runs AFTER changes are saved
     */
    public function updated(Project $project): void
    {
        // Log will be created by controller for detailed tracking
    }

    /**
     * Handle the Project "deleted" event.
     */
    public function deleted(Project $project): void
    {
        // Log already handled by controller
    }

    /**
     * Handle the Project "restored" event.
     */
    public function restored(Project $project): void
    {
        $project->logs()->create([
            'action' => 'restored',
            'description' => "Proyek '{$project->name}' dipulihkan dari soft delete",
        ]);
    }

    /**
     * Handle the Project "force deleted" event.
     */
    public function forceDeleted(Project $project): void
    {
        // Permanent deletion - logs will be cascade deleted
    }

    /**
     * Auto-update status based on progress percentage
     */
    private function autoUpdateStatusFromProgress(Project $project): void
    {
        $progress = $project->progress_percentage;
        
        // If progress reaches 100%, auto-change to "Selesai" status
        if ($progress >= 100) {
            $completedStatus = ProjectStatus::where('code', 'COMPLETED')
                ->orWhere('name', 'Selesai')
                ->orWhere('code', 'SK_TERBIT')
                ->first();
            
            if ($completedStatus && $project->status_id != $completedStatus->id) {
                $project->status_id = $completedStatus->id;
                
                // Create log for auto status change
                \Log::info("Auto-changed project #{$project->id} status to 'Selesai' due to 100% progress");
            }
        }
        
        // If progress is 0%, and status is still "Lead" or "Penawaran", change to "Kontrak"
        elseif ($progress > 0 && $progress < 100) {
            $currentStatus = ProjectStatus::find($project->status_id);
            
            if ($currentStatus && in_array($currentStatus->code, ['LEAD', 'PROPOSAL'])) {
                $inProgressStatus = ProjectStatus::where('code', 'IN_PROGRESS')
                    ->orWhere('code', 'KONTRAK')
                    ->first();
                
                if ($inProgressStatus) {
                    $project->status_id = $inProgressStatus->id;
                    \Log::info("Auto-changed project #{$project->id} status to 'In Progress' due to progress > 0%");
                }
            }
        }
    }

    /**
     * Validate status and progress consistency
     */
    private function validateStatusProgress(Project $project): void
    {
        $newStatus = ProjectStatus::find($project->status_id);
        
        if (!$newStatus) {
            return;
        }
        
        // If status is "Selesai" or any final status, ensure progress is 100%
        if ($newStatus->is_final || 
            in_array($newStatus->code, ['COMPLETED', 'SELESAI', 'SK_TERBIT', 'CLOSED'])) {
            
            if ($project->progress_percentage < 100) {
                $project->progress_percentage = 100;
                \Log::info("Auto-set project #{$project->id} progress to 100% due to final status");
            }
            
            // Auto-set completed_at if not already set
            if (is_null($project->completed_at)) {
                $project->completed_at = now();
                \Log::info("Auto-set project #{$project->id} completed_at to now() due to final status");
            }
        }
        
        // If status is "Dibatalkan", don't change progress
        if ($newStatus->code === 'CANCELLED' || $newStatus->code === 'DIBATALKAN') {
            // Keep current progress as is
            \Log::info("Project #{$project->id} cancelled - progress kept at {$project->progress_percentage}%");
        }
    }

    /**
     * Calculate progress from task completion rate
     */
    private function calculateProgressFromTasks(Project $project): int
    {
        // Only calculate if project has ID (already exists)
        if (!$project->id) {
            return 0;
        }
        
        $totalTasks = $project->tasks()->count();
        
        if ($totalTasks === 0) {
            return $project->progress_percentage ?? 0;
        }
        
        $completedTasks = $project->tasks()
            ->whereIn('status', ['done', 'completed', 'selesai', 'DONE', 'COMPLETED', 'SELESAI'])
            ->count();
        
        return round(($completedTasks / $totalTasks) * 100);
    }
}
