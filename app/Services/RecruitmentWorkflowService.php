<?php

namespace App\Services;

use App\Events\StageStarted;
use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\RecruitmentStage;
use App\Models\TestSession;
use App\Models\TestTemplate;
use App\Models\InterviewSchedule;
use App\Mail\TestAssignedMail;
use App\Mail\InterviewScheduledMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RecruitmentWorkflowService
{
    /**
     * Initialize recruitment pipeline for new application.
     */
    public function initializePipeline(JobApplication $application): void
    {
        $vacancy = $application->jobVacancy;
        $defaultStages = $this->getDefaultStages($vacancy);
        
        foreach ($defaultStages as $order => $stageName) {
            RecruitmentStage::create([
                'job_application_id' => $application->id,
                'stage_name' => $stageName,
                'stage_order' => $order + 1,
                'status' => $order === 0 ? 'in-progress' : 'pending',
                'started_at' => $order === 0 ? now() : null,
            ]);
        }
        
        \Log::info("Initialized recruitment pipeline for application {$application->id} with " . count($defaultStages) . " stages");
    }
    
    /**
     * Get default stages based on vacancy requirements.
     */
    private function getDefaultStages(JobVacancy $vacancy): array
    {
        $stages = ['screening']; // Always start with screening
        
        // Add test stages based on vacancy requirements
        if (isset($vacancy->requirements['requires_psychology_test']) && $vacancy->requirements['requires_psychology_test']) {
            $stages[] = 'psychology-test';
        }
        
        if (isset($vacancy->requirements['requires_technical_test']) && $vacancy->requirements['requires_technical_test']) {
            $stages[] = 'technical-test';
        }
        
        // Default interview stages
        $stages[] = 'hr-interview';
        
        if (isset($vacancy->requirements['requires_technical_interview']) && $vacancy->requirements['requires_technical_interview']) {
            $stages[] = 'technical-interview';
        }
        
        $stages[] = 'final-interview';
        
        return $stages;
    }
    
    /**
     * Assign test to candidate for specific stage.
     */
    public function assignTest(
        JobApplication $application, 
        TestTemplate $template,
        ?RecruitmentStage $stage = null
    ): TestSession {
        // If no stage provided, find or use current stage
        if (!$stage) {
            // Option 1: Find test-specific stage
            $stage = $application->recruitmentStages()
                                ->where('stage_name', 'LIKE', '%test%')
                                ->where('status', 'in-progress')
                                ->first();
            
            // Option 2: Use ANY in-progress stage (screening, etc.)
            if (!$stage) {
                $stage = $application->recruitmentStages()
                                    ->where('status', 'in-progress')
                                    ->orderBy('stage_order')
                                    ->first();
            }
        }
        
        if (!$stage) {
            // Create a default test stage only if NO in-progress stage exists
            $lastStage = $application->recruitmentStages()->orderBy('stage_order', 'desc')->first();
            $nextOrder = $lastStage ? $lastStage->stage_order + 1 : 1;
            
            $stage = RecruitmentStage::create([
                'job_application_id' => $application->id,
                'stage_name' => 'test-stage',
                'stage_order' => $nextOrder,
                'status' => 'in-progress',
                'started_at' => now(),
            ]);
            
            \Log::info("Created new test stage {$stage->id} for application {$application->id}");
        } else {
            \Log::info("Using existing stage {$stage->id} ({$stage->stage_name}) for test assignment");
        }
        
        $session = TestSession::create([
            'job_application_id' => $application->id,
            'test_template_id' => $template->id,
            'recruitment_stage_id' => $stage->id,
            'session_token' => Str::random(64),
            'status' => 'pending',
            'starts_at' => now(),
            'expires_at' => now()->addDays(7),
        ]);
        
        \Log::info("Test session {$session->id} created and linked to stage {$stage->id}");
        
        // Send email notification
        try {
            Mail::to($application->email)->send(new TestAssignedMail($session));
            \Log::info("Test assignment email sent to {$application->email}");
        } catch (\Exception $e) {
            \Log::error("Failed to send test assignment email: " . $e->getMessage());
        }
        
        return $session;
    }
    
    /**
     * Schedule interview for candidate.
     */
    public function scheduleInterview(
        JobApplication $application,
        array $interviewData,
        ?RecruitmentStage $stage = null
    ): InterviewSchedule {
        // If no stage provided, find current interview stage
        if (!$stage) {
            $stage = $application->recruitmentStages()
                                ->where('stage_name', 'LIKE', '%interview%')
                                ->where('status', 'in-progress')
                                ->first();
        }
        
        if (!$stage) {
            // Create a default interview stage if none exists
            $lastStage = $application->recruitmentStages()->orderBy('stage_order', 'desc')->first();
            $nextOrder = $lastStage ? $lastStage->stage_order + 1 : 1;
            
            $interviewType = $interviewData['interview_type'] ?? 'preliminary';
            
            $stage = RecruitmentStage::create([
                'job_application_id' => $application->id,
                'stage_name' => $interviewType . '-interview',
                'stage_order' => $nextOrder,
                'status' => 'in-progress',
                'started_at' => now(),
            ]);
            
            \Log::info("Created new interview stage {$stage->id} for application {$application->id}");
        }
        
        $interviewData['job_application_id'] = $application->id;
        $interviewData['recruitment_stage_id'] = $stage->id;
        $interviewData['created_by'] = auth()->id();
        $interviewData['status'] = $interviewData['status'] ?? 'scheduled';
        
        // Auto-generate Jitsi link if video-call and no link provided
        if (($interviewData['meeting_type'] ?? '') === 'video-call' && empty($interviewData['meeting_link'])) {
            $roomName = 'interview-' . Str::random(12);
            $interviewData['meeting_link'] = 'https://meet.jit.si/' . $roomName;
        }
        
        // Convert interviewer_ids to JSON if it's an array
        if (isset($interviewData['interviewer_ids']) && is_array($interviewData['interviewer_ids'])) {
            $interviewData['interviewer_ids'] = json_encode($interviewData['interviewer_ids']);
        }
        
        $interview = InterviewSchedule::create($interviewData);
        
        \Log::info("Interview {$interview->id} scheduled and linked to stage {$stage->id}");
        
        // Send notification
        try {
            Mail::to($application->email)->send(new InterviewScheduledMail($interview));
            \Log::info("Interview schedule email sent to {$application->email}");
        } catch (\Exception $e) {
            \Log::error("Failed to send interview schedule email: " . $e->getMessage());
        }
        
        return $interview;
    }
    
    /**
     * Complete a manual stage (like screening, document verification).
     */
    public function completeStage(RecruitmentStage $stage, string $status, ?float $score = null, ?string $notes = null): void
    {
        if (!in_array($status, ['passed', 'failed', 'skipped'])) {
            throw new \InvalidArgumentException("Invalid status: {$status}");
        }
        
        $stage->update([
            'status' => $status,
            'score' => $score,
            'notes' => $notes,
            'completed_at' => now(),
        ]);
        
        \Log::info("Stage {$stage->id} marked as {$status}");
        
        // If passed, start next stage
        if ($status === 'passed') {
            $this->startNextStage($stage->jobApplication);
        } elseif ($status === 'failed') {
            // Auto-reject if failed
            $stage->jobApplication->update([
                'status' => 'rejected',
                'notes' => "Failed at {$stage->getStageNameLabel()}"
            ]);
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
            
            \Log::info("Auto-started next stage: {$nextStage->stage_name} (ID: {$nextStage->id})");
            
            // Dispatch event
            event(new StageStarted($nextStage));
        } else {
            // All stages completed - check acceptance
            $this->checkApplicationCompletion($application);
        }
    }
    
    /**
     * Check if all stages completed and application should be accepted.
     */
    public function checkApplicationCompletion(JobApplication $application): void
    {
        $allStages = $application->recruitmentStages;
        
        if ($allStages->isEmpty()) {
            return;
        }
        
        // Check if all stages passed
        $allPassed = $allStages->every(fn($s) => $s->status === 'passed');
        
        if ($allPassed) {
            $application->update([
                'status' => 'accepted',
                'notes' => 'All recruitment stages passed successfully!'
            ]);
            
            \Log::info("Application {$application->id} AUTO-ACCEPTED - all stages passed!");
        }
    }
}
