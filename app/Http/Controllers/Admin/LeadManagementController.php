<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceInquiry;
use App\Models\ConsultRequest;
use Illuminate\Http\Request;

class LeadManagementController extends Controller
{
    /**
     * Display unified lead management with tabs
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'service-inquiries');
        
        // Prepare data based on active tab
        $data = [
            'activeTab' => $activeTab,
        ];

        // Always load stats for both tabs header
        $data['serviceInquiriesCount'] = ServiceInquiry::where('status', 'new')->count();
        $data['consultationLeadsCount'] = ConsultRequest::where('estimate_status', 'new')
            ->orWhere('contacted', false)
            ->count();

        // Load tab-specific data
        if ($activeTab === 'service-inquiries') {
            $data = array_merge($data, $this->getServiceInquiriesData($request));
        } else {
            $data = array_merge($data, $this->getConsultationLeadsData($request));
        }

        return view('admin.leads.index', $data);
    }

    /**
     * Get Service Inquiries data
     */
    private function getServiceInquiriesData(Request $request): array
    {
        $query = ServiceInquiry::with(['client', 'contactedBy'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('inquiry_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $inquiries = $query->paginate(20);
        $inquiries->appends($request->except('page'));

        // Stats for dashboard cards
        $stats = [
            'total' => ServiceInquiry::count(),
            'new' => ServiceInquiry::where('status', 'new')->count(),
            'analyzed' => ServiceInquiry::where('status', 'analyzed')->count(),
            'contacted' => ServiceInquiry::where('status', 'contacted')->count(),
            'converted' => ServiceInquiry::where('status', 'converted')->count(),
            'high_priority' => ServiceInquiry::where('priority', 'high')->count(),
            'this_week' => ServiceInquiry::where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month' => ServiceInquiry::where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        return [
            'inquiries' => $inquiries,
            'serviceInquiriesStats' => $stats,
        ];
    }

    /**
     * Get Consultation Leads data
     */
    private function getConsultationLeadsData(Request $request): array
    {
        $query = ConsultRequest::with(['kbli', 'reviewer', 'client'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('estimate_status', $request->status);
        }

        // Filter by conversion status
        if ($request->filled('converted')) {
            if ($request->converted === 'yes') {
                $query->where('converted_to_client', true);
            } elseif ($request->converted === 'no') {
                $query->where('converted_to_client', false);
            }
        }

        // Filter by contacted status
        if ($request->filled('contacted')) {
            if ($request->contacted === 'yes') {
                $query->where('contacted', true);
            } elseif ($request->contacted === 'no') {
                $query->where('contacted', false);
            }
        }

        // Filter by business size
        if ($request->filled('business_size')) {
            $query->where('business_size', $request->business_size);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('kbli_code', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // High value filter (above 10M)
        if ($request->filled('high_value')) {
            $query->whereRaw("CAST(auto_estimate->'cost_summary'->>'grand_total' AS BIGINT) >= ?", [10000000]);
        }

        $consultations = $query->paginate(20);
        $consultations->appends($request->except('page'));

        // Basic stats for dashboard
        $stats = [
            'total' => ConsultRequest::count(),
            'new' => ConsultRequest::where('estimate_status', 'new')->count(),
            'contacted' => ConsultRequest::where('contacted', true)->count(),
            'converted' => ConsultRequest::where('converted_to_client', true)->count(),
            'pending_review' => ConsultRequest::where('estimate_status', 'pending_review')->count(),
            'high_value' => ConsultRequest::whereRaw("CAST(COALESCE(auto_estimate->'cost_summary'->>'grand_total', '0') AS BIGINT) >= ?", [10000000])->count(),
            'this_week' => ConsultRequest::where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month' => ConsultRequest::where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        return [
            'consultations' => $consultations,
            'consultationLeadsStats' => $stats,
        ];
    }
}
