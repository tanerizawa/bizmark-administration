<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobVacancy;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class RecruitmentController extends Controller
{
    /**
     * Display the recruitment dashboard with tabs
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'jobs');
        
        // Get notification counts for badges
        $notifications = $this->getNotificationCounts();
        
        // Get summary stats (always needed for hero section)
        $totalJobs = JobVacancy::count();
        $activeJobs = JobVacancy::where('status', 'open')->count();
        $totalApplications = JobApplication::count();
        
        // Load data based on active tab
        $data = match($activeTab) {
            'jobs' => $this->getJobsData($request),
            'applications' => $this->getApplicationsData($request),
            default => $this->getJobsData($request)
        };
        
        // Provide empty defaults for inactive tabs
        $emptyPaginator = new LengthAwarePaginator([], 0, 20);
        
        $defaults = [
            'jobs' => clone $emptyPaginator,
            'applications' => clone $emptyPaginator,
            'statuses' => [],
            'jobStatuses' => [],
            'employmentTypes' => [],
            'jobsForFilter' => collect([]),
            'pendingCount' => 0,
            'interviewCount' => 0,
            'offeredCount' => 0,
            'hiredCount' => 0,
            'activeCount' => 0,
            'draftCount' => 0,
            'closedCount' => 0,
        ];
        
        return view('admin.recruitment.index', array_merge($defaults, $data, [
            'activeTab' => $activeTab,
            'notifications' => $notifications,
            'totalJobs' => $totalJobs,
            'activeJobs' => $activeJobs,
            'totalApplications' => $totalApplications,
        ]));
    }
    
    /**
     * Get notification counts for badge display
     */
    private function getNotificationCounts()
    {
        $pendingApplications = JobApplication::where('status', 'pending')->count();
        
        return [
            'applications' => $pendingApplications,
            'total' => $pendingApplications
        ];
    }
    
    /**
     * Get jobs tab data
     */
    private function getJobsData(Request $request)
    {
        $query = JobVacancy::withCount('applications')->latest();
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        $jobs = $query->paginate(20)->withQueryString();
        
        $jobStatuses = ['open', 'closed', 'draft'];
        $employmentTypes = ['full-time', 'part-time', 'contract', 'internship'];
        
        // Get stats
        $activeCount = JobVacancy::where('status', 'open')->count();
        $draftCount = JobVacancy::where('status', 'draft')->count();
        $closedCount = JobVacancy::where('status', 'closed')->count();
        
        return compact('jobs', 'jobStatuses', 'employmentTypes', 'activeCount', 'draftCount', 'closedCount');
    }
    
    /**
     * Get applications tab data
     */
    private function getApplicationsData(Request $request)
    {
        $query = JobApplication::with(['jobVacancy'])->latest();
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('job_id')) {
            $query->where('job_vacancy_id', $request->job_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $applications = $query->paginate(20)->withQueryString();
        
        $statuses = ['pending', 'reviewed', 'interview', 'accepted', 'rejected'];
        $jobsForFilter = JobVacancy::where('status', 'open')->get();
        
        // Get stats
        $pendingCount = JobApplication::where('status', 'pending')->count();
        $interviewCount = JobApplication::where('status', 'interview')->count();
        $offeredCount = JobApplication::where('status', 'accepted')->count();
        $hiredCount = JobApplication::where('status', 'accepted')->count();
        
        return compact('applications', 'statuses', 'jobsForFilter', 'pendingCount', 'interviewCount', 'offeredCount', 'hiredCount');
    }
}

