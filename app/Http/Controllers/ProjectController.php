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
use App\Http\Controllers\Traits\AuthorizesRequests;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizePermissions('projects');
        
        // Additional authorization for custom actions
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->can('projects.edit')) {
                abort(403, 'Anda tidak memiliki akses untuk mengubah status proyek.');
            }
            return $next($request);
        })->only(['updateStatus']);
    }

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
        
        // In Progress: All active work statuses (not lead, not completed, not cancelled)
        $inProgressProjects = Project::whereHas('status', function($q) {
            $q->whereIn('code', [
                'CONTRACT',           // Kontrak
                'PREPARATION',        // Persiapan
                'IN_PROGRESS',        // Dalam Pengerjaan
                'REVIEW',             // Review
                'WAITING_APPROVAL',   // Menunggu Persetujuan
                'REVISION'            // Revisi
            ]);
        })->count();
        
        // Completed: Projects marked as completed or closed successfully
        $completedProjects = Project::whereHas('status', function($q) {
            $q->whereIn('code', ['COMPLETED', 'CLOSED']);
        })->count();
        
        // Overdue: Projects past deadline and not completed/cancelled/on hold
        $overdueProjects = Project::where('deadline', '<', now())
            ->whereHas('status', function($q) {
                $q->whereNotIn('code', ['COMPLETED', 'CLOSED', 'CANCELLED', 'ON_HOLD']);
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
        
        // Auto-fill client information from Client model
        if (isset($validated['client_id'])) {
            $client = \App\Models\Client::find($validated['client_id']);
            if ($client) {
                $validated['client_name'] = $client->name;
                $validated['client_contact'] = $client->contact_person;
                $validated['client_address'] = $client->address;
                $validated['client_company'] = $client->company_name;
            }
        }
        
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
            ->get()
            ->sum(function ($expense) {
                $remaining = ($expense->amount ?? 0) - ($expense->receivable_paid_amount ?? 0);

                return max($remaining, 0);
            });
        
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
        
        // Auto-fill client information if client_id changed
        if (isset($validated['client_id']) && $validated['client_id'] != $project->client_id) {
            $client = \App\Models\Client::find($validated['client_id']);
            if ($client) {
                $validated['client_name'] = $client->name;
                $validated['client_contact'] = $client->contact_person;
                $validated['client_address'] = $client->address;
                $validated['client_company'] = $client->company_name;
            }
        }
        
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

            // Calculate income from multiple sources:
            // 1. Invoice payments (from payment_schedules with paid status)
            $invoiceIncome = $project->paymentSchedules()
                ->where('status', 'paid')
                ->whereNotNull('paid_date')
                ->whereMonth('paid_date', $date->month)
                ->whereYear('paid_date', $date->year)
                ->sum('amount');
            
            // 2. Direct project payments (legacy system - manual payments not linked to invoice)
            $directIncome = $project->payments()
                ->whereNull('invoice_id') // Only count payments NOT linked to invoice
                ->whereMonth('payment_date', $date->month)
                ->whereYear('payment_date', $date->year)
                ->sum('amount');
            
            $totalIncome = (float) ($invoiceIncome + $directIncome);
            $income[] = $totalIncome;

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
