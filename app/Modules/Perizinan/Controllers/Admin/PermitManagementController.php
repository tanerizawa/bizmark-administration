<?php

namespace App\Modules\Perizinan\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermitApplication;
use App\Models\PermitType;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ApplicationNote;
use App\Models\Kbli;
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
        $allowedTabs = ['dashboard', 'applications', 'types', 'kbli', 'payments'];
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
        $applicationsData = $this->getApplicationsData($request, $activeTab);
        $typesData = $this->getTypesData($request, $activeTab);
        $kbliData = $this->getKbliData($request, $activeTab);
        $paymentsData = $this->getPaymentsData($request, $activeTab);

        return view('admin.permits.index', array_merge(
            $dashboardData,
            $applicationsData,
            $typesData,
            $kbliData,
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
    private function getApplicationsData(Request $request, string $activeTab = 'applications')
    {
        $query = PermitApplication::with(['client', 'permitType', 'reviewer'])
            ->latest();
        
        // Only apply filters when on applications tab
        if ($activeTab === 'applications') {
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
    private function getTypesData(Request $request, string $activeTab = 'types')
    {
        $query = PermitType::withCount('applications');
        
        // Only apply filters when on types tab
        if ($activeTab === 'types') {
            if ($request->filled('search')) {
                $query->where('name', 'like', "%{$request->search}%");
            }
            
            if ($request->has('is_active')) {
                $query->where('is_active', $request->is_active);
            }
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
     * Get KBLI tab data
     */
    private function getKbliData(Request $request, string $activeTab = 'kbli')
    {
        $query = Kbli::orderBy('sector')->orderBy('code');
        
        // Only apply filters when on kbli tab
        if ($activeTab === 'kbli') {
            if ($request->filled('category')) {
                $query->where('sector', $request->category);
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $searchVariants = $this->generateSearchVariants($search);
                
                $query->where(function($q) use ($searchVariants) {
                    foreach ($searchVariants as $variant) {
                        $q->orWhere('code', 'like', "%{$variant}%")
                          ->orWhere('description', 'like', "%{$variant}%")
                          ->orWhere('activities', 'like', "%{$variant}%");
                    }
                });
            }
        }
        
        // Dedicated pagination parameter
        $kbliData = $query->paginate(20, ['*'], 'kbli_page')->withQueryString();
        
        // Get actual sectors from database
        $categories = Kbli::select('sector')->distinct()->orderBy('sector')->pluck('sector')->toArray();
        
        // Get KBLI stats for display
        $kbliStats = [
            'total' => Kbli::count(),
            'by_sector' => Kbli::selectRaw('sector, count(*) as count')
                ->groupBy('sector')
                ->orderBy('sector')
                ->get(),
        ];
        
        return compact('kbliData', 'categories', 'kbliStats');
    }
    
    /**
     * Generate search variants for fuzzy matching
     * Handles common Indonesian/English spelling variations
     */
    private function generateSearchVariants(string $search): array
    {
        $search = strtolower(trim($search));
        $variants = [$search];
        
        // Common English to Indonesian spelling mappings
        $mappings = [
            // Ending variations
            'estate' => 'estat',
            'service' => 'servis',
            'office' => 'ofis',
            'computer' => 'komputer',
            'technology' => 'teknologi',
            'electronic' => 'elektronik',
            'electric' => 'elektrik',
            'system' => 'sistem',
            'industry' => 'industri',
            'factory' => 'pabrik',
            'machine' => 'mesin',
            'chemical' => 'kimia',
            'pharmacy' => 'farmasi',
            'textile' => 'tekstil',
            'plastic' => 'plastik',
            'metal' => 'logam',
            'construction' => 'konstruksi',
            'consultant' => 'konsultan',
            'transport' => 'transportasi',
            'communication' => 'komunikasi',
            'insurance' => 'asuransi',
            'property' => 'properti',
            'restaurant' => 'restoran',
            'hotel' => 'hotel',
            'education' => 'pendidikan',
            'health' => 'kesehatan',
            'hospital' => 'rumah sakit',
            'agriculture' => 'pertanian',
            'fishery' => 'perikanan',
            'mining' => 'pertambangan',
            'petroleum' => 'minyak',
            'manufacture' => 'manufaktur',
            'retail' => 'ritel',
            'wholesale' => 'grosir',
            'finance' => 'keuangan',
            'bank' => 'bank',
            'security' => 'keamanan',
            'cleaning' => 'kebersihan',
            'catering' => 'katering',
            'printing' => 'percetakan',
            'advertising' => 'periklanan',
            'media' => 'media',
            'software' => 'perangkat lunak',
            'hardware' => 'perangkat keras',
            'furniture' => 'furnitur',
            'fashion' => 'fesyen',
            'beauty' => 'kecantikan',
            'salon' => 'salon',
            'gym' => 'gym',
            'fitness' => 'kebugaran',
            'sport' => 'olahraga',
            'travel' => 'perjalanan',
            'tour' => 'wisata',
            'agency' => 'agen',
            'export' => 'ekspor',
            'import' => 'impor',
        ];
        
        // Apply mappings in both directions
        foreach ($mappings as $en => $id) {
            if (str_contains($search, $en)) {
                $variants[] = str_replace($en, $id, $search);
            }
            if (str_contains($search, $id)) {
                $variants[] = str_replace($id, $en, $search);
            }
        }
        
        // Handle trailing 'e' variations (estate/estat, service/servis)
        if (preg_match('/e$/', $search)) {
            $variants[] = rtrim($search, 'e');
        } else {
            $variants[] = $search . 'e';
        }
        
        // Handle double consonant simplification
        $variants[] = preg_replace('/([bcdfghjklmnpqrstvwxyz])\1/', '$1', $search);
        
        // Handle 'ph' vs 'f' (photo/foto)
        if (str_contains($search, 'ph')) {
            $variants[] = str_replace('ph', 'f', $search);
        }
        if (str_contains($search, 'f')) {
            $variants[] = str_replace('f', 'ph', $search);
        }
        
        // Handle 'c' vs 'k' (electric/elektrik)
        if (str_contains($search, 'c') && !str_contains($search, 'ch')) {
            $variants[] = str_replace('c', 'k', $search);
        }
        
        // Handle 'y' vs 'i' ending (industry/industri)
        if (preg_match('/y$/', $search)) {
            $variants[] = preg_replace('/y$/', 'i', $search);
        }
        
        return array_unique($variants);
    }

    /**
     * Get payments tab data
     */
    private function getPaymentsData(Request $request, string $activeTab = 'payments')
    {
        $query = Payment::with(['client', 'quotation'])
            ->latest();
        
        // Only apply filters when on payments tab
        if ($activeTab === 'payments') {
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('payment_number', 'like', "%{$search}%")
                      ->orWhereHas('client', function($q) use ($search) {
                          $q->where('company_name', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                      });
                });
            }
            
            if ($request->filled('date_from')) {
                $query->whereDate('paid_at', '>=', $request->date_from);
            }
            
            if ($request->filled('date_to')) {
                $query->whereDate('paid_at', '<=', $request->date_to);
            }
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
