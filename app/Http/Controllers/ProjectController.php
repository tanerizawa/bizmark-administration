<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Institution;
use App\Models\PermitTemplate;
use App\Models\PermitType;
use App\Models\Client;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Project::with(['status', 'institution', 'client']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status_id', $request->status);
        }

        // Filter by client
        if ($request->filled('client')) {
            $query->where('client_id', $request->client);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $projects = $query->paginate(15)->withQueryString();

        // Get filter options
        $statuses = ProjectStatus::all();
        $clients = \App\Models\Client::orderBy('name')->get();

        // Calculate statistics for this filtered view
        $totalProjects = Project::count();
        $inProgressProjects = Project::whereHas('status', function($q) {
            $q->whereIn('code', ['KONTRAK', 'PENGUMPULAN_DOK', 'PROSES_DLH', 'PROSES_BPN', 'PROSES_OSS', 'PROSES_NOTARIS', 'MENUNGGU_PERSETUJUAN']);
        })->count();
        $completedProjects = Project::whereHas('status', function($q) {
            $q->where('code', 'SK_TERBIT');
        })->count();
        $overdueProjects = Project::where('deadline', '<', now())
            ->whereHas('status', function($q) {
                $q->whereNotIn('code', ['SK_TERBIT', 'DIBATALKAN']);
            })->count();

        return view('projects.index', compact(
            'projects', 
            'statuses', 
            'clients',
            'totalProjects',
            'inProgressProjects', 
            'completedProjects',
            'overdueProjects'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statuses = ProjectStatus::all();
        $institutions = Institution::all();
        $clients = Client::where('status', 'active')
                        ->orderBy('name')
                        ->get();

        return view('projects.create', compact('statuses', 'institutions', 'clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $validated = $request->validated();
        
        $project = Project::create($validated);

        // Log project creation
        $project->logs()->create([
            'action' => 'created',
            'description' => "Proyek '{$project->name}' berhasil dibuat",
            'new_values' => $validated,
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Proyek berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $project->load([
            'status', 
            'institution',
            'client',
            'tasks',
            'documents',
            'logs',
            'payments.bankAccount',
            'expenses.bankAccount',
            'permits.permitType',
            'permits.dependencies.dependsOnPermit.permitType',
            'invoices.items',
            'paymentSchedules',
        ]);

        $statuses = ProjectStatus::all();
        $permitTemplates = PermitTemplate::with(['items.permitType'])->get();
        $permitTypes = PermitType::where('is_active', true)->orderBy('name')->get();

        // Calculate financial overview (Sprint 6)
        // Use contract_value first, fallback to budget for backward compatibility
        $totalBudget = $project->contract_value > 0 ? $project->contract_value : ($project->budget ?? 0);
        $totalInvoiced = $project->invoices()->sum('total_amount');
        
        // Total received calculation (fixed for invoice-linked payments)
        // Since payments are now linked to invoices via invoice_id,
        // we calculate from invoice paid_amount to avoid double counting
        $invoicePayments = $project->invoices()->sum('paid_amount');
        
        // Add manual payments that are NOT linked to any invoice (legacy/non-invoice payments)
        $manualPaymentsNotLinked = $project->payments()
            ->whereNull('invoice_id')
            ->sum('amount');
        
        $totalReceived = $invoicePayments + $manualPaymentsNotLinked;
        
        $totalExpenses = $project->expenses()->sum('amount');
        $totalScheduled = $project->paymentSchedules()->where('status', 'pending')->sum('amount');
        
        $budgetRemaining = $totalBudget - $totalInvoiced;
        
        // Calculate outstanding receivables (kasbon yang belum lunas)
        $receivableOutstanding = $project->expenses()
            ->where('is_receivable', true)
            ->whereIn('receivable_status', ['pending', 'partial'])
            ->sum('amount');
        
        $profitMargin = $totalReceived - $totalExpenses;

        // Get monthly data for chart (last 6 months)
        $monthlyData = $this->getMonthlyFinancialData($project);

        // Calculate permits statistics
        $permits = $project->permits;
        $statistics = [
            'total' => $permits->count(),
            'completed' => $permits->where('status', 'APPROVED')->count(),
            'in_progress' => $permits->whereIn('status', ['IN_PROGRESS', 'SUBMITTED'])->count(),
            'not_started' => $permits->where('status', 'NOT_STARTED')->count(),
            'completion_rate' => $permits->count() > 0 ? round(($permits->where('status', 'APPROVED')->count() / $permits->count()) * 100, 1) : 0,
        ];

        return view('projects.show', compact(
            'project', 
            'statuses', 
            'permitTemplates', 
            'permitTypes',
            'statistics',
            'totalBudget',
            'totalInvoiced',
            'totalReceived',
            'totalExpenses',
            'totalScheduled',
            'budgetRemaining',
            'receivableOutstanding',
            'profitMargin',
            'monthlyData'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $statuses = ProjectStatus::all();
        $institutions = Institution::all();
        $clients = Client::where('status', 'active')
                        ->orderBy('name')
                        ->get();

        return view('projects.edit', compact('project', 'statuses', 'institutions', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $validated = $request->validated();
        $oldValues = $project->toArray();
        
        $project->update($validated);

        // Log project update
        $project->logs()->create([
            'action' => 'updated',
            'description' => "Proyek '{$project->name}' diperbarui",
            'old_values' => $oldValues,
            'new_values' => $validated,
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Proyek berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        // Log project deletion
        $project->logs()->create([
            'action' => 'deleted',
            'description' => "Proyek '{$project->name}' dihapus",
            'old_values' => $project->toArray(),
        ]);

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil dihapus.');
    }

    /**
     * Update project status
     */
    public function updateStatus(Request $request, Project $project)
    {
        $request->validate([
            'status_id' => 'required|exists:project_statuses,id',
            'notes' => 'nullable|string|max:1000'
        ]);

        $oldStatus = $project->status;
        $newStatus = ProjectStatus::find($request->status_id);

        $project->update(['status_id' => $request->status_id]);

        // Create log description
        $description = "Status proyek diubah dari '{$oldStatus->name}' menjadi '{$newStatus->name}'";
        if ($request->notes) {
            $description .= ". Catatan: {$request->notes}";
        }

        // Log status change
        $project->logs()->create([
            'action' => 'status_changed',
            'description' => $description,
            'old_values' => ['status_id' => $oldStatus->id],
            'new_values' => ['status_id' => $newStatus->id, 'notes' => $request->notes],
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Status proyek berhasil diperbarui.');
    }

    /**
     * Get monthly financial data for charts (Sprint 6)
     */
    private function getMonthlyFinancialData($project)
    {
        $months = [];
        $income = [];
        $expenses = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            // Calculate income - fixed to avoid double counting
            // Since payments are now linked to invoices via invoice_id,
            // we calculate income from ALL payments in this month
            // (both invoice-linked and manual payments)
            $monthIncome = $project->payments()
                ->whereMonth('payment_date', $date->month)
                ->whereYear('payment_date', $date->year)
                ->sum('amount');
            
            $income[] = (float) $monthIncome;

            // Calculate expenses
            $monthExpense = $project->expenses()
                ->whereMonth('expense_date', $date->month)
                ->whereYear('expense_date', $date->year)
                ->sum('amount');
            $expenses[] = (float) $monthExpense;
        }

        return [
            'labels' => $months,
            'income' => $income,
            'expenses' => $expenses,
        ];
    }
}
