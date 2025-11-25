<?php

namespace App\Listeners;

use App\Events\InterviewCompleted;
use App\Events\StageStarted;
use App\Models\JobApplication;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateRecruitmentStageAfterInterview implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(InterviewCompleted $event): void
    {
        $interview = $event->interview;
        $stage = $interview->recruitmentStage;
        
        // If no stage linked, skip automation
        if (!$stage) {
            \Log::warning("Interview {$interview->id} completed but no recruitment_stage_id linked");
            return;
        }
        
        // Determine if passed based on recommendation
        $passed = in_array($event->recommendation, ['highly-recommended', 'recommended']);
        
        // Convert 1-5 rating to 0-100 score
        $score = $event->overallRating * 20;
        
        // Update stage with interview results
        $stage->update([
            'status' => $passed ? 'passed' : 'failed',
            'score' => $score,
            'completed_at' => now(),
            'notes' => "Interview completed: " . ucfirst(str_replace('-', ' ', $event->recommendation)) . " (Rating: {$event->overallRating}/5)"
        ]);
        
        \Log::info("Recruitment stage {$stage->id} updated after interview: " . ($passed ? 'PASSED' : 'FAILED'));
        
        $application = $interview->jobApplication;
        
        if ($passed) {
            // Interview passed - start next stage
            $this->startNextStage($application);
        } else {
            // Interview failed - auto-reject application
            $application->update([
                'status' => 'rejected',
                'notes' => "Did not pass {$stage->getStageNameLabel()}: {$event->recommendation}"
            ]);
            
            \Log::info("Application {$application->id} auto-rejected due to interview feedback");
        }
    }
    
    /**
     * Start the next pending stage.
     */
    private function startNextStage(JobApplication $application): void
    {
        $nextStage = $application->recruitmentStages()
                                 ->where('status', 'pending')
                                 ->orderBy('stage_order')
                                 ->first();
        
        if ($nextStage) {
            $nextStage->update([
                'status' => 'in-progress',
                'started_at' => now()
            ]);
            
            \Log::info("Started next stage: {$nextStage->stage_name} for application {$application->id}");
            
            // Dispatch event for next stage
            event(new StageStarted($nextStage));
        } else {
            // All stages completed - check if application should be accepted
            $this->checkApplicationCompletion($application);
        }
    }
    
    /**
     * Check if all stages are completed and application should be accepted.
     */
    private function checkApplicationCompletion(JobApplication $application): void
    {
        $allStages = $application->recruitmentStages;
        
        // Check if all stages passed
        $allPassed = $allStages->every(fn($s) => $s->status === 'passed');
        
        if ($allPassed) {
            $application->update(['status' => 'accepted']);
            \Log::info("Application {$application->id} auto-accepted - all stages passed!");
        }
    }
}
