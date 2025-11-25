<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InterviewRescheduledMail;
use App\Mail\InterviewScheduledMail;
use App\Models\InterviewSchedule;
use App\Models\JobApplication;
use App\Models\User;
use App\Events\InterviewCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InterviewScheduleController extends Controller
{
    /**
     * Display a listing of the resource (calendar view).
     */
    public function index(Request $request)
    {
        // Check if request wants JSON (for FullCalendar AJAX)
        if ($request->wantsJson() || $request->has('json')) {
            $start = $request->input('start');
            $end = $request->input('end');

            $interviews = InterviewSchedule::with(['jobApplication.jobVacancy'])
                ->whereBetween('scheduled_at', [$start, $end])
                ->get();

            // Format for FullCalendar
            $events = $interviews->map(function ($interview) {
                return [
                    'id' => $interview->id,
                    'title' => $interview->jobApplication->full_name . ' - ' . $interview->jobApplication->jobVacancy->title,
                    'start' => $interview->scheduled_at->toIso8601String(),
                    'end' => $interview->scheduled_at->copy()->addMinutes($interview->duration_minutes)->toIso8601String(),
                    'backgroundColor' => $this->getStatusColor($interview->status),
                    'borderColor' => $this->getStatusColor($interview->status),
                    'extendedProps' => [
                        'status' => $interview->status,
                        'type' => $interview->interview_type,
                        'location' => $interview->location,
                        'meetingLink' => $interview->meeting_link,
                    ],
                ];
            });

            return response()->json($events);
        }

        // Regular view
        $upcomingInterviews = InterviewSchedule::with(['jobApplication.jobVacancy'])
            ->where('scheduled_at', '>=', now())
            ->where('status', 'scheduled')
            ->orderBy('scheduled_at')
            ->take(10)
            ->get();

        $todayInterviews = InterviewSchedule::with(['jobApplication.jobVacancy'])
            ->whereDate('scheduled_at', today())
            ->orderBy('scheduled_at')
            ->get();

        // Interview metrics
        $metrics = [
            'completed' => InterviewSchedule::where('status', 'completed')->count(),
            'cancelled' => InterviewSchedule::where('status', 'cancelled')->count(),
        ];

        return view('admin.recruitment.interviews.index', compact('upcomingInterviews', 'todayInterviews', 'metrics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $applicationId = $request->input('application_id');
        $application = $applicationId ? JobApplication::with('jobVacancy')->findOrFail($applicationId) : null;
        
        // Get available interviewers (active users, can filter by role if needed)
        $interviewers = User::where('is_active', true)
            ->orderBy('full_name')
            ->get();

        return view('admin.recruitment.interviews.create', compact('application', 'interviewers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_application_id' => 'required|exists:job_applications,id',
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'interview_type' => 'required|in:preliminary,technical,hr,final',
            'interview_stage' => 'nullable|integer|min:1|max:10',
            'meeting_type' => 'required|in:in-person,video-call,phone',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url',
            'meeting_password' => 'nullable|string|max:255',
            'interviewer_ids' => 'required|array|min:1',
            'interviewer_ids.*' => 'exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = 'scheduled';
        $validated['interviewer_ids'] = json_encode($validated['interviewer_ids']);
        $validated['interview_stage'] = $validated['interview_stage'] ?? 1;
        $validated['created_by'] = auth()->id();

        // If video interview, generate Jitsi room name if no meeting link provided
        if ($validated['meeting_type'] === 'video-call' && empty($validated['meeting_link'])) {
            $roomName = 'interview-' . Str::random(12);
            $validated['meeting_link'] = 'https://' . config('services.jitsi.domain', 'meet.jit.si') . '/' . $roomName;
        }

        $interview = InterviewSchedule::create($validated);

        // Send interview scheduled email notification
        $interview->load('jobApplication.jobVacancy');
        Mail::to($interview->jobApplication->email)
            ->send(new InterviewScheduledMail($interview));

        return redirect()
            ->route('admin.recruitment.interviews.show', $interview)
            ->with('success', 'Interview berhasil dijadwalkan dan notifikasi email telah dikirim.');
    }

    /**
     * Display the specified resource.
     */
    public function show(InterviewSchedule $interview)
    {
        $interview->load(['jobApplication.jobVacancy', 'feedback.interviewer']);

        return view('admin.recruitment.interviews.show', compact('interview'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InterviewSchedule $interview)
    {
        $interview->load('jobApplication.jobVacancy');
        $interviewers = User::where('is_active', true)
            ->orderBy('full_name')
            ->get();

        return view('admin.recruitment.interviews.edit', compact('interview', 'interviewers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InterviewSchedule $interview)
    {
        $validated = $request->validate([
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'interview_type' => 'required|in:preliminary,technical,hr,final',
            'interview_stage' => 'nullable|integer|min:1|max:10',
            'meeting_type' => 'required|in:in-person,video-call,phone',
            'location' => 'nullable|string|max:255',
            'meeting_link' => 'nullable|url',
            'meeting_password' => 'nullable|string|max:255',
            'interviewer_ids' => 'required|array|min:1',
            'interviewer_ids.*' => 'exists:users,id',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:scheduled,confirmed,rescheduled,completed,cancelled,no-show',
        ]);

        // Store old date for email comparison
        $oldDate = $interview->scheduled_at;
        $dateChanged = $request->input('scheduled_at') !== $oldDate->format('Y-m-d H:i:s');

        $validated['interviewer_ids'] = json_encode($validated['interviewer_ids']);

        $interview->update($validated);

        // Send reschedule notification if date/time changed
        if ($dateChanged) {
            $interview->load('jobApplication.jobVacancy');
            Mail::to($interview->jobApplication->email)
                ->send(new InterviewRescheduledMail(
                    $interview, 
                    $oldDate, 
                    $request->input('reschedule_reason', 'Schedule adjustment')
                ));
        }

        return redirect()
            ->route('admin.recruitment.interviews.show', $interview)
            ->with('success', 'Interview berhasil diupdate.' . ($dateChanged ? ' Notifikasi reschedule telah dikirim.' : ''));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InterviewSchedule $interview)
    {
        $interview->delete();

        return redirect()
            ->route('admin.recruitment.interviews.index')
            ->with('success', 'Interview berhasil dihapus.');
    }

    /**
     * Show feedback form for completed interview.
     */
    public function feedback(InterviewSchedule $interview)
    {
        // Check if interview is completed
        if ($interview->status !== 'completed') {
            return redirect()
                ->route('admin.recruitment.interviews.show', $interview)
                ->with('warning', 'Feedback hanya dapat diberikan untuk interview yang sudah completed.');
        }

        // Check if feedback already exists
        if ($interview->feedback()->exists()) {
            return redirect()
                ->route('admin.recruitment.interviews.show', $interview)
                ->with('info', 'Feedback sudah pernah diberikan. Anda dapat mengeditnya dari detail interview.');
        }

        $interview->load('jobApplication.jobVacancy');

        return view('admin.recruitment.interviews.feedback', compact('interview'));
    }

    /**
     * Store interview feedback.
     */
    public function storeFeedback(Request $request, InterviewSchedule $interview)
    {
        $validated = $request->validate([
            'communication_rating' => 'required|integer|min:1|max:5',
            'technical_rating' => 'required|integer|min:1|max:5',
            'teamwork_rating' => 'required|integer|min:1|max:5',
            'culture_fit_rating' => 'required|integer|min:1|max:5',
            'overall_rating' => 'required|integer|min:1|max:5',
            'strengths' => 'nullable|string|max:2000',
            'weaknesses' => 'nullable|string|max:2000',
            'additional_notes' => 'nullable|string|max:2000',
            'recommendation' => 'required|in:highly-recommended,recommended,neutral,not-recommended',
        ]);

        // Create feedback record
        $interview->feedback()->create([
            'communication_rating' => $validated['communication_rating'],
            'technical_rating' => $validated['technical_rating'],
            'teamwork_rating' => $validated['teamwork_rating'],
            'culture_fit_rating' => $validated['culture_fit_rating'],
            'overall_rating' => $validated['overall_rating'],
            'comments' => json_encode([
                'strengths' => $validated['strengths'] ?? null,
                'weaknesses' => $validated['weaknesses'] ?? null,
                'additional_notes' => $validated['additional_notes'] ?? null,
            ]),
            'recommendation' => $validated['recommendation'],
            'reviewer_id' => auth()->id(),
        ]);

        // Update interview stage in recruitment pipeline if exists
        $application = $interview->jobApplication;
        $interviewStage = $application->recruitmentStages()
            ->where('stage_name', 'interview')
            ->first();

        if ($interviewStage && $interviewStage->status === 'in-progress') {
            // Auto-pass or fail based on recommendation
            $passed = in_array($validated['recommendation'], ['highly-recommended', 'recommended']);
            
            $interviewStage->update([
                'status' => $passed ? 'passed' : 'failed',
                'completed_at' => now(),
                'passed' => $passed,
                'notes' => $passed 
                    ? 'Interview feedback: ' . ucfirst(str_replace('-', ' ', $validated['recommendation']))
                    : 'Interview feedback: Not recommended',
            ]);

            // Dispatch event to trigger recruitment workflow automation
            event(new InterviewCompleted(
                $interview,
                $validated['recommendation'],
                $validated['overall_rating']
            ));

            // If passed, start next stage if exists
            if ($passed) {
                $nextStage = $application->getNextStage();
                if ($nextStage) {
                    $nextStage->update([
                        'status' => 'in-progress',
                        'started_at' => now(),
                    ]);
                }
            }
        }

        return redirect()
            ->route('admin.recruitment.interviews.show', $interview)
            ->with('success', 'Feedback berhasil disimpan dan recruitment stage telah diupdate.');
    }

    /**
     * Get status color for calendar.
     */
    private function getStatusColor(string $status): string
    {
        return match($status) {
            'scheduled' => '#3b82f6',  // blue
            'completed' => '#10b981',  // green
            'cancelled' => '#ef4444',  // red
            'rescheduled' => '#f59e0b', // orange
            default => '#6b7280',      // gray
        };
    }

    /**
     * Display interviews for a specific job (Tab View).
     */
    public function jobInterviews($jobId)
    {
        $vacancy = \App\Models\JobVacancy::withCount('applications')->findOrFail($jobId);
        
        // Get all interviews for applications of this job
        $query = InterviewSchedule::with(['jobApplication' => function($q) use ($jobId) {
                $q->where('job_vacancy_id', $jobId);
            }])
            ->whereHas('jobApplication', function($q) use ($jobId) {
                $q->where('job_vacancy_id', $jobId);
            })
            ->orderBy('scheduled_at', 'desc');

        // Apply status filter
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Apply interview type filter
        if (request('interview_type')) {
            $query->where('interview_type', request('interview_type'));
        }

        $interviews = $query->paginate(20);

        // Interview statistics for this job
        $stats = [
            'total' => InterviewSchedule::whereHas('jobApplication', function($q) use ($jobId) {
                $q->where('job_vacancy_id', $jobId);
            })->count(),
            
            'scheduled' => InterviewSchedule::whereHas('jobApplication', function($q) use ($jobId) {
                $q->where('job_vacancy_id', $jobId);
            })->where('status', 'scheduled')->count(),
            
            'completed' => InterviewSchedule::whereHas('jobApplication', function($q) use ($jobId) {
                $q->where('job_vacancy_id', $jobId);
            })->where('status', 'completed')->count(),
            
            'upcoming' => InterviewSchedule::whereHas('jobApplication', function($q) use ($jobId) {
                $q->where('job_vacancy_id', $jobId);
            })->where('status', 'scheduled')
              ->where('scheduled_at', '>=', now())
              ->count(),
        ];

        return view('admin.jobs.interviews', compact('vacancy', 'interviews', 'stats'));
    }
}
