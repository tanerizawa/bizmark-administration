<?php

namespace App\Listeners;

use App\Events\StageStarted;
use App\Events\TestCompleted;
use App\Models\JobApplication;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateRecruitmentStageAfterTest implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(TestCompleted $event): void
    {
        $session = $event->session;
        $stage = $session->recruitmentStage;
        
        // If no stage linked, skip automation
        if (!$stage) {
            \Log::warning("Test session {$session->id} completed but no recruitment_stage_id linked");
            return;
        }
        
        // Update stage with test results
        $stage->update([
            'status' => $event->passed ? 'passed' : 'failed',
            'score' => $event->score,
            'completed_at' => now(),
            'notes' => "Test completed: {$session->testTemplate->title} - Score: {$event->score}%"
        ]);
        
        \Log::info("Recruitment stage {$stage->id} updated: " . ($event->passed ? 'PASSED' : 'FAILED'));
        
        $application = $session->jobApplication;
        
        if ($event->passed) {
            // Test passed - start next stage
            $this->startNextStage($application);
        } else {
            // Test failed - auto-reject application
            $application->update([
                'status' => 'rejected',
                'notes' => "Failed {$stage->getStageNameLabel()} with score {$event->score}%"
            ]);
            
            \Log::info("Application {$application->id} auto-rejected due to failed test");
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
