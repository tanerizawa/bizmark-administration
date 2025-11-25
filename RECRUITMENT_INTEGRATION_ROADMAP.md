# 🎯 Recruitment System Integration Roadmap

**Dokumen:** Analisis & Roadmap Integrasi Ekosistem Recruitment  
**Tanggal:** 24 November 2025  
**Tujuan:** Mengintegrasikan Test & Interview ke dalam satu ekosistem recruitment yang kohesif

---

## 📊 ANALISIS KONDISI SAAT INI

### ✅ Yang Sudah Ada (Good Foundation)

#### 1. **Database Structure** - SUDAH BAGUS ✅
```
job_applications (Kandidat)
├── recruitment_stages (Pipeline stages dengan order)
├── test_sessions (Test results dengan score)
└── interview_schedules (Interview jadwal & feedback)
```

**Relasi yang sudah ada:**
- ✅ `test_sessions.job_application_id` → Foreign key exists
- ✅ `interview_schedules.job_application_id` → Foreign key exists
- ✅ `recruitment_stages.job_application_id` → Foreign key exists

**Model Relationships:**
- ✅ `JobApplication::testSessions()` → hasMany
- ✅ `JobApplication::interviewSchedules()` → hasMany
- ✅ `JobApplication::recruitmentStages()` → hasMany

#### 2. **Core Features** - ADA TAPI BELUM INTEGRATED

| Feature | Status | Integration Level |
|---------|--------|------------------|
| Test Management | ✅ Working | 🟡 Standalone (60%) |
| Interview Scheduling | ✅ Working | 🟡 Standalone (60%) |
| Recruitment Pipeline | ✅ Working | 🟡 Basic (50%) |
| Candidate Dashboard | ✅ Working | 🔴 No Integration (30%) |

### ❌ GAP & MASALAH YANG DITEMUKAN

#### 1. **Disconnected Workflow** ⚠️ CRITICAL

**Masalah:**
```
Current Flow (MANUAL):
┌────────────────┐
│ Test Assigned  │ → HR manually creates test session
└────────────────┘
        ↓
┌────────────────┐
│ Candidate Takes│ → Test completed, score recorded
│ Test           │
└────────────────┘
        ↓
┌────────────────┐
│ HR Checks      │ → HR manually checks score
│ Score          │
└────────────────┘
        ↓
┌────────────────┐
│ HR Updates     │ → HR manually updates recruitment_stages
│ Pipeline       │
└────────────────┘
        ↓
┌────────────────┐
│ HR Schedules   │ → HR manually creates interview
│ Interview      │
└────────────────┘
```

**Problems:**
- ❌ Tidak ada auto-progression
- ❌ HR harus manual update di 3 tempat berbeda
- ❌ Prone to human error
- ❌ Inconsistent data
- ❌ Candidate tidak tahu status real-time

#### 2. **Missing Links** 🔗

**Test Session → Recruitment Stage:**
```sql
-- ❌ TIDAK ADA FIELD INI:
test_sessions.recruitment_stage_id (NULL)

-- Akibatnya:
- Test score tidak auto-sync ke recruitment_stages.score
- Test completion tidak auto-update recruitment_stages.status
- Tidak bisa track "test ini untuk stage mana?"
```

**Interview → Recruitment Stage:**
```sql
-- ❌ TIDAK ADA FIELD INI:
interview_schedules.recruitment_stage_id (NULL)

-- Akibatnya:
- Interview feedback tidak auto-sync ke pipeline
- Interview completion tidak trigger next stage
- Manual sync required
```

#### 3. **No Automation** 🤖

**Skenario yang HARUS otomatis tapi BELUM:**

| Event | Expected Automation | Current Reality |
|-------|-------------------|-----------------|
| Test Passed (score ≥ passing_score) | ✅ Auto-update recruitment_stage to 'passed'<br>✅ Auto-start next stage<br>✅ Auto-notify candidate | ❌ Manual update<br>❌ HR manually starts next stage<br>❌ No notification |
| Test Failed | ✅ Auto-update stage to 'failed'<br>✅ Auto-reject candidate<br>✅ Send rejection email | ❌ Manual update<br>❌ HR manually rejects<br>❌ Manual email |
| Interview Completed | ✅ Auto-request feedback from interviewer<br>✅ Auto-update pipeline based on feedback<br>✅ Auto-schedule next interview if passed | ❌ HR manually follows up<br>❌ Manual pipeline update<br>❌ Manual scheduling |
| All Stages Passed | ✅ Auto-mark as 'accepted'<br>✅ Generate offer letter<br>✅ Notify HR for next action | ❌ Manual status change<br>❌ No automation<br>❌ Prone to delays |

#### 4. **Fragmented UI/UX** 🖥️

**HR Experience (Scattered):**
```
For ONE candidate, HR must navigate to:
1. /admin/applications/{id} → See basic info
2. /admin/recruitment/tests → Create/check test
3. /admin/recruitment/interviews → Schedule interview
4. /admin/applications/{id} → Update status manually
5. Back to /admin/recruitment/tests → Check if candidate completed
```

**Candidate Experience (Confusing):**
```
Candidate receives:
1. Email: "Test assigned" → Link to test
2. Email: "Interview scheduled" → Calendar invite
3. (No single dashboard to see journey)
4. (No progress indicator)
5. (No visibility into next steps)
```

#### 5. **Data Integrity Issues** 📉

**Potential Inconsistencies:**
```php
// Example problematic scenario:
$application->status = 'interview'; // DB: interview
$currentStage = $application->currentStage(); // recruitment_stages: in-progress (psych-test)
$testSession = $application->testSessions()->latest()->first(); // status: completed

// THREE DIFFERENT SOURCES OF TRUTH! ❌
```

---

## 🎯 SOLUSI: UNIFIED RECRUITMENT ECOSYSTEM

### Vision Statement
> **"Satu kandidat, satu journey, satu dashboard"**
> 
> Setiap kandidat memiliki recruitment journey yang linear, trackable, dan fully automated dari apply hingga offer.

### Core Principles

1. **Single Source of Truth:** `recruitment_stages` adalah master timeline
2. **Event-Driven:** Setiap action trigger automation
3. **HR-Friendly:** Minimal manual intervention
4. **Candidate-Centric:** Full visibility dan guidance

---

## 📋 ROADMAP IMPLEMENTASI (5 PHASE)

---

## **PHASE 1: Database Integration** ⚙️

**Tujuan:** Link test/interview ke recruitment stages

**Duration:** 2-3 hari  
**Complexity:** Medium  
**Impact:** High (Foundation untuk semua automation)

### 1.1 Migration: Add Foreign Keys

**File:** `database/migrations/2025_11_25_000001_add_recruitment_stage_links.php`

```php
Schema::table('test_sessions', function (Blueprint $table) {
    $table->foreignId('recruitment_stage_id')
          ->nullable()
          ->after('job_application_id')
          ->constrained('recruitment_stages')
          ->nullOnDelete();
    
    $table->index('recruitment_stage_id');
});

Schema::table('interview_schedules', function (Blueprint $table) {
    $table->foreignId('recruitment_stage_id')
          ->nullable()
          ->after('job_application_id')
          ->constrained('recruitment_stages')
          ->nullOnDelete();
    
    $table->index('recruitment_stage_id');
});
```

### 1.2 Update Models

**app/Models/TestSession.php:**
```php
public function recruitmentStage()
{
    return $this->belongsTo(RecruitmentStage::class);
}
```

**app/Models/InterviewSchedule.php:**
```php
public function recruitmentStage()
{
    return $this->belongsTo(RecruitmentStage::class);
}
```

**app/Models/RecruitmentStage.php:**
```php
public function testSession()
{
    return $this->hasOne(TestSession::class);
}

public function interview()
{
    return $this->hasOne(InterviewSchedule::class);
}
```

### 1.3 Data Migration Script

**Purpose:** Link existing test/interview records ke stages

```php
// Script: link_existing_data.php
DB::transaction(function() {
    // Link test sessions
    $testSessions = TestSession::whereNull('recruitment_stage_id')->get();
    foreach ($testSessions as $session) {
        $stage = RecruitmentStage::where('job_application_id', $session->job_application_id)
                                 ->where('stage_name', 'LIKE', '%test%')
                                 ->first();
        if ($stage) {
            $session->update(['recruitment_stage_id' => $stage->id]);
        }
    }
    
    // Link interviews
    $interviews = InterviewSchedule::whereNull('recruitment_stage_id')->get();
    foreach ($interviews as $interview) {
        $stage = RecruitmentStage::where('job_application_id', $interview->job_application_id)
                                 ->where('stage_name', 'LIKE', '%interview%')
                                 ->first();
        if ($stage) {
            $interview->update(['recruitment_stage_id' => $stage->id]);
        }
    }
});
```

**Deliverables:**
- ✅ Migration file created
- ✅ Models updated with relationships
- ✅ Existing data linked
- ✅ Foreign key constraints working

---

## **PHASE 2: Pipeline Automation** 🤖

**Tujuan:** Auto-update stages based on test/interview results

**Duration:** 3-4 hari  
**Complexity:** High  
**Impact:** Very High (Core automation)

### 2.1 Create Event System

**app/Events/TestCompleted.php:**
```php
class TestCompleted
{
    public function __construct(
        public TestSession $session,
        public bool $passed,
        public float $score
    ) {}
}
```

**app/Events/InterviewCompleted.php:**
```php
class InterviewCompleted
{
    public function __construct(
        public InterviewSchedule $interview,
        public string $recommendation, // highly-recommended, recommended, not-recommended
        public float $overallRating
    ) {}
}
```

### 2.2 Create Listeners

**app/Listeners/UpdateRecruitmentStageAfterTest.php:**
```php
class UpdateRecruitmentStageAfterTest
{
    public function handle(TestCompleted $event): void
    {
        $session = $event->session;
        $stage = $session->recruitmentStage;
        
        if (!$stage) return;
        
        // Update stage
        $stage->update([
            'status' => $event->passed ? 'passed' : 'failed',
            'score' => $event->score,
            'completed_at' => now(),
            'notes' => "Test completed with score: {$event->score}%"
        ]);
        
        // If passed, start next stage
        if ($event->passed) {
            $this->startNextStage($session->jobApplication);
        } else {
            // Auto-reject application
            $session->jobApplication->update([
                'status' => 'rejected',
                'notes' => "Failed {$stage->stage_name} with score {$event->score}%"
            ]);
        }
    }
    
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
            
            // Dispatch event untuk trigger creation
            event(new StageStarted($nextStage));
        }
    }
}
```

**app/Listeners/UpdateRecruitmentStageAfterInterview.php:**
```php
class UpdateRecruitmentStageAfterInterview
{
    public function handle(InterviewCompleted $event): void
    {
        $interview = $event->interview;
        $stage = $interview->recruitmentStage;
        
        if (!$stage) return;
        
        $passed = in_array($event->recommendation, ['highly-recommended', 'recommended']);
        
        $stage->update([
            'status' => $passed ? 'passed' : 'failed',
            'score' => $event->overallRating * 20, // Convert 1-5 to 0-100
            'completed_at' => now(),
            'notes' => "Interview: {$event->recommendation}"
        ]);
        
        if ($passed) {
            $this->startNextStage($interview->jobApplication);
        } else {
            $interview->jobApplication->update([
                'status' => 'rejected',
                'notes' => "Did not pass interview: {$event->recommendation}"
            ]);
        }
    }
}
```

### 2.3 Create Workflow Service

**app/Services/RecruitmentWorkflowService.php:**
```php
class RecruitmentWorkflowService
{
    /**
     * Initialize recruitment pipeline for new application
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
    }
    
    /**
     * Get default stages based on vacancy requirements
     */
    private function getDefaultStages(JobVacancy $vacancy): array
    {
        $stages = ['screening']; // Always start with screening
        
        if ($vacancy->requires_psychology_test) {
            $stages[] = 'psychology-test';
        }
        
        if ($vacancy->requires_technical_test) {
            $stages[] = 'technical-test';
        }
        
        $stages[] = 'hr-interview';
        
        if ($vacancy->requires_user_interview) {
            $stages[] = 'user-interview';
        }
        
        $stages[] = 'final-interview';
        
        return $stages;
    }
    
    /**
     * Assign test to candidate for specific stage
     */
    public function assignTest(
        JobApplication $application, 
        TestTemplate $template,
        ?RecruitmentStage $stage = null
    ): TestSession {
        // If no stage provided, find current test stage
        $stage = $stage ?? $application->recruitmentStages()
                                      ->where('stage_name', 'LIKE', '%test%')
                                      ->where('status', 'in-progress')
                                      ->first();
        
        if (!$stage) {
            throw new \Exception('No active test stage found');
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
        
        // Send email notification
        Mail::to($application->email)->send(new TestAssignedMail($session));
        
        return $session;
    }
    
    /**
     * Schedule interview for candidate
     */
    public function scheduleInterview(
        JobApplication $application,
        array $interviewData,
        ?RecruitmentStage $stage = null
    ): InterviewSchedule {
        $stage = $stage ?? $application->recruitmentStages()
                                      ->where('stage_name', 'LIKE', '%interview%')
                                      ->where('status', 'in-progress')
                                      ->first();
        
        if (!$stage) {
            throw new \Exception('No active interview stage found');
        }
        
        $interviewData['recruitment_stage_id'] = $stage->id;
        
        $interview = InterviewSchedule::create($interviewData);
        
        // Send notification
        Mail::to($application->email)->send(new InterviewScheduledMail($interview));
        
        return $interview;
    }
    
    /**
     * Check if all stages completed and application should be accepted
     */
    public function checkCompletion(JobApplication $application): void
    {
        $allStages = $application->recruitmentStages;
        
        if ($allStages->every(fn($s) => $s->status === 'passed')) {
            $application->update(['status' => 'accepted']);
            
            // Trigger offer generation
            event(new ApplicationAccepted($application));
        }
    }
}
```

### 2.4 Update Controllers

**TestManagementController::store() - ADD:**
```php
public function store(Request $request)
{
    // ... existing validation ...
    
    // Use workflow service
    $workflowService = app(RecruitmentWorkflowService::class);
    
    $application = JobApplication::findOrFail($request->job_application_id);
    $template = TestTemplate::findOrFail($request->test_template_id);
    
    // This automatically links to recruitment stage
    $session = $workflowService->assignTest($application, $template);
    
    return redirect()->route('admin.recruitment.tests.index')
                    ->with('success', 'Test berhasil di-assign dan kandidat telah dinotifikasi.');
}
```

**InterviewScheduleController::store() - UPDATE:**
```php
public function store(Request $request)
{
    // ... existing validation ...
    
    $workflowService = app(RecruitmentWorkflowService::class);
    $application = JobApplication::findOrFail($request->job_application_id);
    
    $interview = $workflowService->scheduleInterview($application, $validated);
    
    return redirect()->route('admin.recruitment.interviews.show', $interview)
                    ->with('success', 'Interview dijadwalkan dan terintegrasi dengan pipeline.');
}
```

**Deliverables:**
- ✅ Event & Listener system created
- ✅ RecruitmentWorkflowService implemented
- ✅ Controllers updated to use workflow
- ✅ Auto-progression working
- ✅ Email notifications sent

---

## **PHASE 3: Unified Candidate Dashboard** 📱

**Tujuan:** Single page untuk kandidat track progress

**Duration:** 2-3 hari  
**Complexity:** Medium  
**Impact:** High (Candidate experience)

### 3.1 Create Candidate Portal Route

**routes/web.php:**
```php
Route::prefix('candidate')->name('candidate.')->group(function() {
    Route::get('/dashboard/{token}', [CandidatePortalController::class, 'dashboard'])
         ->name('dashboard');
    Route::get('/test/{token}', [CandidatePortalController::class, 'takeTest'])
         ->name('test');
    Route::post('/test/{token}/submit', [CandidatePortalController::class, 'submitTest'])
         ->name('test.submit');
});
```

### 3.2 Create Candidate Portal Controller

**app/Http/Controllers/CandidatePortalController.php:**
```php
class CandidatePortalController extends Controller
{
    public function dashboard($token)
    {
        // Token bisa dari email link
        $application = JobApplication::where('email_verification_token', $token)
                                     ->with(['recruitmentStages', 'testSessions', 'interviewSchedules'])
                                     ->firstOrFail();
        
        $journey = $this->buildJourneyTimeline($application);
        
        return view('candidate.dashboard', compact('application', 'journey'));
    }
    
    private function buildJourneyTimeline(JobApplication $application): array
    {
        $timeline = [];
        
        foreach ($application->recruitmentStages as $stage) {
            $item = [
                'stage_name' => $stage->stage_name,
                'status' => $stage->status,
                'started_at' => $stage->started_at,
                'completed_at' => $stage->completed_at,
                'score' => $stage->score,
            ];
            
            // Add test details if applicable
            if ($stage->testSession) {
                $item['test'] = [
                    'title' => $stage->testSession->testTemplate->title,
                    'status' => $stage->testSession->status,
                    'link' => $stage->testSession->status === 'pending' 
                        ? route('candidate.test', $stage->testSession->session_token)
                        : null,
                    'expires_at' => $stage->testSession->expires_at,
                    'score' => $stage->testSession->score,
                ];
            }
            
            // Add interview details if applicable
            if ($stage->interview) {
                $item['interview'] = [
                    'scheduled_at' => $stage->interview->scheduled_at,
                    'meeting_type' => $stage->interview->meeting_type,
                    'meeting_link' => $stage->interview->meeting_link,
                    'status' => $stage->interview->status,
                ];
            }
            
            $timeline[] = $item;
        }
        
        return $timeline;
    }
}
```

### 3.3 Create Candidate Dashboard View

**resources/views/candidate/dashboard.blade.php:**
```blade
<div class="recruitment-journey">
    <h1>Your Recruitment Journey</h1>
    <p>Position: {{ $application->jobVacancy->title }}</p>
    
    <div class="progress-bar">
        <div class="progress" style="width: {{ $application->getOverallProgress() }}%"></div>
    </div>
    <p class="text-center">{{ $application->getOverallProgress() }}% Complete</p>
    
    <div class="timeline">
        @foreach($journey as $item)
            <div class="timeline-item status-{{ $item['status'] }}">
                <div class="timeline-icon">
                    @if($item['status'] === 'passed')
                        <i class="fas fa-check-circle text-green"></i>
                    @elseif($item['status'] === 'in-progress')
                        <i class="fas fa-spinner fa-spin text-blue"></i>
                    @elseif($item['status'] === 'failed')
                        <i class="fas fa-times-circle text-red"></i>
                    @else
                        <i class="fas fa-circle text-gray"></i>
                    @endif
                </div>
                
                <div class="timeline-content">
                    <h3>{{ ucfirst(str_replace('-', ' ', $item['stage_name'])) }}</h3>
                    
                    @if($item['status'] === 'in-progress' && isset($item['test']))
                        <div class="alert alert-info">
                            <p><strong>Action Required:</strong> {{ $item['test']['title'] }}</p>
                            @if($item['test']['link'])
                                <a href="{{ $item['test']['link'] }}" class="btn btn-primary">
                                    Start Test
                                </a>
                                <p class="text-sm">Expires: {{ $item['test']['expires_at']->format('d M Y, H:i') }}</p>
                            @endif
                        </div>
                    @endif
                    
                    @if($item['status'] === 'in-progress' && isset($item['interview']))
                        <div class="alert alert-info">
                            <p><strong>Scheduled:</strong> {{ $item['interview']['scheduled_at']->format('d M Y, H:i') }}</p>
                            <p>Type: {{ ucfirst($item['interview']['meeting_type']) }}</p>
                            @if($item['interview']['meeting_link'])
                                <a href="{{ $item['interview']['meeting_link'] }}" class="btn btn-primary">
                                    Join Meeting
                                </a>
                            @endif
                        </div>
                    @endif
                    
                    @if($item['status'] === 'passed')
                        <p class="text-green">
                            <i class="fas fa-check"></i> Completed
                            @if($item['score'])
                                (Score: {{ $item['score'] }}%)
                            @endif
                        </p>
                    @endif
                    
                    @if($item['status'] === 'pending')
                        <p class="text-gray">Waiting for previous stage to complete</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    
    <div class="overall-status mt-6">
        @if($application->status === 'accepted')
            <div class="alert alert-success">
                <h2>🎉 Congratulations!</h2>
                <p>You have successfully passed all stages. Our HR team will contact you soon for the next steps.</p>
            </div>
        @elseif($application->status === 'rejected')
            <div class="alert alert-danger">
                <p>Unfortunately, your application was not successful at this time. Thank you for your interest.</p>
            </div>
        @endif
    </div>
</div>
```

**Deliverables:**
- ✅ Candidate portal controller
- ✅ Dashboard view with timeline
- ✅ Test/interview integration
- ✅ Progress tracking
- ✅ Action buttons (Start Test, Join Interview)

---

## **PHASE 4: HR Unified Dashboard** 🎛️

**Tujuan:** Single page untuk HR manage kandidat dari apply hingga offer

**Duration:** 3-4 hari  
**Complexity:** High  
**Impact:** Very High (HR productivity)

### 4.1 Enhanced Application Detail Page

**app/Http/Controllers/Admin/JobApplicationController.php - UPDATE show():**
```php
public function show($id)
{
    $application = JobApplication::with([
        'jobVacancy',
        'reviewer',
        'recruitmentStages.testSession.testTemplate',
        'recruitmentStages.interview',
        'testSessions' => fn($q) => $q->latest(),
        'interviewSchedules' => fn($q) => $q->latest(),
    ])->findOrFail($id);
    
    // Calculate stage statistics
    $stageStats = [
        'total' => $application->recruitmentStages->count(),
        'completed' => $application->recruitmentStages->whereIn('status', ['passed', 'failed'])->count(),
        'in_progress' => $application->recruitmentStages->where('status', 'in-progress')->count(),
        'pending' => $application->recruitmentStages->where('status', 'pending')->count(),
    ];
    
    // Get available test templates and interviewers for quick actions
    $testTemplates = TestTemplate::where('is_active', true)->get();
    $interviewers = User::where('is_active', true)->get();
    
    return view('admin.applications.show', compact(
        'application', 
        'stageStats',
        'testTemplates',
        'interviewers'
    ));
}
```

### 4.2 Create Unified Application View

**resources/views/admin/applications/show.blade.php - NEW SECTIONS:**

```blade
{{-- SECTION 1: Application Overview --}}
<div class="grid grid-cols-3 gap-4">
    <div class="card">
        <h3>Candidate Info</h3>
        <p><strong>Name:</strong> {{ $application->full_name }}</p>
        <p><strong>Email:</strong> {{ $application->email }}</p>
        <p><strong>Phone:</strong> {{ $application->phone }}</p>
        <p><strong>Applied:</strong> {{ $application->created_at->format('d M Y') }}</p>
    </div>
    
    <div class="card">
        <h3>Position</h3>
        <p><strong>Title:</strong> {{ $application->jobVacancy->title }}</p>
        <p><strong>Department:</strong> {{ $application->jobVacancy->department }}</p>
        <p><strong>Status:</strong> 
            <span class="badge badge-{{ $application->status }}">
                {{ $application->status_label }}
            </span>
        </p>
    </div>
    
    <div class="card">
        <h3>Pipeline Progress</h3>
        <div class="progress-bar">
            <div class="progress" style="width: {{ $application->getOverallProgress() }}%"></div>
        </div>
        <p class="text-center mt-2">{{ $application->getOverallProgress() }}% Complete</p>
        <div class="grid grid-cols-3 gap-2 mt-3 text-xs">
            <div>
                <p class="font-semibold text-green">{{ $stageStats['completed'] }}</p>
                <p class="text-gray">Completed</p>
            </div>
            <div>
                <p class="font-semibold text-blue">{{ $stageStats['in_progress'] }}</p>
                <p class="text-gray">In Progress</p>
            </div>
            <div>
                <p class="font-semibold text-gray">{{ $stageStats['pending'] }}</p>
                <p class="text-gray">Pending</p>
            </div>
        </div>
    </div>
</div>

{{-- SECTION 2: Recruitment Pipeline Timeline --}}
<div class="card mt-4">
    <div class="flex justify-between items-center mb-4">
        <h3>Recruitment Pipeline</h3>
        <div class="flex gap-2">
            <button onclick="openAssignTestModal()" class="btn btn-sm btn-primary">
                <i class="fas fa-clipboard-check"></i> Assign Test
            </button>
            <button onclick="openScheduleInterviewModal()" class="btn btn-sm btn-primary">
                <i class="fas fa-calendar-plus"></i> Schedule Interview
            </button>
        </div>
    </div>
    
    <div class="pipeline-timeline">
        @foreach($application->recruitmentStages as $stage)
            <div class="pipeline-stage status-{{ $stage->status }}">
                <div class="stage-header">
                    <div class="flex items-center gap-2">
                        <div class="stage-icon">
                            @if($stage->status === 'passed')
                                <i class="fas fa-check-circle text-green"></i>
                            @elseif($stage->status === 'failed')
                                <i class="fas fa-times-circle text-red"></i>
                            @elseif($stage->status === 'in-progress')
                                <i class="fas fa-spinner fa-spin text-blue"></i>
                            @else
                                <i class="fas fa-circle text-gray"></i>
                            @endif
                        </div>
                        <div>
                            <h4>{{ ucfirst(str_replace('-', ' ', $stage->stage_name)) }}</h4>
                            <p class="text-xs text-gray">
                                Stage {{ $stage->stage_order }} of {{ $stageStats['total'] }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="stage-meta">
                        @if($stage->started_at)
                            <p class="text-xs">Started: {{ $stage->started_at->format('d M Y H:i') }}</p>
                        @endif
                        @if($stage->completed_at)
                            <p class="text-xs">Completed: {{ $stage->completed_at->format('d M Y H:i') }}</p>
                        @endif
                        @if($stage->score)
                            <p class="text-sm font-semibold">Score: {{ $stage->score }}%</p>
                        @endif
                    </div>
                </div>
                
                {{-- TEST DETAILS --}}
                @if($stage->testSession)
                    <div class="stage-detail test-detail">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold">{{ $stage->testSession->testTemplate->title }}</p>
                                <p class="text-sm text-gray">{{ $stage->testSession->testTemplate->test_type }}</p>
                            </div>
                            <span class="badge badge-{{ $stage->testSession->status }}">
                                {{ $stage->testSession->status }}
                            </span>
                        </div>
                        
                        @if($stage->testSession->status === 'completed')
                            <div class="mt-2 p-2 bg-gray-50 rounded">
                                <div class="grid grid-cols-3 gap-2 text-xs">
                                    <div>
                                        <p class="text-gray">Score</p>
                                        <p class="font-semibold text-lg">{{ $stage->testSession->score }}%</p>
                                    </div>
                                    <div>
                                        <p class="text-gray">Duration</p>
                                        <p class="font-semibold">{{ $stage->testSession->time_taken_minutes }} min</p>
                                    </div>
                                    <div>
                                        <p class="text-gray">Result</p>
                                        <p class="font-semibold {{ $stage->testSession->passed ? 'text-green' : 'text-red' }}">
                                            {{ $stage->testSession->passed ? 'PASSED' : 'FAILED' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('admin.recruitment.tests.show', $stage->testSession) }}" 
                               class="btn btn-sm btn-secondary mt-2">
                                View Full Results
                            </a>
                        @elseif($stage->testSession->status === 'pending')
                            <div class="mt-2 alert alert-info">
                                <p class="text-sm">Test assigned, waiting for candidate to start</p>
                                <p class="text-xs">Expires: {{ $stage->testSession->expires_at->format('d M Y H:i') }}</p>
                            </div>
                        @elseif($stage->testSession->status === 'in-progress')
                            <div class="mt-2 alert alert-warning">
                                <p class="text-sm">Candidate is currently taking the test</p>
                                <p class="text-xs">Started: {{ $stage->testSession->started_at->format('d M Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                @endif
                
                {{-- INTERVIEW DETAILS --}}
                @if($stage->interview)
                    <div class="stage-detail interview-detail">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold">{{ ucfirst($stage->interview->interview_type) }} Interview</p>
                                <p class="text-sm text-gray">
                                    <i class="fas fa-calendar"></i> 
                                    {{ $stage->interview->scheduled_at->format('d M Y, H:i') }}
                                </p>
                                <p class="text-sm text-gray">
                                    <i class="fas fa-{{ $stage->interview->meeting_type === 'video-call' ? 'video' : ($stage->interview->meeting_type === 'phone' ? 'phone' : 'map-marker-alt') }}"></i>
                                    {{ ucfirst(str_replace('-', ' ', $stage->interview->meeting_type)) }}
                                </p>
                            </div>
                            <span class="badge badge-{{ $stage->interview->status }}">
                                {{ $stage->interview->status }}
                            </span>
                        </div>
                        
                        @if($stage->interview->meeting_link)
                            <a href="{{ $stage->interview->meeting_link }}" target="_blank" 
                               class="btn btn-sm btn-primary mt-2">
                                <i class="fas fa-external-link-alt"></i> Join Meeting
                            </a>
                        @endif
                        
                        @if($stage->interview->status === 'completed' && $stage->interview->feedback)
                            <div class="mt-2 p-2 bg-gray-50 rounded">
                                <p class="text-xs text-gray">Feedback Summary</p>
                                <p class="font-semibold">Overall Rating: {{ $stage->interview->feedback->overall_rating }}/5</p>
                                <p class="text-sm">Recommendation: {{ ucfirst(str_replace('-', ' ', $stage->interview->feedback->recommendation)) }}</p>
                            </div>
                        @endif
                        
                        <a href="{{ route('admin.recruitment.interviews.show', $stage->interview) }}" 
                           class="btn btn-sm btn-secondary mt-2">
                            View Full Details
                        </a>
                    </div>
                @endif
                
                {{-- STAGE ACTIONS --}}
                @if($stage->status === 'in-progress')
                    <div class="stage-actions mt-3">
                        @if(str_contains($stage->stage_name, 'test') && !$stage->testSession)
                            <button onclick="assignTestToStage({{ $stage->id }})" 
                                    class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> Assign Test
                            </button>
                        @endif
                        
                        @if(str_contains($stage->stage_name, 'interview') && !$stage->interview)
                            <button onclick="scheduleInterviewForStage({{ $stage->id }})" 
                                    class="btn btn-sm btn-primary">
                                <i class="fas fa-calendar-plus"></i> Schedule Interview
                            </button>
                        @endif
                        
                        @if(in_array($stage->stage_name, ['screening', 'document-verification']))
                            <button onclick="completeStage({{ $stage->id }}, 'passed')" 
                                    class="btn btn-sm btn-success">
                                <i class="fas fa-check"></i> Mark as Passed
                            </button>
                            <button onclick="completeStage({{ $stage->id }}, 'failed')" 
                                    class="btn btn-sm btn-danger">
                                <i class="fas fa-times"></i> Mark as Failed
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

{{-- SECTION 3: Quick Actions Modals --}}
<div id="assignTestModal" class="modal">
    <div class="modal-content">
        <h3>Assign Test</h3>
        <form method="POST" action="{{ route('admin.recruitment.tests.assign') }}">
            @csrf
            <input type="hidden" name="job_application_id" value="{{ $application->id }}">
            <input type="hidden" name="recruitment_stage_id" id="test_stage_id">
            
            <div class="form-group">
                <label>Select Test Template</label>
                <select name="test_template_id" required>
                    @foreach($testTemplates as $template)
                        <option value="{{ $template->id }}">
                            {{ $template->title }} ({{ $template->test_type }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label>Valid Until</label>
                <input type="datetime-local" name="expires_at" 
                       value="{{ now()->addDays(7)->format('Y-m-d\TH:i') }}" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Assign Test</button>
            <button type="button" onclick="closeModal('assignTestModal')" class="btn btn-secondary">Cancel</button>
        </form>
    </div>
</div>

<div id="scheduleInterviewModal" class="modal">
    <div class="modal-content">
        <h3>Schedule Interview</h3>
        <form method="POST" action="{{ route('admin.recruitment.interviews.store') }}">
            @csrf
            <input type="hidden" name="job_application_id" value="{{ $application->id }}">
            <input type="hidden" name="recruitment_stage_id" id="interview_stage_id">
            
            <div class="form-group">
                <label>Interview Type</label>
                <select name="interview_type" required>
                    <option value="preliminary">Preliminary</option>
                    <option value="technical">Technical</option>
                    <option value="hr">HR</option>
                    <option value="final">Final</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Date & Time</label>
                <input type="datetime-local" name="scheduled_at" required>
            </div>
            
            <div class="form-group">
                <label>Duration (minutes)</label>
                <select name="duration_minutes" required>
                    <option value="30">30 minutes</option>
                    <option value="45">45 minutes</option>
                    <option value="60">60 minutes</option>
                    <option value="90">90 minutes</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Meeting Type</label>
                <select name="meeting_type" required>
                    <option value="video-call">Video Call</option>
                    <option value="phone">Phone</option>
                    <option value="in-person">In-Person</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Interviewers</label>
                <select name="interviewer_ids[]" multiple required style="height: 150px;">
                    @foreach($interviewers as $interviewer)
                        <option value="{{ $interviewer->id }}">
                            {{ $interviewer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Schedule Interview</button>
            <button type="button" onclick="closeModal('scheduleInterviewModal')" class="btn btn-secondary">Cancel</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openAssignTestModal() {
    // Auto-select current stage if it's a test stage
    const currentStage = {{ $application->currentStage()?->id ?? 'null' }};
    if (currentStage) {
        document.getElementById('test_stage_id').value = currentStage;
    }
    document.getElementById('assignTestModal').style.display = 'block';
}

function openScheduleInterviewModal() {
    const currentStage = {{ $application->currentStage()?->id ?? 'null' }};
    if (currentStage) {
        document.getElementById('interview_stage_id').value = currentStage;
    }
    document.getElementById('scheduleInterviewModal').style.display = 'block';
}

function assignTestToStage(stageId) {
    document.getElementById('test_stage_id').value = stageId;
    openAssignTestModal();
}

function scheduleInterviewForStage(stageId) {
    document.getElementById('interview_stage_id').value = stageId;
    openScheduleInterviewModal();
}

function completeStage(stageId, status) {
    if (!confirm(`Are you sure you want to mark this stage as ${status}?`)) return;
    
    fetch(`/admin/recruitment/stages/${stageId}/complete`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}
</script>
@endpush
```

**Deliverables:**
- ✅ Unified application detail page
- ✅ Embedded pipeline timeline
- ✅ Quick actions (Assign Test, Schedule Interview)
- ✅ Real-time status display
- ✅ Test/Interview integration in one view

---

## **PHASE 5: Advanced Automation & Analytics** 📊

**Tujuan:** Auto-notifications, reminders, dan analytics

**Duration:** 3-4 hari  
**Complexity:** Medium  
**Impact:** High (Operational efficiency)

### 5.1 Notification System

**app/Notifications/TestReminderNotification.php:**
```php
class TestReminderNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }
    
    public function toMail($notifiable): MailMessage
    {
        $session = $this->session;
        
        return (new MailMessage)
            ->subject('Reminder: Complete Your Test')
            ->greeting('Hello ' . $notifiable->full_name)
            ->line("This is a reminder to complete your {$session->testTemplate->title}.")
            ->line("Test expires in 24 hours: " . $session->expires_at->format('d M Y, H:i'))
            ->action('Start Test Now', route('candidate.test', $session->session_token))
            ->line('Good luck!');
    }
}
```

**Create Scheduled Command:**
```php
// app/Console/Commands/SendTestReminders.php
class SendTestReminders extends Command
{
    protected $signature = 'recruitment:send-test-reminders';
    
    public function handle()
    {
        $sessions = TestSession::where('status', 'pending')
                              ->where('expires_at', '>', now())
                              ->where('expires_at', '<=', now()->addDay())
                              ->whereDoesntHave('reminders') // Don't send multiple times
                              ->get();
        
        foreach ($sessions as $session) {
            $session->jobApplication->notify(new TestReminderNotification($session));
            
            // Mark as reminded
            $session->update(['reminder_sent_at' => now()]);
        }
        
        $this->info("Sent {$sessions->count()} test reminders");
    }
}

// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('recruitment:send-test-reminders')->dailyAt('09:00');
    $schedule->command('recruitment:send-interview-reminders')->dailyAt('08:00');
}
```

### 5.2 Analytics Dashboard

**app/Http/Controllers/Admin/RecruitmentAnalyticsController.php:**
```php
class RecruitmentAnalyticsController extends Controller
{
    public function index()
    {
        $data = [
            'funnel' => $this->getFunnelData(),
            'stage_performance' => $this->getStagePerformance(),
            'time_to_hire' => $this->getTimeToHire(),
            'test_performance' => $this->getTestPerformance(),
        ];
        
        return view('admin.recruitment.analytics', $data);
    }
    
    private function getFunnelData(): array
    {
        return [
            'total_applications' => JobApplication::count(),
            'screening_passed' => JobApplication::whereHas('recruitmentStages', function($q) {
                $q->where('stage_name', 'screening')->where('status', 'passed');
            })->count(),
            'test_completed' => JobApplication::whereHas('testSessions', function($q) {
                $q->where('status', 'completed');
            })->count(),
            'interviewed' => JobApplication::whereHas('interviewSchedules', function($q) {
                $q->where('status', 'completed');
            })->count(),
            'accepted' => JobApplication::where('status', 'accepted')->count(),
        ];
    }
    
    private function getStagePerformance(): Collection
    {
        return RecruitmentStage::selectRaw('
                stage_name,
                COUNT(*) as total,
                AVG(score) as avg_score,
                SUM(CASE WHEN status = "passed" THEN 1 ELSE 0 END) as passed_count,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_count
            ')
            ->groupBy('stage_name')
            ->get();
    }
    
    private function getTimeToHire(): float
    {
        $accepted = JobApplication::where('status', 'accepted')
                                  ->with('recruitmentStages')
                                  ->get();
        
        $totalDays = 0;
        foreach ($accepted as $app) {
            $firstStage = $app->recruitmentStages->first();
            $lastStage = $app->recruitmentStages->last();
            
            if ($firstStage && $lastStage && $lastStage->completed_at) {
                $totalDays += $firstStage->started_at->diffInDays($lastStage->completed_at);
            }
        }
        
        return $accepted->count() > 0 ? round($totalDays / $accepted->count(), 1) : 0;
    }
}
```

**Deliverables:**
- ✅ Auto-reminders for pending tests
- ✅ Interview reminder emails
- ✅ Analytics dashboard
- ✅ Funnel visualization
- ✅ Stage performance metrics
- ✅ Time-to-hire calculation

---

## 🎯 IMPLEMENTATION PRIORITY

### High Priority (Must Have)
1. ✅ **Phase 1** - Database Integration (Foundation)
2. ✅ **Phase 2** - Pipeline Automation (Core value)
3. ✅ **Phase 4** - HR Unified Dashboard (Productivity)

### Medium Priority (Should Have)
4. ✅ **Phase 3** - Candidate Dashboard (UX improvement)

### Low Priority (Nice to Have)
5. ✅ **Phase 5** - Analytics (Insights)

---

## 📈 EXPECTED OUTCOMES

### Quantitative Benefits

| Metric | Before | After | Improvement |
|--------|--------|-------|-------------|
| Time to update candidate status | 5-10 min | <30 sec | 90% reduction |
| Manual tasks per candidate | 8-12 | 2-3 | 75% reduction |
| Status inconsistency rate | 15-20% | <2% | 90% reduction |
| HR hours per week on admin | 10-15 hrs | 2-3 hrs | 80% reduction |
| Candidate response time | 24-48 hrs | <2 hrs | 95% faster |

### Qualitative Benefits

**For HR:**
- ✅ Single dashboard per candidate
- ✅ No manual status updates
- ✅ Clear visibility into pipeline
- ✅ Automated notifications
- ✅ Less context switching

**For Candidates:**
- ✅ Clear journey visibility
- ✅ Know what to do next
- ✅ No need to email HR for status
- ✅ Professional experience
- ✅ Faster response times

**For Management:**
- ✅ Real-time analytics
- ✅ Bottleneck identification
- ✅ Data-driven decisions
- ✅ Audit trail
- ✅ Process optimization

---

## ⚠️ RISKS & MITIGATION

### Technical Risks

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| Data migration errors | High | Medium | Extensive testing, backup before migration |
| Event listener failures | High | Low | Queue jobs, retry mechanism, logging |
| Performance degradation | Medium | Low | Database indexing, eager loading, caching |
| Email delivery issues | Medium | Medium | Queue emails, use reliable service (SendGrid) |

### Business Risks

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| HR resistance to change | Medium | Medium | Training, gradual rollout, feedback loop |
| Candidate confusion | Low | Low | Clear UI, onboarding guide, help center |
| Existing process disruption | High | Low | Parallel run for 2 weeks, fallback plan |

---

## 🚀 DEPLOYMENT STRATEGY

### Week 1-2: Phase 1 (Database Integration)
- Run migrations on staging
- Test foreign key constraints
- Migrate existing data
- Deploy to production with zero downtime

### Week 3-4: Phase 2 (Automation)
- Deploy event system
- Test automation with sample candidates
- Monitor job queue performance
- Gradual rollout (10% → 50% → 100%)

### Week 5: Phase 3 & 4 (Dashboards)
- Deploy candidate portal
- Deploy HR unified dashboard
- User acceptance testing
- Gather feedback

### Week 6: Phase 5 (Analytics)
- Deploy analytics
- Train HR team
- Documentation
- Go-live celebration 🎉

---

## 📚 SUCCESS CRITERIA

### Phase 1 Success:
- [ ] All test_sessions have recruitment_stage_id
- [ ] All interview_schedules have recruitment_stage_id
- [ ] No orphaned records
- [ ] Foreign keys working

### Phase 2 Success:
- [ ] Test completion auto-updates pipeline
- [ ] Interview completion auto-updates pipeline
- [ ] Next stage auto-starts
- [ ] Email notifications sent
- [ ] No manual intervention needed for happy path

### Phase 3 Success:
- [ ] Candidate can view full journey
- [ ] Progress bar accurate
- [ ] Action buttons working
- [ ] Mobile-responsive
- [ ] <5 seconds load time

### Phase 4 Success:
- [ ] HR can manage candidate from single page
- [ ] Quick actions working
- [ ] Pipeline timeline accurate
- [ ] <3 clicks to assign test or schedule interview
- [ ] 80% reduction in navigation

### Phase 5 Success:
- [ ] Reminders sent daily
- [ ] Analytics dashboard loading <2 seconds
- [ ] Funnel visualization accurate
- [ ] Time-to-hire calculated correctly

---

## 🎓 TRAINING & DOCUMENTATION

### For HR Team:
1. Video tutorial (15 min): "New Unified Candidate Management"
2. User guide: "Quick Reference for Common Tasks"
3. FAQ document
4. Weekly office hours for Q&A

### For Developers:
1. Architecture documentation
2. Event system flow diagram
3. Database schema updates
4. API documentation (if applicable)

### For Candidates:
1. Welcome email with portal guide
2. In-app tooltips
3. Help center with screenshots

---

## 📞 SUPPORT & ROLLBACK PLAN

### Support Structure:
- Dedicated Slack channel: #recruitment-system-support
- Email: recruitment-support@bizmark.id
- Response time: <2 hours during business hours

### Rollback Plan:
```sql
-- If Phase 2 causes issues, disable automation:
UPDATE config SET key='recruitment.automation.enabled' value='false';

-- If major issues in Phase 4:
-- Redirect /admin/applications/{id} to old view
-- Keep new view at /admin/applications/{id}/unified (beta)
```

---

## ✅ CONCLUSION

Ini adalah transformasi BESAR dari fragmented system ke integrated ecosystem. Dengan roadmap 5 fase ini:

1. **Phase 1** membuat foundation
2. **Phase 2** membawa automation (biggest value!)
3. **Phase 3 & 4** improve UX drastis
4. **Phase 5** add icing on the cake

Total timeline: **6 minggu** untuk complete transformation.

**Start with Phase 1 & 2** karena mereka membawa 80% of the value! 🚀
