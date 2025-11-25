<?php

/**
 * Test Script for Advanced Recruitment System
 * 
 * This script will:
 * 1. Create test job vacancy
 * 2. Create test job application
 * 3. Create test interview scheduled for 24h from now
 * 4. Test reminder command
 * 5. Test email queue
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\JobVacancy;
use App\Models\JobApplication;
use App\Models\InterviewSchedule;
use App\Models\TestTemplate;
use App\Models\TestSession;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║   RECRUITMENT SYSTEM - AUTOMATED TEST SCRIPT              ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

// ==================================================
// 1. GET EXISTING JOB VACANCY & APPLICATION
// ==================================================
echo "📝 [1/7] Getting existing job application...\n";

$application = JobApplication::whereIn('status', ['review', 'shortlisted', 'pending', 'reviewed'])
    ->first();

if (!$application) {
    echo "   ❌ No job application found.\n";
    echo "   ℹ️  Please create an application first via the public form.\n";
    exit(1);
}

// Get vacancy from application
$vacancy = $application->jobVacancy;

echo "   ✅ Job Application found: ID {$application->id} - {$application->full_name}\n";
echo "   📋 For vacancy: {$vacancy->title}\n\n";

// ==================================================
// 2. SKIP (merged with step 1)
// ==================================================
echo "📝 [2/7] Skipped (merged with step 1)...\n\n";

// ==================================================
// 3. CREATE TEST INTERVIEW (24h from now)
// ==================================================
echo "📝 [3/7] Creating test interview scheduled for 24h from now...\n";

// Delete old test interviews
InterviewSchedule::where('interview_type', 'preliminary')->where('notes', 'like', '%test%')->delete();

$interviewDate = now()->addHours(24);

$interview = InterviewSchedule::create([
    'job_application_id' => $application->id,
    'scheduled_at' => $interviewDate,
    'interview_type' => 'preliminary',
    'interview_stage' => 1,
    'duration_minutes' => 60,
    'location' => 'Video Conference',
    'meeting_type' => 'video-call',
    'meeting_link' => 'https://meet.jit.si/test-interview-' . time(),
    'interviewer_ids' => json_encode([1]), // Assuming user ID 1 exists
    'notes' => 'This is a test interview for reminder testing',
    'status' => 'scheduled',
    'access_token' => Str::random(64),
]);

echo "   ✅ Interview created: ID {$interview->id}\n";
echo "   📅 Scheduled for: {$interviewDate->format('Y-m-d H:i:s')}\n";
echo "   ⏰ Time until interview: {$interviewDate->diffForHumans()}\n\n";

// ==================================================
// 4. TEST REMINDER COMMAND (Dry Run)
// ==================================================
echo "📝 [4/7] Testing reminder command...\n";

$startTime = now()->addHours(23);
$endTime = now()->addHours(25);

$reminderInterviews = InterviewSchedule::with(['jobApplication.jobVacancy'])
    ->where('status', 'scheduled')
    ->whereBetween('scheduled_at', [$startTime, $endTime])
    ->whereNull('reminder_sent_at')
    ->get();

echo "   🔍 Interviews found in 23-25h window: {$reminderInterviews->count()}\n";

if ($reminderInterviews->count() > 0) {
    foreach ($reminderInterviews as $reminderInterview) {
        echo "      - Interview ID {$reminderInterview->id} for {$reminderInterview->jobApplication->full_name}\n";
    }
    echo "   ✅ Reminder command will send {$reminderInterviews->count()} email(s)\n\n";
} else {
    echo "   ⚠️  No interviews found in reminder window\n\n";
}

// ==================================================
// 5. CREATE TEST TEMPLATE
// ==================================================
echo "📝 [5/7] Creating test template...\n";

$template = TestTemplate::firstOrCreate(
    ['title' => 'Basic Programming Test (TEST)'],
    [
        'description' => 'Test template for recruitment system testing',
        'test_type' => 'technical',
        'duration_minutes' => 60,
        'passing_score' => 70,
        'questions' => json_encode([
            'questions' => [
                [
                    'id' => 1,
                    'question' => 'What is PHP?',
                    'type' => 'multiple_choice',
                    'options' => ['A programming language', 'A database', 'An OS', 'A framework'],
                    'correct_answer' => 'A programming language',
                    'points' => 10
                ],
                [
                    'id' => 2,
                    'question' => 'Laravel is a PHP framework',
                    'type' => 'true_false',
                    'correct_answer' => 'true',
                    'points' => 10
                ]
            ]
        ]),
        'is_active' => true,
    ]
);

echo "   ✅ Test Template created: ID {$template->id} - {$template->title}\n\n";

// ==================================================
// 6. CREATE TEST SESSION
// ==================================================
echo "📝 [6/7] Creating test session...\n";

$testSession = TestSession::firstOrCreate(
    [
        'test_template_id' => $template->id,
        'job_application_id' => $application->id,
    ],
    [
        'session_token' => Str::random(64),
        'starts_at' => now(),
        'expires_at' => now()->addDays(3),
        'status' => 'pending',
    ]
);

echo "   ✅ Test Session created: ID {$testSession->id}\n";
echo "   📅 Expires: {$testSession->expires_at->format('Y-m-d H:i:s')}\n";
echo "   🔗 Token: " . substr($testSession->session_token, 0, 20) . "...\n\n";

// ==================================================
// 7. QUEUE STATUS
// ==================================================
echo "📝 [7/7] Checking queue status...\n";

$queueJobs = DB::table('jobs')->count();
$failedJobs = DB::table('failed_jobs')->count();

echo "   📊 Pending jobs in queue: {$queueJobs}\n";
echo "   ❌ Failed jobs: {$failedJobs}\n\n";

// ==================================================
// SUMMARY
// ==================================================
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║   TEST DATA CREATION COMPLETE                              ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

echo "📊 SUMMARY:\n";
echo "   ✅ Job Vacancy ID: {$vacancy->id}\n";
echo "   ✅ Job Application ID: {$application->id}\n";
echo "   ✅ Interview ID: {$interview->id}\n";
echo "   ✅ Test Template ID: {$template->id}\n";
echo "   ✅ Test Session ID: {$testSession->id}\n";
echo "\n";

echo "🧪 MANUAL TESTS YOU CAN RUN:\n";
echo "\n";
echo "1. Test Reminder Command:\n";
echo "   php artisan interviews:send-reminders\n";
echo "\n";
echo "2. Access Candidate Interview Portal:\n";
echo "   http://your-domain.com/interview/{$interview->access_token}\n";
echo "\n";
echo "3. Access Candidate Test Portal:\n";
echo "   http://your-domain.com/test/{$testSession->session_token}\n";
echo "\n";
echo "4. Check Queue Processing:\n";
echo "   php artisan queue:work --once\n";
echo "\n";
echo "5. Check Failed Jobs:\n";
echo "   php artisan queue:failed\n";
echo "\n";

echo "✨ All test data created successfully!\n";
echo "\n";
