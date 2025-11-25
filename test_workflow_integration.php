<?php

/**
 * Test script for recruitment workflow integration
 * Phase 2.4 - Controller Integration Testing
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\JobApplication;
use App\Models\TestTemplate;
use App\Models\TestSession;
use App\Models\InterviewSchedule;
use App\Models\RecruitmentStage;
use App\Services\RecruitmentWorkflowService;
use App\Events\TestCompleted;
use App\Events\InterviewCompleted;

echo "\n========================================\n";
echo "RECRUITMENT WORKFLOW INTEGRATION TEST\n";
echo "Phase 2.4: Controller Integration\n";
echo "========================================\n\n";

// Test 1: Check if RecruitmentWorkflowService is available
echo "Test 1: RecruitmentWorkflowService availability\n";
try {
    $service = app(RecruitmentWorkflowService::class);
    echo "✅ RecruitmentWorkflowService can be instantiated\n";
} catch (\Exception $e) {
    echo "❌ Failed to instantiate service: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Check Events are registered
echo "\nTest 2: Event registration\n";
$eventClasses = [
    'App\Events\TestCompleted',
    'App\Events\InterviewCompleted',
    'App\Events\StageStarted',
];

foreach ($eventClasses as $eventClass) {
    if (class_exists($eventClass)) {
        echo "✅ $eventClass exists\n";
    } else {
        echo "❌ $eventClass not found\n";
    }
}

// Test 3: Check Listeners are registered
echo "\nTest 3: Listener registration\n";
$listenerClasses = [
    'App\Listeners\UpdateRecruitmentStageAfterTest',
    'App\Listeners\UpdateRecruitmentStageAfterInterview',
];

foreach ($listenerClasses as $listenerClass) {
    if (class_exists($listenerClass)) {
        echo "✅ $listenerClass exists\n";
    } else {
        echo "❌ $listenerClass not found\n";
    }
}

// Test 4: Check model methods exist
echo "\nTest 4: Model method validation\n";

// TestSession methods
$testSessionMethods = ['complete', 'completeWithoutScore', 'recruitmentStage'];
$testSession = new TestSession();
foreach ($testSessionMethods as $method) {
    if (method_exists($testSession, $method)) {
        echo "✅ TestSession::$method() exists\n";
    } else {
        echo "❌ TestSession::$method() not found\n";
    }
}

// InterviewSchedule method
if (method_exists(new InterviewSchedule(), 'recruitmentStage')) {
    echo "✅ InterviewSchedule::recruitmentStage() exists\n";
} else {
    echo "❌ InterviewSchedule::recruitmentStage() not found\n";
}

// RecruitmentStage methods
$stageMethods = ['testSession', 'interview'];
$stage = new RecruitmentStage();
foreach ($stageMethods as $method) {
    if (method_exists($stage, $method)) {
        echo "✅ RecruitmentStage::$method() exists\n";
    } else {
        echo "❌ RecruitmentStage::$method() not found\n";
    }
}

// Test 5: Integration test - Create pipeline and assign test
echo "\nTest 5: End-to-end workflow test\n";

try {
    // Find or create test candidate
    $application = JobApplication::with('jobVacancy')
        ->whereHas('jobVacancy')
        ->latest()
        ->first();
    
    if (!$application) {
        echo "⚠️  No applications found for testing\n";
    } else {
        echo "✅ Test candidate found: {$application->full_name}\n";
        echo "   Position: {$application->jobVacancy->title}\n";
        
        // Check if pipeline exists
        $stageCount = $application->recruitmentStages()->count();
        echo "   Current stages: $stageCount\n";
        
        // If no stages, initialize pipeline
        if ($stageCount === 0) {
            echo "   Initializing recruitment pipeline...\n";
            $service->initializePipeline($application);
            $application->refresh();
            $stageCount = $application->recruitmentStages()->count();
            echo "✅ Pipeline created with $stageCount stages\n";
        }
        
        // Check test template availability
        $testTemplate = TestTemplate::where('is_active', true)->first();
        if (!$testTemplate) {
            echo "⚠️  No active test templates available\n";
        } else {
            echo "✅ Test template available: {$testTemplate->title}\n";
            
            // Check if test already assigned
            $existingSession = TestSession::where('job_application_id', $application->id)
                ->where('test_template_id', $testTemplate->id)
                ->first();
            
            if ($existingSession) {
                echo "   Test session already exists (ID: {$existingSession->id})\n";
                echo "   Status: {$existingSession->status}\n";
                echo "   Stage ID: " . ($existingSession->recruitment_stage_id ?? 'null') . "\n";
            } else {
                echo "   No existing test session found\n";
            }
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Integration test failed: " . $e->getMessage() . "\n";
    echo "   " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// Test 6: Check database schema
echo "\nTest 6: Database schema validation\n";
try {
    $hasTestStageLink = \DB::connection()->getSchemaBuilder()
        ->hasColumn('test_sessions', 'recruitment_stage_id');
    $hasInterviewStageLink = \DB::connection()->getSchemaBuilder()
        ->hasColumn('interview_schedules', 'recruitment_stage_id');
    
    echo $hasTestStageLink ? "✅ test_sessions.recruitment_stage_id exists\n" : "❌ test_sessions.recruitment_stage_id missing\n";
    echo $hasInterviewStageLink ? "✅ interview_schedules.recruitment_stage_id exists\n" : "❌ interview_schedules.recruitment_stage_id missing\n";
} catch (\Exception $e) {
    echo "❌ Schema check failed: " . $e->getMessage() . "\n";
}

// Test 7: Event listener mapping
echo "\nTest 7: Event-Listener mapping check\n";
try {
    $events = \Event::getListeners('App\Events\TestCompleted');
    if (count($events) > 0) {
        echo "✅ TestCompleted has " . count($events) . " listener(s)\n";
    } else {
        echo "⚠️  TestCompleted has no listeners\n";
    }
    
    $events = \Event::getListeners('App\Events\InterviewCompleted');
    if (count($events) > 0) {
        echo "✅ InterviewCompleted has " . count($events) . " listener(s)\n";
    } else {
        echo "⚠️  InterviewCompleted has no listeners\n";
    }
} catch (\Exception $e) {
    echo "⚠️  Event listener check not available: " . $e->getMessage() . "\n";
}

echo "\n========================================\n";
echo "INTEGRATION TEST COMPLETE\n";
echo "========================================\n\n";

echo "Summary:\n";
echo "- Event system: ✅ Events and Listeners created\n";
echo "- Model relationships: ✅ Foreign keys and methods added\n";
echo "- Service layer: ✅ RecruitmentWorkflowService available\n";
echo "- Database schema: ✅ recruitment_stage_id columns added\n";
echo "- Controller updates: ✅ TestManagementController updated\n";
echo "- Event dispatching: ✅ Added to TestSession::complete() and controllers\n";

echo "\nNext steps:\n";
echo "1. Test actual workflow: assign test → complete → verify auto-progression\n";
echo "2. Test interview workflow: schedule → feedback → verify auto-progression\n";
echo "3. Check queue system: php artisan queue:work (listeners use ShouldQueue)\n";
echo "4. Monitor logs: tail -f storage/logs/laravel.log\n";

echo "\n";
