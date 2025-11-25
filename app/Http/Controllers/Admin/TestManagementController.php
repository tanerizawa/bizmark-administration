<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TestAssignedMail;
use App\Models\TestTemplate;
use App\Models\TestSession;
use App\Models\JobApplication;
use App\Services\RecruitmentWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TestManagementController extends Controller
{
    /**
     * Display a listing of test templates.
     */
    public function index()
    {
        $templates = TestTemplate::withCount('testSessions')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total_templates' => TestTemplate::count(),
            'active_templates' => TestTemplate::where('is_active', true)->count(),
            'active_sessions' => TestSession::where('status', 'started')->count(),
            'completed_today' => TestSession::whereDate('completed_at', today())->count(),
        ];

        return view('admin.recruitment.tests.index', compact('templates', 'stats'));
    }

    /**
     * Show the form for creating a new test template.
     */
    public function create()
    {
        return view('admin.recruitment.tests.create');
    }

    /**
     * Store a newly created test template.
     */
    public function store(Request $request)
    {
        // Different validation based on test type
        if ($request->test_type === 'document-editing') {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'test_type' => 'required|in:psychology,psychometric,technical,aptitude,personality,document-editing',
                'duration_minutes' => 'required|integer|min:5|max:480',
                'passing_score' => 'required|numeric|min:0|max:100',
                'template_file' => 'nullable|file|mimes:doc,docx|max:10240', // Changed to nullable
                'evaluation_criteria' => 'required|array|min:1',
                'evaluation_criteria.*.category' => 'required|string',
                'evaluation_criteria.*.description' => 'required|string',
                'evaluation_criteria.*.points' => 'required|numeric|min:0',
                'evaluation_criteria.*.type' => 'required|in:checkbox,rating,numeric,Technical,Analysis,Quality', // Added more type options
                'instructions' => 'nullable|string',
                'is_active' => 'boolean',
            ]);
            
            // Process evaluation criteria
            $criteria = collect($validated['evaluation_criteria'])->map(function ($criterion, $index) {
                return [
                    'id' => $index + 1,
                    'category' => $criterion['category'],
                    'description' => $criterion['description'],
                    'points' => $criterion['points'],
                    'type' => $criterion['type'],
                ];
            })->toArray();
            
            $validated['evaluation_criteria'] = [
                'criteria' => $criteria,
                'total_points' => collect($criteria)->sum('points'),
            ];
            
            // Store template file
            if ($request->hasFile('template_file')) {
                $file = $request->file('template_file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('test-templates', $filename, 'private');
                $validated['template_file_path'] = $path;
                
                Log::info('Template file uploaded (create)', [
                    'filename' => $filename,
                    'path' => $path,
                ]);
            }
            
            unset($validated['template_file']);
            $validated['is_active'] = $request->boolean('is_active', true);
            
        } else {
            // Regular question-based test
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'test_type' => 'required|in:psychology,psychometric,technical,aptitude,personality,document-editing',
                'duration_minutes' => 'required|integer|min:5|max:480',
                'passing_score' => 'required|numeric|min:0|max:100',
                'questions' => 'required|array|min:1',
                'questions.*.question_text' => 'required|string',
                'questions.*.question_type' => 'required|in:multiple-choice,true-false,essay,rating',
                'questions.*.options' => 'required_if:questions.*.question_type,multiple-choice|array',
                'questions.*.correct_answer' => 'required_unless:questions.*.question_type,essay',
                'questions.*.points' => 'required|numeric|min:0',
                'instructions' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            // Process questions array
            $processedQuestions = collect($validated['questions'])->map(function ($question, $index) {
                return [
                    'id' => $index + 1,
                    'question_text' => $question['question_text'],
                    'question_type' => $question['question_type'],
                    'options' => $question['options'] ?? null,
                    'correct_answer' => $question['correct_answer'] ?? null,
                    'points' => $question['points'],
                ];
            })->toArray();

            $validated['questions_data'] = $processedQuestions;
            $validated['total_questions'] = count($processedQuestions);
            $validated['is_active'] = $request->boolean('is_active', true);
            unset($validated['questions']);
        }

        $template = TestTemplate::create($validated);

        return redirect()
            ->route('admin.recruitment.tests.show', $template)
            ->with('success', 'Template test berhasil dibuat.');
    }

    /**
     * Display the specified test template with statistics.
     */
    public function show(TestTemplate $test)
    {
        $test->loadCount(['testSessions', 'testSessions as completed_sessions_count' => function ($query) {
            $query->where('status', 'completed');
        }]);

        // Get ALL sessions (not just 10)
        $recentSessions = $test->testSessions()
            ->with(['jobApplication.jobVacancy'])
            ->latest()
            ->get();  // Changed from take(10) to get all

        $statistics = [
            'average_score' => $test->averageScore(),
            'pass_rate' => $test->passRate(),
            'average_duration' => $test->testSessions()
                ->where('status', 'completed')
                ->avg(\DB::raw('EXTRACT(EPOCH FROM (completed_at - started_at)) / 60')),
        ];

        // Get available candidates for assignment (candidates without active session for this test)
        $assignedApplicationIds = $test->testSessions()
            ->whereIn('status', ['pending', 'in-progress'])
            ->pluck('job_application_id')
            ->toArray();

        $availableCandidates = \App\Models\JobApplication::whereNotIn('id', $assignedApplicationIds)
            ->with('jobVacancy')
            ->latest()
            ->take(20)
            ->get();

        return view('admin.recruitment.tests.show', compact('test', 'recentSessions', 'statistics', 'availableCandidates'));
    }

    /**
     * Show the form for editing the specified test template.
     */
    public function edit(TestTemplate $test)
    {
        return view('admin.recruitment.tests.edit', compact('test'));
    }

    /**
     * Update the specified test template.
     */
    public function update(Request $request, TestTemplate $test)
    {
        // Base validation
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'test_type' => 'required|in:psychology,psychometric,technical,aptitude,personality,document-editing',
            'duration_minutes' => 'required|integer|min:5|max:480',
            'passing_score' => 'required|numeric|min:0|max:100',
            'instructions' => 'nullable|string',
            'is_active' => 'boolean',
        ];

        // Conditional validation based on test_type
        if ($request->test_type === 'document-editing') {
            $rules['template_file'] = 'nullable|file|mimes:doc,docx|max:10240';
            $rules['evaluation_criteria'] = 'nullable|array'; // Changed to nullable for existing templates
            $rules['evaluation_criteria.*.category'] = 'nullable|string|max:255';
            $rules['evaluation_criteria.*.description'] = 'nullable|string';
            $rules['evaluation_criteria.*.points'] = 'nullable|numeric|min:0|max:100';
            $rules['evaluation_criteria.*.type'] = 'nullable|in:Technical,Analysis,Quality,checkbox,rating,numeric';
        } else {
            // For non-document-editing types, questions are optional on update
            // (they might already exist in questions_data)
            $rules['questions'] = 'nullable|array';
            $rules['questions.*.question_text'] = 'nullable|string';
            $rules['questions.*.question_type'] = 'nullable|in:multiple-choice,true-false,essay,coding';
            $rules['questions.*.points'] = 'nullable|numeric|min:0';
            $rules['questions.*.options'] = 'nullable|array';
            $rules['questions.*.correct_answer'] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        Log::info('Update validation passed', [
            'test_id' => $test->id,
            'test_type' => $request->test_type,
            'has_file' => $request->hasFile('template_file'),
            'file_size' => $request->hasFile('template_file') ? $request->file('template_file')->getSize() : 0,
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        // Handle file upload for document-editing
        if ($request->test_type === 'document-editing' && $request->hasFile('template_file')) {
            // Delete old file if exists
            if ($test->template_file_path && Storage::disk('private')->exists($test->template_file_path)) {
                Storage::disk('private')->delete($test->template_file_path);
            }

            $file = $request->file('template_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('test-templates', $filename, 'private');
            $validated['template_file_path'] = $path;
            
            Log::info('Template file uploaded (update)', [
                'filename' => $filename,
                'path' => $path,
                'test_id' => $test->id
            ]);
        }

        // Process evaluation_criteria for document-editing (only if provided)
        if ($request->test_type === 'document-editing' && $request->has('evaluation_criteria')) {
            $validated['evaluation_criteria'] = [
                'criteria' => $request->evaluation_criteria,
                'total_points' => array_sum(array_column($request->evaluation_criteria, 'points'))
            ];
        }

        // Process questions for non-document-editing types (only if provided)
        if ($request->test_type !== 'document-editing' && $request->has('questions')) {
            // Process questions similar to create
            $processedQuestions = collect($request->questions)->map(function ($question, $index) {
                return [
                    'id' => $index + 1,
                    'question_text' => $question['question_text'],
                    'question_type' => $question['question_type'],
                    'options' => $question['options'] ?? null,
                    'correct_answer' => $question['correct_answer'] ?? null,
                    'points' => $question['points'],
                ];
            })->toArray();

            $validated['questions_data'] = $processedQuestions;
        }

        $test->update($validated);

        Log::info('Template updated', [
            'test_id' => $test->id,
            'has_file' => isset($validated['template_file_path']),
            'file_path' => $validated['template_file_path'] ?? null
        ]);

        return redirect()
            ->route('admin.recruitment.tests.edit', $test)
            ->with('success', 'Template test berhasil diupdate.');
    }

    /**
     * Remove the specified test template.
     */
    public function destroy(TestTemplate $test)
    {
        // Check if test has completed or in-progress sessions
        $activeSessions = $test->testSessions()
            ->whereIn('status', ['in-progress', 'completed'])
            ->count();
        
        if ($activeSessions > 0) {
            return redirect()
                ->route('admin.recruitment.tests.index')
                ->with('error', "Tidak dapat menghapus template yang memiliki {$activeSessions} sesi aktif/selesai. Hapus sesi tersebut terlebih dahulu.");
        }
        
        // Delete pending sessions (if any) - they haven't started yet
        $pendingSessions = $test->testSessions()->where('status', 'pending')->count();
        if ($pendingSessions > 0) {
            $test->testSessions()->where('status', 'pending')->delete();
            Log::info('Deleted pending sessions', [
                'test_id' => $test->id,
                'count' => $pendingSessions
            ]);
        }
        
        // Delete template file if document-editing type
        if ($test->test_type === 'document-editing' && $test->template_file_path) {
            if (Storage::disk('private')->exists($test->template_file_path)) {
                Storage::disk('private')->delete($test->template_file_path);
            }
        }

        $test->delete();

        $message = $pendingSessions > 0 
            ? "Template test berhasil dihapus (termasuk {$pendingSessions} sesi pending)."
            : 'Template test berhasil dihapus.';

        return redirect()
            ->route('admin.recruitment.tests.index')
            ->with('success', $message);
    }

    /**
     * Assign test to candidate (create test session).
     */
    public function assign(Request $request)
    {
        $validated = $request->validate([
            'test_template_id' => 'required|exists:test_templates,id',
            'job_application_id' => 'required|exists:job_applications,id',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $template = TestTemplate::findOrFail($validated['test_template_id']);
        $application = JobApplication::findOrFail($validated['job_application_id']);
        
        // Check if active session already exists for this application + template
        $existingSession = TestSession::where('job_application_id', $application->id)
            ->where('test_template_id', $template->id)
            ->whereIn('status', ['pending', 'in-progress'])
            ->first();
        
        if ($existingSession) {
            return back()
                ->with('warning', 'Kandidat sudah memiliki test session aktif untuk template ini.')
                ->withInput();
        }
        
        // Use WorkflowService for integrated assignment
        $workflowService = app(RecruitmentWorkflowService::class);
        
        try {
            $session = $workflowService->assignTest($application, $template);
            
            // Update expires_at if provided
            if (isset($validated['expires_at'])) {
                $session->update(['expires_at' => $validated['expires_at']]);
            }
            
            Log::info("Test assigned via workflow service", [
                'session_id' => $session->id,
                'application_id' => $application->id,
                'template_id' => $template->id,
                'stage_id' => $session->recruitment_stage_id,
            ]);

            // Redirect back to test template detail (not session)
            return redirect()
                ->route('admin.recruitment.tests.show', $template)
                ->with('success', 'Test berhasil diberikan kepada ' . $application->full_name . '. Notifikasi email telah dikirim.');
        } catch (\Exception $e) {
            Log::error("Failed to assign test via workflow", [
                'error' => $e->getMessage(),
                'application_id' => $application->id,
            ]);
            
            return back()
                ->with('error', 'Gagal memberikan test: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * View test session results.
     */
    public function sessionResults(TestSession $session)
    {
        $session->load([
            'testTemplate',
            'jobApplication.jobVacancy',
            'testAnswers',
            'evaluator'
        ]);

        return view('admin.recruitment.tests.session-results', compact('session'));
    }

    /**
     * Show evaluation form for manual grading (essay/rating questions).
     */
    public function showEvaluationForm(TestSession $session)
    {
        // Check if session requires manual review
        if (!$session->requires_manual_review) {
            return redirect()
                ->route('admin.recruitment.tests.sessions.results', $session)
                ->with('info', 'This test session does not require manual evaluation.');
        }

        // Load relationships
        $session->load([
            'testTemplate',
            'jobApplication.jobVacancy',
            'testAnswers',
        ]);

        // Get questions that need manual review
        $questions = $session->testTemplate->questions_data ?? [];
        $answers = $session->testAnswers->keyBy('question_id');
        
        $subjectiveQuestions = [];
        foreach ($questions as $index => $question) {
            $questionType = $question['question_type'] ?? null;
            
            // Only subjective questions need manual grading
            if (in_array($questionType, ['essay', 'rating', 'rating-scale', 'document-editing'])) {
                $subjectiveQuestions[$index] = [
                    'question' => $question,
                    'answer' => $answers->get($index),
                ];
            }
        }

        return view('admin.recruitment.tests.evaluate', compact('session', 'subjectiveQuestions'));
    }

    /**
     * Submit manual evaluation scores.
     */
    public function submitEvaluation(Request $request, TestSession $session)
    {
        // Validate session status
        if (!$session->requires_manual_review) {
            return redirect()
                ->route('admin.recruitment.tests.sessions.results', $session)
                ->with('error', 'This test session does not require manual evaluation.');
        }

        // Validate input
        $request->validate([
            'manual_scores' => 'required|array',
            'manual_scores.*' => 'required|numeric|min:0',
            'evaluator_notes' => 'nullable|string|max:2000',
        ]);

        // Complete manual evaluation
        $success = $session->completeManualEvaluation(
            evaluatorId: auth()->id(),
            manualScores: $request->manual_scores,
            notes: $request->evaluator_notes
        );

        if ($success) {
            return redirect()
                ->route('admin.recruitment.tests.sessions.results', $session)
                ->with('success', 'Evaluation completed successfully. Final score: ' . number_format($session->fresh()->score, 2) . '%');
        }

        return back()
            ->with('error', 'Failed to submit evaluation. Please try again.');
    }

    /**
     * Cancel a test session (soft delete).
     */
    public function cancelSession(TestSession $session)
    {
        // Validate that session can be cancelled
        if (!in_array($session->status, ['pending', 'not-started', 'expired'])) {
            return back()
                ->with('error', 'Test session tidak dapat dibatalkan karena sudah dimulai atau diselesaikan.');
        }

        // Get candidate info for notification
        $candidateName = $session->jobApplication?->full_name ?? 'Candidate';
        $testTitle = $session->testTemplate?->title ?? 'Test';

        // Soft delete the session
        $session->delete();

        // Log the cancellation
        Log::info('Test session cancelled', [
            'session_id' => $session->id,
            'candidate' => $candidateName,
            'test' => $testTitle,
            'cancelled_by' => auth()->id(),
            'cancelled_at' => now(),
        ]);

        return back()
            ->with('success', "Test session untuk {$candidateName} - {$testTitle} berhasil dibatalkan.");
    }
}
