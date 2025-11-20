<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectPayment;
use App\Models\ProjectExpense;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        // Calculate key metrics for the period
        $period = request('period', '3months');
        $startDate = $this->getStartDate($period);
        
        $metrics = [
            'revenue' => (float) ProjectPayment::where('payment_date', '>=', $startDate)->sum('amount'),
            'expenses' => (float) ProjectExpense::where('expense_date', '>=', $startDate)->sum('amount'),
            'active_projects' => Project::whereIn('status_id', [2, 3, 4, 5, 6, 7, 8])->count(), // Kontrak through Menunggu Persetujuan
            'completed_projects' => Project::where('status_id', 9) // SK Terbit
                ->where('updated_at', '>=', $startDate)
                ->count(),
        ];
        
        // Calculate growth percentages
        $previousStartDate = $this->getPreviousStartDate($period);
        $previousRevenue = (float) ProjectPayment::whereBetween('payment_date', [$previousStartDate, $startDate])->sum('amount');
        $previousExpenses = (float) ProjectExpense::whereBetween('expense_date', [$previousStartDate, $startDate])->sum('amount');
        
        $metrics['revenue_growth'] = $previousRevenue > 0 
            ? round((($metrics['revenue'] - $previousRevenue) / $previousRevenue) * 100, 1)
            : 0;
            
        $metrics['expense_growth'] = $previousExpenses > 0
            ? round((($metrics['expenses'] - $previousExpenses) / $previousExpenses) * 100, 1)
            : 0;
        
        // Calculate profit margin
        $metrics['profit_margin'] = $metrics['revenue'] > 0
            ? round((($metrics['revenue'] - $metrics['expenses']) / $metrics['revenue']) * 100, 1)
            : 0;
        
        return view('mobile.reports.index', compact('metrics'));
    }
    
    public function financial()
    {
        $period = request('period', '3months');
        $startDate = $this->getStartDate($period);
        
        // Financial data
        $cashFlow = [
            'income' => (float) ProjectPayment::where('payment_date', '>=', $startDate)->sum('amount'),
            'expenses' => (float) ProjectExpense::where('expense_date', '>=', $startDate)->sum('amount'),
        ];
        $cashFlow['net'] = $cashFlow['income'] - $cashFlow['expenses'];
        
        // Receivables
        $receivables = [
            'total' => (float) Invoice::where('status', '!=', 'paid')->sum('total_amount'),
            'overdue' => (float) Invoice::where('status', 'overdue')->sum('total_amount'),
        ];
        
        // Monthly breakdown
        $monthly = [];
        for ($i = 2; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $monthly[] = [
                'month' => $monthStart->format('M Y'),
                'income' => (float) ProjectPayment::whereBetween('payment_date', [$monthStart, $monthEnd])->sum('amount'),
                'expenses' => (float) ProjectExpense::whereBetween('expense_date', [$monthStart, $monthEnd])->sum('amount'),
            ];
        }
        
        return view('mobile.reports.financial', compact('cashFlow', 'receivables', 'monthly'));
    }
    
    public function projects()
    {
        // Project statistics
        $stats = [
            'total' => Project::count(),
            'active' => Project::whereIn('status_id', [2,3,4,5,6,7,8])->count(),
            'completed' => Project::where('status_id', 9)->count(),
            'cancelled' => Project::where('status_id', 10)->count(),
        ];
        
        // By status
        $byStatus = \DB::table('projects')
            ->join('project_statuses', 'projects.status_id', '=', 'project_statuses.id')
            ->select('project_statuses.name', \DB::raw('count(*) as count'))
            ->groupBy('project_statuses.name')
            ->get();
        
        // Recent completions
        $recentCompletions = Project::where('status_id', 9)
            ->with('status')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('mobile.reports.projects', compact('stats', 'byStatus', 'recentCompletions'));
    }
    
    public function team()
    {
        // Team statistics
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'on_project' => User::whereHas('assignedTasks.project', function($q) {
                $q->whereIn('status_id', [2,3,4,5,6,7,8]);
            })->count(),
        ];
        
        // Task completion by user
        $userPerformance = User::with('role')
            ->withCount([
                'assignedTasks as total_tasks',
                'assignedTasks as completed_tasks' => function($q) {
                    $q->where('status', 'completed');
                }
            ])->where('is_active', true)->limit(10)->get();
        
        return view('mobile.reports.team', compact('stats', 'userPerformance'));
    }
    
    public function clients()
    {
        // Client statistics
        $stats = [
            'total' => Client::count(),
            'active' => Client::where('status', 'active')->count(),
            'inactive' => Client::where('status', 'inactive')->count(),
        ];
        
        // Top clients by project count
        $topClients = Client::withCount('projects')
            ->orderBy('projects_count', 'desc')
            ->limit(10)
            ->get();
        
        return view('mobile.reports.clients', compact('stats', 'topClients'));
    }
    
    private function getStartDate($period)
    {
        return match($period) {
            'month' => Carbon::now()->startOfMonth(),
            '3months' => Carbon::now()->subMonths(3)->startOfDay(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->subMonths(3)->startOfDay(),
        };
    }
    
    private function getPreviousStartDate($period)
    {
        return match($period) {
            'month' => Carbon::now()->subMonth()->startOfMonth(),
            '3months' => Carbon::now()->subMonths(6)->startOfDay(),
            'year' => Carbon::now()->subYear()->startOfYear(),
            default => Carbon::now()->subMonths(6)->startOfDay(),
        };
    }
}
