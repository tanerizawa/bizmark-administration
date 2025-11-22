<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermitApplication;
use App\Models\PermitType;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ApplicationNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermitManagementController extends Controller
{
    /**
     * Display the permit management dashboard with tabs
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'dashboard');
        $allowedTabs = ['dashboard', 'applications', 'types', 'payments'];
        if (!in_array($activeTab, $allowedTabs, true)) {
            $activeTab = 'dashboard';
        }
        
        // Get notification counts for badges
        $notifications = $this->getNotificationCounts();
        
        // Get summary stats (always needed for hero section)
        $totalApplications = PermitApplication::count();
        $activeProjects = Project::whereIn('status_id', [1, 2, 3])->count();

        // Preload all tab data so switching tabs never requires a refresh
        $dashboardData = $this->getDashboardData();
        $applicationsData = $this->getApplicationsData($request);
        $typesData = $this->getTypesData($request);
        $paymentsData = $this->getPaymentsData($request);

        return view('admin.permits.index', array_merge(
            $dashboardData,
            $applicationsData,
            $typesData,
            $paymentsData,
            [
                'activeTab' => $activeTab,
                'notifications' => $notifications,
                'totalApplications' => $totalApplications,
                'activeProjects' => $activeProjects
            ]
        ));
    }
    
    /**
     * Get notification counts for badge display
     */
    private function getNotificationCounts()
    {
        $submittedCount = PermitApplication::where('status', 'submitted')->count();
        $underReviewCount = PermitApplication::where('status', 'under_review')->count();
        $unreadClientNotes = ApplicationNote::where('author_type', 'client')
            ->where('is_read', false)
            ->count();
        $pendingPayments = Payment::where('payment_method', 'manual')
            ->where('status', 'processing')
            ->count();
        
        return [
            'applications' => $submittedCount + $underReviewCount + $unreadClientNotes,
            'payments' => $pendingPayments,
            'total' => $submittedCount + $underReviewCount + $unreadClientNotes + $pendingPayments
        ];
    }
    
    /**
     * Get dashboard tab data
     */
    private function getDashboardData()
    {
        $totalApplications = PermitApplication::count();
        $pendingApplications = PermitApplication::whereIn('status', ['submitted', 'under_review'])->count();
        $needQuotation = PermitApplication::whereIn('status', ['under_review'])
            ->whereDoesntHave('quotation')
            ->count();
        $pendingPayments = Payment::where('payment_method', 'manual')
            ->where('status', 'processing')
            ->count();
        $activeProjects = Project::whereIn('status_id', [1, 2, 3])->count();
        
        $applicationsThisMonth = PermitApplication::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        $applicationsByStatus = PermitApplication::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');
        
        $recentApplications = PermitApplication::with(['client', 'permitType'])
            ->latest()
            ->take(10)
            ->get();
        
        $upcomingPayments = Payment::with(['application.client', 'application.permitType'])
            ->where('status', 'processing')
            ->latest()
            ->take(5)
            ->get();
        
        return compact(
            'totalApplications',
            'pendingApplications',
            'needQuotation',
            'pendingPayments',
            'activeProjects',
            'applicationsThisMonth',
            'applicationsByStatus',
            'recentApplications',
            'upcomingPayments'
        );
    }
    
    /**
     * Get applications tab data
     */
    private function getApplicationsData(Request $request)
    {
        $query = PermitApplication::with(['client', 'permitType', 'assignedUser'])
            ->latest();
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('permit_type')) {
            $query->where('permit_type_id', $request->permit_type);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('company_name', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Dedicated pagination parameter prevents clashes with other tabs
        $applications = $query->paginate(20, ['*'], 'applications_page')->withQueryString();
        
        $permitTypes = PermitType::where('is_active', true)->get();
        $statuses = ['submitted', 'under_review', 'quoted', 'payment_verified', 'in_progress', 'completed', 'cancelled'];
        
        return compact('applications', 'permitTypes', 'statuses');
    }
    
    /**
     * Get permit types tab data
     */
    private function getTypesData(Request $request)
    {
        $query = PermitType::withCount('applications');
        
        // Apply filters
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        // Dedicated pagination parameter prevents clashes with other tabs
        $permitTypes = $query->latest()->paginate(20, ['*'], 'types_page')->withQueryString();
        
        $totalTypes = PermitType::count();
        $activeTypes = PermitType::where('is_active', true)->count();
        $totalApplications = PermitApplication::count();
        $avgPrice = PermitType::avg('estimated_cost_min') ?: 0;
        
        return compact('permitTypes', 'totalTypes', 'activeTypes', 'totalApplications', 'avgPrice');
    }
    
    /**
     * Get payments tab data
     */
    private function getPaymentsData(Request $request)
    {
        $query = Payment::with(['application.client', 'application.permitType'])
            ->latest();
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_reference', 'like', "%{$search}%")
                  ->orWhereHas('application', function($q) use ($search) {
                      $q->where('application_number', 'like', "%{$search}%")
                        ->orWhereHas('client', function($q) use ($search) {
                            $q->where('company_name', 'like', "%{$search}%")
                              ->orWhere('name', 'like', "%{$search}%");
                        });
                  });
            });
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }
        
        // Dedicated pagination parameter prevents clashes with other tabs
        $payments = $query->paginate(20, ['*'], 'payments_page')->withQueryString();
        
        $totalPayments = Payment::count();
        $pendingPayments = Payment::where('status', 'processing')->count();
        $verifiedPayments = Payment::where('status', 'verified')->count();
        $totalAmount = Payment::where('status', 'verified')->sum('amount');
        
        return compact('payments', 'totalPayments', 'pendingPayments', 'verifiedPayments', 'totalAmount');
    }
}
