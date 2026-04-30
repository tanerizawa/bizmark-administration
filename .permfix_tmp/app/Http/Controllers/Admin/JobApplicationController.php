<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\AuthorizesRequests;
use App\Models\JobApplication;
use App\Models\JobVacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JobApplicationController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizePermission('recruitment.view', 'Anda tidak memiliki akses untuk melihat lamaran kerja.');
    }

    /**
     * Display applications list with filters (ADMIN).
     */
    public function index(Request $request)
    {
        $query = JobApplication::with(['jobVacancy', 'reviewer'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by job vacancy
        if ($request->filled('job_vacancy_id')) {
            $query->where('job_vacancy_id', $request->job_vacancy_id);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $applications = $query->paginate(20);

        // Get data for filters
        $vacancies = JobVacancy::orderBy('title')->get();
        $statusCounts = JobApplication::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('admin.applications.index', compact('applications', 'vacancies', 'statusCounts'));
    }

    /**
     * Display application detail (ADMIN).
     */
    public function show($id)
    {
        $application = JobApplication::with(['jobVacancy', 'reviewer'])->findOrFail($id);

        // Decode JSON fields
        $application->work_experience = is_string($application->work_experience) 
            ? json_decode($application->work_experience, true) 
            : $application->work_experience;
        
        $application->skills = is_string($application->skills) 
            ? json_decode($application->skills, true) 
            : $application->skills;

        return view('admin.applications.show', compact('application'));
    }

    /**
     * Update application status (ADMIN).
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,reviewed,interview,accepted,rejected',
            'notes' => 'nullable|string',
        ]);

        $application = JobApplication::with('jobVacancy')->findOrFail($id);
        $previousStatus = $application->status;
        
        $application->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $application->notes,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        // Send email notification to applicant about status change
        if ($application->email) {
            try {
                \Mail::to($application->email)->send(
                    new \App\Mail\JobApplicationStatusChanged(
                        $application,
                        $previousStatus,
                        $validated['status'],
                        auth()->user(),
                        $validated['notes']
                    )
                );
            } catch (\Exception $emailException) {
                \Log::warning('Failed to send job application status change email', [
                    'application_id' => $application->id,
                    'applicant_email' => $application->email,
                    'error' => $emailException->getMessage()
                ]);
            }
        }

        return back()->with('success', 'Status lamaran berhasil diperbarui dan notifikasi email telah dikirim ke pelamar!');
    }

    /**
     * Download CV file (ADMIN).
     */
    public function downloadCv($id)
    {
        $application = JobApplication::findOrFail($id);

        if (!$application->cv_path || !Storage::disk('public')->exists($application->cv_path)) {
            return back()->with('error', 'File CV tidak ditemukan.');
        }

        return Storage::disk('public')->download($application->cv_path, 
            'CV_' . str_replace(' ', '_', $application->full_name) . '.pdf');
    }

    /**
     * Download Portfolio file (ADMIN).
     */
    public function downloadPortfolio($id)
    {
        $application = JobApplication::findOrFail($id);

        if (!$application->portfolio_path || !Storage::disk('public')->exists($application->portfolio_path)) {
            return back()->with('error', 'File portfolio tidak ditemukan.');
        }

        return Storage::disk('public')->download($application->portfolio_path, 
            'Portfolio_' . str_replace(' ', '_', $application->full_name));
    }

    /**
     * Delete application (ADMIN).
     */
    public function destroy($id)
    {
        $application = JobApplication::findOrFail($id);
        
        $candidateName = $application->full_name;

        // Delete related test sessions (with answers)
        $testSessions = $application->testSessions()->get();
        foreach ($testSessions as $session) {
            // Delete test answers first
            $session->testAnswers()->delete();
            // Delete test session (force delete to bypass soft delete)
            $session->forceDelete();
        }

        // Delete related interview schedules
        $application->interviewSchedules()->delete();

        // Delete uploaded files
        if ($application->cv_path) {
            Storage::disk('public')->delete($application->cv_path);
        }
        if ($application->portfolio_path) {
            Storage::disk('public')->delete($application->portfolio_path);
        }

        // Decrement applications count if job vacancy exists
        if ($application->jobVacancy) {
            $application->jobVacancy->decrement('applications_count');
        }

        // Delete application
        $application->delete();

        return redirect()->route('admin.applications.index')
            ->with('success', "Kandidat {$candidateName} dan semua data terkait berhasil dihapus!");
    }
}
