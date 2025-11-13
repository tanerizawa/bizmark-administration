<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobVacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JobApplicationController extends Controller
{
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

        $application = JobApplication::findOrFail($id);
        
        $application->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $application->notes,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        // TODO: Send email notification to applicant about status change
        // Mail::to($application->email)->send(new ApplicationStatusChanged($application));

        return back()->with('success', 'Status lamaran berhasil diperbarui!');
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

        // Delete uploaded files
        if ($application->cv_path) {
            Storage::disk('public')->delete($application->cv_path);
        }
        if ($application->portfolio_path) {
            Storage::disk('public')->delete($application->portfolio_path);
        }

        // Decrement applications count
        $application->jobVacancy->decrement('applications_count');

        $application->delete();

        return redirect()->route('admin.applications.index')
            ->with('success', 'Lamaran berhasil dihapus!');
    }
}
