<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\RecruitmentStage;
use App\Models\InterviewSchedule;
use App\Models\TestSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecruitmentPipelineController extends Controller
{
    /**
     * Display recruitment pipeline dashboard.
     */
    public function index(Request $request)
    {
        $query = JobApplication::with(['jobVacancy', 'recruitmentStages' => function ($q) {
            $q->orderBy('stage_order');
        }]);

        // Filter by vacancy
        if ($request->filled('vacancy_id')) {
            $query->where('job_vacancy_id', $request->vacancy_id);
        }

        // Filter by current stage
        if ($request->filled('stage')) {
            $query->whereHas('recruitmentStages', function ($q) use ($request) {
                $q->where('stage_name', $request->stage)
                  ->where('status', 'in-progress');
            });
        }

        $applications = $query->paginate(20);

        // Pipeline statistics
        $stats = [
            'total_in_pipeline' => JobApplication::whereHas('recruitmentStages', function ($q) {
                $q->whereIn('status', ['pending', 'in-progress']);
            })->count(),
            
            'screening' => RecruitmentStage::where('stage_name', 'screening')
                ->where('status', 'in-progress')
                ->count(),
            
            'testing' => RecruitmentStage::where('stage_name', 'testing')
                ->where('status', 'in-progress')
                ->count(),
            
            'interview' => RecruitmentStage::where('stage_name', 'interview')
                ->where('status', 'in-progress')
                ->count(),
            
            'offer' => RecruitmentStage::where('stage_name', 'offer')
                ->where('status', 'in-progress')
                ->count(),
            
            'completed_this_week' => RecruitmentStage::where('status', 'passed')
                ->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];

        return view('admin.recruitment.pipeline.index', compact('applications', 'stats'));
    }

    /**
     * Show detailed view of single application in pipeline.
     */
    public function show(JobApplication $application)
    {
        $application->load([
            'jobVacancy',
            'recruitmentStages' => function ($q) {
                $q->orderBy('stage_order');
            },
            'interviewSchedules.feedback',
            'testSessions.testTemplate',
            'technicalTestSubmissions',
        ]);

        // Check if job vacancy exists
        if (!$application->jobVacancy) {
            return redirect()
                ->route('admin.recruitment.pipeline.index')
                ->with('error', 'Job Vacancy untuk aplikasi ini tidak ditemukan. Data mungkin sudah dihapus.');
        }

        $timeline = $this->buildTimeline($application);

        return view('admin.recruitment.pipeline.show', compact('application', 'timeline'));
    }

    /**
     * Initialize recruitment stages for an application.
     */
    public function initializeStages(Request $request, JobApplication $application)
    {
        // Check if already initialized
        if ($application->recruitmentStages()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Pipeline sudah diinisialisasi untuk aplikasi ini.');
        }

        $validated = $request->validate([
            'stages' => 'required|array|min:1',
            'stages.*.stage_name' => 'required|in:screening,testing,interview,technical-test,offer',
            'stages.*.stage_order' => 'required|integer|min:1',
        ]);

        foreach ($validated['stages'] as $stageData) {
            RecruitmentStage::create([
                'job_application_id' => $application->id,
                'stage_name' => $stageData['stage_name'],
                'stage_order' => $stageData['stage_order'],
                'status' => (int)$stageData['stage_order'] === 1 ? 'in-progress' : 'pending',
                'started_at' => (int)$stageData['stage_order'] === 1 ? now() : null,
            ]);
        }

        return redirect()
            ->route('admin.recruitment.pipeline.show', $application)
            ->with('success', 'Pipeline berhasil diinisialisasi.');
    }

    /**
     * Update stage status (mark as passed/failed).
     */
    public function updateStage(Request $request, RecruitmentStage $stage)
    {
        $validated = $request->validate([
            'status' => 'required|in:in-progress,passed,failed,skipped',
            'action' => 'nullable|in:pass,fail',
            'score' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        // Handle new status-based update
        if (isset($validated['status'])) {
            switch ($validated['status']) {
                case 'in-progress':
                    $stage->update([
                        'status' => 'in-progress',
                        'started_at' => now(),
                    ]);
                    $message = 'Stage dimulai.';
                    break;
                    
                case 'passed':
                    $stage->update([
                        'status' => 'passed',
                        'completed_at' => now(),
                        'score' => $validated['score'] ?? null,
                        'notes' => $validated['notes'] ?? null,
                    ]);
                    
                    // Start next stage automatically
                    $nextStage = $stage->jobApplication->recruitmentStages()
                        ->where('stage_order', '>', $stage->stage_order)
                        ->where('status', 'pending')
                        ->orderBy('stage_order')
                        ->first();
                        
                    if ($nextStage) {
                        $nextStage->update([
                            'status' => 'in-progress',
                            'started_at' => now(),
                        ]);
                    }
                    
                    $message = 'Stage berhasil diselesaikan.' . ($nextStage ? ' Stage berikutnya sudah dimulai.' : '');
                    break;
                    
                case 'failed':
                    $stage->update([
                        'status' => 'failed',
                        'completed_at' => now(),
                        'notes' => $validated['notes'] ?? null,
                    ]);
                    $message = 'Stage ditandai sebagai gagal.';
                    break;
                    
                case 'skipped':
                    $stage->update([
                        'status' => 'skipped',
                        'completed_at' => now(),
                        'notes' => 'Skipped',
                    ]);
                    $message = 'Stage dilewati.';
                    break;
            }
        }
        // Handle legacy action-based update
        elseif (isset($validated['action'])) {
            if ($validated['action'] === 'pass') {
                $stage->markAsPassed($validated['score'] ?? null, $validated['notes'] ?? null);
                
                // Start next stage automatically
                $nextStage = $stage->jobApplication->getNextStage();
                if ($nextStage) {
                    $nextStage->markAsStarted();
                }
                
                $message = 'Stage berhasil diselesaikan. Stage berikutnya sudah dimulai.';
            } else {
                $stage->markAsFailed($validated['notes'] ?? null);
                $message = 'Stage ditandai sebagai gagal.';
            }
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }

    /**
     * Build timeline for application.
     */
    private function buildTimeline(JobApplication $application): array
    {
        $timeline = [];

        // Application submitted
        $timeline[] = [
            'date' => $application->created_at,
            'timestamp' => $application->created_at->format('d M Y H:i'),
            'type' => 'application',
            'title' => 'Lamaran Diterima',
            'description' => 'Kandidat mengirimkan lamaran',
            'icon' => 'document',
            'color' => 'primary',
        ];

        // Recruitment stages
        foreach ($application->recruitmentStages as $stage) {
            if ($stage->started_at) {
                $timeline[] = [
                    'date' => $stage->started_at,
                    'timestamp' => $stage->started_at->format('d M Y H:i'),
                    'type' => 'stage_start',
                    'title' => 'Memulai: ' . $stage->getStageNameLabel(),
                    'description' => $stage->notes ?? '',
                    'icon' => 'play',
                    'color' => 'info',
                ];
            }

            if ($stage->completed_at) {
                $timeline[] = [
                    'date' => $stage->completed_at,
                    'timestamp' => $stage->completed_at->format('d M Y H:i'),
                    'type' => $stage->status === 'passed' ? 'stage_pass' : 'stage_fail',
                    'title' => ($stage->status === 'passed' ? 'Lulus: ' : 'Gagal: ') . $stage->getStageNameLabel(),
                    'description' => $stage->notes ?? '',
                    'icon' => $stage->status === 'passed' ? 'check' : 'x',
                    'color' => $stage->status === 'passed' ? 'success' : 'danger',
                    'score' => $stage->score,
                ];
            }
        }

        // Interviews
        foreach ($application->interviewSchedules as $interview) {
            $timeline[] = [
                'date' => $interview->scheduled_at,
                'timestamp' => $interview->scheduled_at->format('d M Y H:i'),
                'type' => 'interview',
                'title' => 'Interview ' . $interview->getMeetingTypeLabel(),
                'description' => $interview->notes ?? '',
                'icon' => 'users',
                'color' => 'warning',
                'status' => $interview->status,
            ];
        }

        // Test sessions
        foreach ($application->testSessions as $session) {
            if ($session->completed_at) {
                $timeline[] = [
                    'date' => $session->completed_at,
                    'timestamp' => $session->completed_at->format('d M Y H:i'),
                    'type' => 'test',
                    'title' => 'Selesai: ' . $session->testTemplate->title,
                    'description' => 'Skor: ' . $session->final_score . '%',
                    'icon' => 'clipboard',
                    'color' => 'secondary',
                    'score' => $session->final_score,
                ];
            }
        }

        // Sort by date
        usort($timeline, function ($a, $b) {
            return $a['date'] <=> $b['date'];
        });

        return $timeline;
    }

    /**
     * Display pipeline for a specific job (Tab View).
     */
    public function jobPipeline($jobId)
    {
        $vacancy = \App\Models\JobVacancy::withCount('applications')->findOrFail($jobId);
        
        // Get all applications for this job with pipeline data
        $query = $vacancy->applications()
            ->with(['jobVacancy', 'recruitmentStages' => function ($q) {
                $q->orderBy('stage_order');
            }])
            ->orderBy('created_at', 'desc');

        // Apply status filter if provided
        if (request('stage')) {
            $query->whereHas('recruitmentStages', function ($q) {
                $q->where('stage_name', request('stage'))
                  ->where('status', 'in-progress');
            });
        }

        // Apply search filter
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $applications = $query->paginate(20);

        // Pipeline statistics for this job
        $stats = [
            'total_in_pipeline' => $vacancy->applications()
                ->whereHas('recruitmentStages', function ($q) {
                    $q->whereIn('status', ['pending', 'in-progress']);
                })->count(),
            
            'screening' => RecruitmentStage::whereHas('jobApplication', function($q) use ($jobId) {
                $q->where('job_vacancy_id', $jobId);
            })->where('stage_name', 'screening')
              ->where('status', 'in-progress')
              ->count(),
            
            'testing' => RecruitmentStage::whereHas('jobApplication', function($q) use ($jobId) {
                $q->where('job_vacancy_id', $jobId);
            })->where('stage_name', 'testing')
              ->where('status', 'in-progress')
              ->count(),
            
            'interview' => RecruitmentStage::whereHas('jobApplication', function($q) use ($jobId) {
                $q->where('job_vacancy_id', $jobId);
            })->where('stage_name', 'interview')
              ->where('status', 'in-progress')
              ->count(),
            
            'final' => RecruitmentStage::whereHas('jobApplication', function($q) use ($jobId) {
                $q->where('job_vacancy_id', $jobId);
            })->where('stage_name', 'final')
              ->where('status', 'in-progress')
              ->count(),
            
            'passed' => $vacancy->applications()
                ->whereHas('recruitmentStages', function ($q) {
                    $q->where('status', 'passed');
                })->count(),
            
            'failed' => $vacancy->applications()
                ->whereHas('recruitmentStages', function ($q) {
                    $q->where('status', 'failed');
                })->count(),
        ];

        return view('admin.jobs.pipeline', compact('vacancy', 'applications', 'stats'));
    }
}
