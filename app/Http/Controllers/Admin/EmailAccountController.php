<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailAccount;
use App\Models\User;
use Illuminate\Http\Request;

class EmailAccountController extends Controller
{
    /**
     * Display a listing of email accounts
     */
    public function index(Request $request)
    {
        $query = EmailAccount::with(['users', 'assignments']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('email', 'ILIKE', "%{$search}%")
                  ->orWhere('name', 'ILIKE', "%{$search}%")
                  ->orWhere('department', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } else {
                $query->where('is_active', false);
            }
        }

        $accounts = $query->orderBy('email')->paginate(20);

        // For API response
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $accounts
            ]);
        }

        // Calculate stats
        $stats = [
            'total' => EmailAccount::count(),
            'shared' => EmailAccount::where('type', 'shared')->count(),
            'personal' => EmailAccount::where('type', 'personal')->count(),
            'active_users' => User::whereHas('emailAssignments', function($q) {
                $q->where('is_active', true);
            })->distinct()->count()
        ];

        // For web view
        $emailAccounts = $accounts; // Rename for consistency with view
        return view('admin.email-accounts.index', compact('emailAccounts', 'stats'));
    }

    /**
     * Show form for creating new email account
     */
    public function create()
    {
        $departments = [
            'general' => 'General',
            'cs' => 'Customer Service',
            'sales' => 'Sales',
            'support' => 'Technical Support',
            'finance' => 'Finance',
            'technical' => 'Technical',
        ];

        $users = User::orderBy('name')->get();

        $availableUsers = $users; // Rename for consistency with view
        return view('admin.email-accounts.create', compact('departments', 'availableUsers'));
    }

    /**
     * Store a new email account
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:email_accounts,email',
            'name' => 'required|string|max:255',
            'type' => 'required|in:shared,personal',
            'department' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'auto_reply_enabled' => 'boolean',
            'auto_reply_message' => 'nullable|string',
            'signature' => 'nullable|string',
            
            // Assignment during creation
            'assign_users' => 'nullable|array',
            'assign_users.*.user_id' => 'required|exists:users,id',
            'assign_users.*.role' => 'required|in:primary,backup,viewer',
            'assign_users.*.can_send' => 'boolean',
            'assign_users.*.can_receive' => 'boolean',
            'assign_users.*.can_delete' => 'boolean',
            'assign_users.*.can_assign_others' => 'boolean',
        ]);

        // Create email account
        $account = EmailAccount::create([
            'email' => $validated['email'],
            'name' => $validated['name'],
            'type' => $validated['type'],
            'department' => $validated['department'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'auto_reply_enabled' => $validated['auto_reply_enabled'] ?? false,
            'auto_reply_message' => $validated['auto_reply_message'] ?? null,
            'signature' => $validated['signature'] ?? null,
        ]);

        // Assign users if provided
        if (!empty($validated['assign_users'])) {
            foreach ($validated['assign_users'] as $assignment) {
                $user = User::find($assignment['user_id']);
                $account->assignUser($user, [
                    'role' => $assignment['role'],
                    'can_send' => $assignment['can_send'] ?? true,
                    'can_receive' => $assignment['can_receive'] ?? true,
                    'can_delete' => $assignment['can_delete'] ?? false,
                    'can_assign_others' => $assignment['can_assign_others'] ?? false,
                    'assigned_by' => $request->user()->id,
                ]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Email account created successfully',
                'data' => $account->load('users')
            ], 201);
        }

        return redirect()
            ->route('admin.email-accounts.show', $account)
            ->with('success', 'Email account created successfully');
    }

    /**
     * Display email account details
     */
    public function show(EmailAccount $emailAccount)
    {
        $emailAccount->load([
            'users' => function($query) {
                $query->withPivot(['role', 'can_send', 'can_receive', 'can_delete', 'can_assign_others', 'is_active']);
            },
            'assignments.user',
            'inbox' => function($query) {
                $query->latest('received_at')->limit(10);
            }
        ]);

        // Statistics
        $stats = [
            'total_users' => $emailAccount->users->count(),
            'active_users' => $emailAccount->activeUsers()->count(),
            'primary_handlers' => $emailAccount->primaryUsers()->count(),
            'backup_handlers' => $emailAccount->backupUsers()->count(),
            'total_received' => $emailAccount->total_received ?? 0,
            'total_sent' => $emailAccount->total_sent ?? 0,
            'unread_count' => $emailAccount->getUnreadCount(),
            'today_count' => $emailAccount->getTodayEmailCount(),
        ];

        // Recent emails
        $recentEmails = \App\Models\EmailInbox::where('email_account_id', $emailAccount->id)
            ->orderBy('received_at', 'desc')
            ->limit(10)
            ->get();

        // Available users for assignment
        $availableUsers = \App\Models\User::whereDoesntHave('emailAssignments', function($q) use ($emailAccount) {
                $q->where('email_account_id', $emailAccount->id);
            })
            ->orderBy('name')
            ->get();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'account' => $emailAccount,
                    'stats' => $stats
                ]
            ]);
        }

        return view('admin.email-accounts.show', compact('emailAccount', 'stats', 'recentEmails', 'availableUsers'));
    }

    /**
     * Show form for editing email account
     */
    public function edit(EmailAccount $emailAccount)
    {
        $departments = [
            'general' => 'General',
            'cs' => 'Customer Service',
            'sales' => 'Sales',
            'support' => 'Technical Support',
            'finance' => 'Finance',
            'technical' => 'Technical',
        ];

        $emailAccount->load('users');

        return view('admin.email-accounts.edit', compact('emailAccount', 'departments'));
    }

    /**
     * Update email account
     */
    public function update(Request $request, EmailAccount $emailAccount)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:shared,personal',
            'department' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'auto_reply_enabled' => 'boolean',
            'auto_reply_message' => 'nullable|string',
            'signature' => 'nullable|string',
        ]);

        $emailAccount->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Email account updated successfully',
                'data' => $emailAccount
            ]);
        }

        return redirect()
            ->route('admin.email-accounts.show', $emailAccount)
            ->with('success', 'Email account updated successfully');
    }

    /**
     * Soft delete email account
     */
    public function destroy(Request $request, EmailAccount $emailAccount)
    {
        // Check if has active assignments
        $activeAssignments = $emailAccount->assignments()->where('is_active', true)->count();
        
        if ($activeAssignments > 0 && !$request->has('force')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete. Email account has {$activeAssignments} active user(s). Use force=1 to delete anyway.",
                ], 400);
            }

            return redirect()
                ->back()
                ->with('error', "Cannot delete. Email account has {$activeAssignments} active user(s).");
        }

        $emailAccount->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Email account deleted successfully'
            ]);
        }

        return redirect()
            ->route('admin.email-accounts.index')
            ->with('success', 'Email account deleted successfully');
    }

    /**
     * Get available users for assignment
     */
    public function availableUsers(EmailAccount $emailAccount)
    {
        // Get users not yet assigned to this email
        $assignedUserIds = $emailAccount->users()->pluck('users.id');
        
        $availableUsers = User::whereNotIn('id', $assignedUserIds)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'department']);

        return response()->json([
            'success' => true,
            'data' => $availableUsers
        ]);
    }

    /**
     * Get statistics for dashboard
     */
    public function stats()
    {
        $stats = [
            'total_accounts' => EmailAccount::count(),
            'active_accounts' => EmailAccount::where('is_active', true)->count(),
            'shared_accounts' => EmailAccount::where('type', 'shared')->count(),
            'personal_accounts' => EmailAccount::where('type', 'personal')->count(),
            'unassigned_accounts' => EmailAccount::doesntHave('assignments')->count(),
            'accounts_by_department' => EmailAccount::selectRaw('department, COUNT(*) as count')
                ->groupBy('department')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
