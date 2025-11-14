<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailAccount;
use App\Models\EmailAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmailAssignmentController extends Controller
{
    /**
     * Assign user to email account
     */
    public function assign(Request $request, EmailAccount $emailAccount)
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('email_assignments')->where(function ($query) use ($emailAccount) {
                    return $query->where('email_account_id', $emailAccount->id);
                })->ignore($request->user_id, 'user_id'),
            ],
            'role' => 'required|in:primary,backup,viewer',
            'can_send' => 'boolean',
            'can_receive' => 'boolean',
            'can_delete' => 'boolean',
            'can_assign_others' => 'boolean',
            'notify_on_receive' => 'boolean',
            'notify_on_reply' => 'boolean',
            'notify_on_mention' => 'boolean',
            'priority' => 'nullable|integer|min:0|max:10',
        ]);

        $user = User::findOrFail($validated['user_id']);

        // Check if personal email trying to assign multiple users
        if ($emailAccount->type === 'personal') {
            $existingAssignments = $emailAccount->assignments()->count();
            if ($existingAssignments > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personal email accounts can only have one user assigned'
                ], 400);
            }
        }

        // Assign user
        $assignment = $emailAccount->assignUser($user, [
            'role' => $validated['role'],
            'can_send' => $validated['can_send'] ?? true,
            'can_receive' => $validated['can_receive'] ?? true,
            'can_delete' => $validated['can_delete'] ?? false,
            'can_assign_others' => $validated['can_assign_others'] ?? false,
            'notify_on_receive' => $validated['notify_on_receive'] ?? true,
            'notify_on_reply' => $validated['notify_on_reply'] ?? false,
            'notify_on_mention' => $validated['notify_on_mention'] ?? true,
            'priority' => $validated['priority'] ?? 5,
            'assigned_by' => $request->user()->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "User {$user->name} assigned to {$emailAccount->email}",
                'data' => $assignment->load(['user', 'emailAccount'])
            ], 201);
        }

        return redirect()
            ->back()
            ->with('success', "User {$user->name} assigned successfully");
    }

    /**
     * Update assignment permissions
     */
    public function updatePermissions(Request $request, EmailAccount $emailAccount, User $user)
    {
        $validated = $request->validate([
            'role' => 'sometimes|in:primary,backup,viewer',
            'can_send' => 'sometimes|boolean',
            'can_receive' => 'sometimes|boolean',
            'can_delete' => 'sometimes|boolean',
            'can_assign_others' => 'sometimes|boolean',
            'notify_on_receive' => 'sometimes|boolean',
            'notify_on_reply' => 'sometimes|boolean',
            'notify_on_mention' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'priority' => 'sometimes|integer|min:0|max:10',
        ]);

        $assignment = EmailAssignment::where('email_account_id', $emailAccount->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $assignment->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Permissions updated successfully',
                'data' => $assignment->fresh(['user', 'emailAccount'])
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Permissions updated successfully');
    }

    /**
     * Remove user from email account
     */
    public function unassign(Request $request, EmailAccount $emailAccount, User $user)
    {
        $assignment = EmailAssignment::where('email_account_id', $emailAccount->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$assignment) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Assignment not found'
                ], 404);
            }

            return redirect()
                ->back()
                ->with('error', 'Assignment not found');
        }

        // Check if this is the last primary handler
        if ($assignment->role === 'primary') {
            $primaryCount = EmailAssignment::where('email_account_id', $emailAccount->id)
                ->where('role', 'primary')
                ->where('is_active', true)
                ->count();

            if ($primaryCount <= 1 && !$request->has('force')) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot remove the last primary handler. Assign another primary handler first or use force=1.'
                    ], 400);
                }

                return redirect()
                    ->back()
                    ->with('error', 'Cannot remove the last primary handler');
            }
        }

        $emailAccount->removeUser($user);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "User {$user->name} removed from {$emailAccount->email}"
            ]);
        }

        return redirect()
            ->back()
            ->with('success', "User removed successfully");
    }

    /**
     * Bulk assign multiple users to email account
     */
    public function bulkAssign(Request $request, EmailAccount $emailAccount)
    {
        $validated = $request->validate([
            'assignments' => 'required|array|min:1',
            'assignments.*.user_id' => 'required|exists:users,id',
            'assignments.*.role' => 'required|in:primary,backup,viewer',
            'assignments.*.can_send' => 'boolean',
            'assignments.*.can_receive' => 'boolean',
            'assignments.*.can_delete' => 'boolean',
            'assignments.*.can_assign_others' => 'boolean',
        ]);

        $results = [
            'success' => [],
            'failed' => [],
        ];

        foreach ($validated['assignments'] as $assignmentData) {
            try {
                $user = User::find($assignmentData['user_id']);
                
                // Check if already assigned
                $existing = EmailAssignment::where('email_account_id', $emailAccount->id)
                    ->where('user_id', $user->id)
                    ->first();

                if ($existing) {
                    $results['failed'][] = [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'reason' => 'Already assigned'
                    ];
                    continue;
                }

                $emailAccount->assignUser($user, [
                    'role' => $assignmentData['role'],
                    'can_send' => $assignmentData['can_send'] ?? true,
                    'can_receive' => $assignmentData['can_receive'] ?? true,
                    'can_delete' => $assignmentData['can_delete'] ?? false,
                    'can_assign_others' => $assignmentData['can_assign_others'] ?? false,
                    'assigned_by' => $request->user()->id,
                ]);

                $results['success'][] = [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $assignmentData['role']
                ];

            } catch (\Exception $e) {
                $results['failed'][] = [
                    'user_id' => $assignmentData['user_id'] ?? null,
                    'reason' => $e->getMessage()
                ];
            }
        }

        $message = count($results['success']) . ' user(s) assigned successfully';
        if (count($results['failed']) > 0) {
            $message .= ', ' . count($results['failed']) . ' failed';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $results
            ]);
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }

    /**
     * Get user's assigned email accounts
     */
    public function userEmails(User $user)
    {
        $assignments = EmailAssignment::where('user_id', $user->id)
            ->with(['emailAccount'])
            ->get();

        $grouped = $assignments->groupBy('role')->map(function($group) {
            return $group->map(function($assignment) {
                return [
                    'email' => $assignment->emailAccount->email,
                    'name' => $assignment->emailAccount->name,
                    'type' => $assignment->emailAccount->type,
                    'department' => $assignment->emailAccount->department,
                    'role' => $assignment->role,
                    'permissions' => [
                        'can_send' => $assignment->can_send,
                        'can_receive' => $assignment->can_receive,
                        'can_delete' => $assignment->can_delete,
                        'can_assign_others' => $assignment->can_assign_others,
                    ],
                    'is_active' => $assignment->is_active,
                ];
            });
        });

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user->only(['id', 'name', 'email', 'department']),
                'assignments' => $grouped,
                'total' => $assignments->count(),
            ]
        ]);
    }

    /**
     * Transfer primary role to another user
     */
    public function transferPrimary(Request $request, EmailAccount $emailAccount)
    {
        $validated = $request->validate([
            'from_user_id' => 'required|exists:users,id',
            'to_user_id' => 'required|exists:users,id|different:from_user_id',
        ]);

        $fromAssignment = EmailAssignment::where('email_account_id', $emailAccount->id)
            ->where('user_id', $validated['from_user_id'])
            ->where('role', 'primary')
            ->firstOrFail();

        $toAssignment = EmailAssignment::where('email_account_id', $emailAccount->id)
            ->where('user_id', $validated['to_user_id'])
            ->first();

        if (!$toAssignment) {
            return response()->json([
                'success' => false,
                'message' => 'Target user is not assigned to this email account'
            ], 400);
        }

        // Transfer: from becomes backup, to becomes primary
        $fromAssignment->update(['role' => 'backup']);
        $toAssignment->update([
            'role' => 'primary',
            'can_send' => true,
            'can_receive' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Primary role transferred successfully',
            'data' => [
                'from' => $fromAssignment->fresh('user'),
                'to' => $toAssignment->fresh('user'),
            ]
        ]);
    }
}
