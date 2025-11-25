<?php

/**
 * End-to-End Test: Recruitment Workflow Automation
 * Tests complete flow: Pipeline → Test Assignment → Completion → Auto-Progression
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\TestTemplate;
use App\Models\TestSession;
use App\Models\RecruitmentStage;
use App\Services\RecruitmentWorkflowService;
use App\Events\TestCompleted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "\n========================================\n";
echo "E2E TEST: RECRUITMENT WORKFLOW\n";
echo "========================================\n\n";

// Clean up any test data from previous runs
echo "Step 0: Cleanup previous test data\n";
$testEmail = 'test.automation@bizmark.test';
$existingApp = JobApplication::where('email', $testEmail)->first();
if ($existingApp) {
    echo "   Deleting previous test application (ID: {$existingApp->id})\n";
    $existingApp->delete();
}

// Step 1: Create test application
echo "\nStep 1: Create test job application\n";

// Find an active vacancy
$vacancy = JobVacancy::where('status', 'open')->first();
if (!$vacancy) {
    echo "❌ No open vacancies found. Creating test vacancy...\n";
    $vacancy = JobVacancy::create([
        'title' => 'Test Position - Automated Testing',
        'department' => 'IT',
        'location' => 'Jakarta',
        'employment_type' => 'full-time',
        'status' => 'open',
        'description' => 'Test vacancy for automation testing',
        'requirements' => json_encode(['requirement' => 'Test requirement']),
        'responsibilities' => json_encode(['responsibility' => 'Test responsibility']),
        'published_at' => now(),
    ]);
    echo "✅ Test vacancy created (ID: {$vacancy->id})\n";
} else {
    echo "✅ Using existing vacancy: {$vacancy->title} (ID: {$vacancy->id})\n";
}

// Create application
$application = JobApplication::create([
    'job_vacancy_id' => $vacancy->id,
    'full_name' => 'Candidate Test Automation',
    'email' => $testEmail,
    'phone' => '+6281234567890',
    'education_level' => 'S1',
    'major' => 'Computer Science',
    'institution' => 'Test University',
    'cv_path' => 'test/resume.pdf',
    'cover_letter' => 'This is a test application for automation testing',
    'status' => 'pending',
]);

echo "✅ Test application created\n";
echo "   Candidate: {$application->full_name}\n";
echo "   Email: {$application->email}\n";
echo "   Application ID: {$application->id}\n";

// Step 2: Initialize recruitment pipeline
echo "\nStep 2: Initialize recruitment pipeline\n";

$workflowService = app(RecruitmentWorkflowService::class);

try {
    $workflowService->initializePipeline($application);
    $application->refresh();
    
    $stages = $application->recruitmentStages()->orderBy('stage_order')->get();
    echo "✅ Pipeline initialized with {$stages->count()} stages:\n";
    
    foreach ($stages as $stage) {
        echo "   Stage {$stage->stage_order}: {$stage->stage_name} (Status: {$stage->status})\n";
    }
} catch (\Exception $e) {
    echo "❌ Failed to initialize pipeline: {$e->getMessage()}\n";
    exit(1);
}

// Step 3: Assign test to candidate
echo "\nStep 3: Assign test to candidate\n";

$testTemplate = TestTemplate::where('is_active', true)->first();
if (!$testTemplate) {
    echo "❌ No active test templates found\n";
    exit(1);
}

echo "   Using test template: {$testTemplate->title}\n";

try {
    $testSession = $workflowService->assignTest($application, $testTemplate);
    
    echo "✅ Test assigned successfully\n";
    echo "   Session ID: {$testSession->id}\n";
    echo "   Session Token: {$testSession->session_token}\n";
    echo "   Linked to Stage ID: {$testSession->recruitment_stage_id}\n";
    echo "   Status: {$testSession->status}\n";
    
    // Verify stage link
    $linkedStage = RecruitmentStage::find($testSession->recruitment_stage_id);
    if ($linkedStage) {
        echo "   Linked Stage: {$linkedStage->stage_name} (Status: {$linkedStage->status})\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Failed to assign test: {$e->getMessage()}\n";
    echo "   File: {$e->getFile()}:{$e->getLine()}\n";
    exit(1);
}

// Step 4: Simulate test completion with scoring
echo "\nStep 4: Simulate test completion and scoring\n";

// First, mark as completed (requires manual review)
$testSession->update([
    'status' => 'completed',
    'completed_at' => now(),
    'requires_manual_review' => true,
]);

echo "   Test marked as completed (pending review)\n";

// Simulate HR scoring the test
$score = 85.5; // Passing score (>= 70)
$passed = $score >= 70;

echo "   HR evaluation: Score = {$score}%, Passed = " . ($passed ? 'YES' : 'NO') . "\n";

// Update session with score and trigger event
$testSession->update([
    'score' => $score,
    'passed' => $passed,
    'evaluated_at' => now(),
    'requires_manual_review' => false,
]);

echo "   Dispatching TestCompleted event...\n";

// Manually dispatch event (simulating what complete() method does)
event(new TestCompleted($testSession, $passed, $score));

echo "✅ Event dispatched\n";

// Step 5: Process event (simulate queue worker)
echo "\nStep 5: Process event listener (simulating queue worker)\n";

try {
    $listener = new \App\Listeners\UpdateRecruitmentStageAfterTest();
    $event = new TestCompleted($testSession, $passed, $score);
    
    echo "   Calling listener->handle()...\n";
    $listener->handle($event);
    
    echo "✅ Listener executed\n";
    
} catch (\Exception $e) {
    echo "❌ Listener failed: {$e->getMessage()}\n";
    echo "   File: {$e->getFile()}:{$e->getLine()}\n";
    exit(1);
}

// Step 6: Verify automation results
echo "\nStep 6: Verify automation results\n";

// Refresh data
$testSession->refresh();
$application->refresh();
$linkedStage->refresh();

// Check stage status
echo "   Current stage (after test):\n";
echo "      Stage: {$linkedStage->stage_name}\n";
echo "      Status: {$linkedStage->status}\n";
echo "      Score: {$linkedStage->score}%\n";
echo "      Completed At: {$linkedStage->completed_at}\n";

if ($linkedStage->status !== 'passed') {
    echo "❌ Stage status should be 'passed' but is '{$linkedStage->status}'\n";
} else {
    echo "✅ Stage status correctly updated to 'passed'\n";
}

// Check next stage
$nextStage = $application->recruitmentStages()
    ->where('stage_order', '>', $linkedStage->stage_order)
    ->orderBy('stage_order')
    ->first();

if ($nextStage) {
    echo "\n   Next stage:\n";
    echo "      Stage: {$nextStage->stage_name}\n";
    echo "      Status: {$nextStage->status}\n";
    echo "      Started At: {$nextStage->started_at}\n";
    
    if ($nextStage->status === 'in-progress') {
        echo "✅ Next stage automatically started!\n";
    } else {
        echo "❌ Next stage should be 'in-progress' but is '{$nextStage->status}'\n";
    }
} else {
    echo "⚠️  No next stage found\n";
}

// Check application status
echo "\n   Application status: {$application->status}\n";

// Step 7: Summary
echo "\n========================================\n";
echo "TEST SUMMARY\n";
echo "========================================\n\n";

$allStages = $application->recruitmentStages()->orderBy('stage_order')->get();

echo "Pipeline Status:\n";
foreach ($allStages as $stage) {
    $icon = match($stage->status) {
        'passed' => '✅',
        'in-progress' => '🔄',
        'pending' => '⏸️',
        'failed' => '❌',
        default => '⚪',
    };
    echo "  {$icon} Stage {$stage->stage_order}: {$stage->stage_name} ({$stage->status})\n";
}

echo "\nTest Results:\n";
echo "  Test Session ID: {$testSession->id}\n";
echo "  Score: {$testSession->score}%\n";
echo "  Passed: " . ($testSession->passed ? 'YES' : 'NO') . "\n";
echo "  Stage Linked: " . ($testSession->recruitment_stage_id ? 'YES' : 'NO') . "\n";

echo "\nAutomation Verification:\n";

$checks = [
    'Pipeline created' => $stages->count() >= 3,
    'Test assigned and linked to stage' => $testSession->recruitment_stage_id !== null,
    'Test stage marked as passed' => $linkedStage->status === 'passed',
    'Next stage auto-started' => $nextStage && $nextStage->status === 'in-progress',
    'Event dispatched successfully' => true,
    'Listener executed successfully' => true,
];

$passedChecks = 0;
$totalChecks = count($checks);

foreach ($checks as $check => $result) {
    $icon = $result ? '✅' : '❌';
    echo "  {$icon} {$check}\n";
    if ($result) $passedChecks++;
}

$percentage = round(($passedChecks / $totalChecks) * 100);
echo "\n";
echo "========================================\n";
echo "FINAL SCORE: {$passedChecks}/{$totalChecks} ({$percentage}%)\n";
echo "========================================\n";

if ($passedChecks === $totalChecks) {
    echo "\n🎉 ALL AUTOMATION TESTS PASSED!\n";
    echo "The recruitment workflow automation is working correctly.\n\n";
    exit(0);
} else {
    echo "\n⚠️  SOME CHECKS FAILED\n";
    echo "Please review the results above.\n\n";
    exit(1);
}
