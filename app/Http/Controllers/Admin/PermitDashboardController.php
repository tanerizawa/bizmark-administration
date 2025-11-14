<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermitApplication;
use App\Models\Quotation;
use App\Models\Payment;
use App\Models\ApplicationStatusLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermitDashboardController extends Controller
{
    /**
     * Display permit application dashboard
     */
    public function index()
    {
        // Pending Applications (submitted, need review)
        $pendingApplications = PermitApplication::whereIn('status', ['submitted', 'under_review'])
            ->count();

        // Pending Quotations (applications that need quotation)
        $needQuotation = PermitApplication::where('status', 'under_review')
            ->whereDoesntHave('quotation')
            ->count();

        // Pending Payments (payments awaiting verification)
        $pendingPayments = Payment::where('status', 'processing')->count();

        // Revenue This Month
        $revenueThisMonth = Payment::where('status', 'success')
            ->whereMonth('paid_at', Carbon::now()->month)
            ->whereYear('paid_at', Carbon::now()->year)
            ->sum('paid_amount');

        // Applications by Status (for chart)
        $applicationsByStatus = PermitApplication::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->total];
            });

        // Recent Activity (last 10 status changes)
        $recentActivity = ApplicationStatusLog::with(['application.client', 'application.permitType'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Statistics for cards
        $totalApplications = PermitApplication::count();
        $totalRevenue = Payment::where('status', 'success')->sum('paid_amount');
        $activeProjects = PermitApplication::where('status', 'converted_to_project')->count();

        // Applications This Month
        $applicationsThisMonth = PermitApplication::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        return view('admin.permit-dashboard.index', compact(
            'pendingApplications',
            'needQuotation',
            'pendingPayments',
            'revenueThisMonth',
            'applicationsByStatus',
            'recentActivity',
            'totalApplications',
            'totalRevenue',
            'activeProjects',
            'applicationsThisMonth'
        ));
    }
}
