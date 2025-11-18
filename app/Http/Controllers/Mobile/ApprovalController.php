<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\ProjectExpense;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApprovalController extends Controller
{
    /**
     * Approvals Dashboard
     * Show all pending items that need user's approval
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'all'); // all, expenses, documents, invoices
        
        $approvals = $this->getPendingApprovals($type);
        
        $stats = [
            'total' => $approvals->count(),
            'expenses' => $this->countByType('expenses'),
            'documents' => $this->countByType('documents'),
            'invoices' => $this->countByType('invoices'),
        ];
        
        return view('mobile.approvals.index', [
            'approvals' => $approvals,
            'stats' => $stats,
            'currentType' => $type
        ]);
    }
    
    /**
     * Show pending approvals only
     */
    public function pending(Request $request)
    {
        $approvals = $this->getPendingApprovals('all');
        
        if ($request->expectsJson()) {
            return response()->json([
                'approvals' => $approvals,
                'count' => $approvals->count()
            ]);
        }
        
        return view('mobile.approvals.pending', compact('approvals'));
    }
    
    /**
     * Show approval detail
     */
    public function show(Request $request, $type, $id)
    {
        $item = $this->getApprovalItem($type, $id);
        
        if (!$item) {
            abort(404, 'Item tidak ditemukan');
        }
        
        return view('mobile.approvals.show', [
            'item' => $item,
            'type' => $type
        ]);
    }
    
    /**
     * Approve single item
     */
    public function approve(Request $request, $type, $id)
    {
        $request->validate([
            'note' => 'nullable|string|max:500'
        ]);
        
        $item = $this->getApprovalItem($type, $id);
        
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan'
            ], 404);
        }
        
        DB::beginTransaction();
        try {
            $this->processApproval($type, $item, 'approved', $request->note);
            
            // Log activity
            activity()
                ->performedOn($item)
                ->causedBy(auth()->user())
                ->withProperties(['type' => $type, 'action' => 'approved'])
                ->log('approval_approved');
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil disetujui',
                'item' => [
                    'id' => $item->id,
                    'type' => $type,
                    'status' => 'approved'
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Approval error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Reject single item
     */
    public function reject(Request $request, $type, $id)
    {
        $request->validate([
            'note' => 'required|string|max:500',
            'reason' => 'required|string'
        ]);
        
        $item = $this->getApprovalItem($type, $id);
        
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan'
            ], 404);
        }
        
        DB::beginTransaction();
        try {
            $this->processApproval($type, $item, 'rejected', $request->note, $request->reason);
            
            // Log activity
            activity()
                ->performedOn($item)
                ->causedBy(auth()->user())
                ->withProperties([
                    'type' => $type,
                    'action' => 'rejected',
                    'reason' => $request->reason
                ])
                ->log('approval_rejected');
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil ditolak',
                'item' => [
                    'id' => $item->id,
                    'type' => $type,
                    'status' => 'rejected'
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Rejection error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Bulk approve multiple items
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.type' => 'required|in:expenses,documents,invoices',
            'items.*.id' => 'required|integer',
            'note' => 'nullable|string|max:500'
        ]);
        
        $results = [
            'success' => [],
            'failed' => []
        ];
        
        DB::beginTransaction();
        try {
            foreach ($request->items as $itemData) {
                $item = $this->getApprovalItem($itemData['type'], $itemData['id']);
                
                if ($item) {
                    try {
                        $this->processApproval($itemData['type'], $item, 'approved', $request->note);
                        $results['success'][] = $itemData;
                    } catch (\Exception $e) {
                        $results['failed'][] = [
                            'item' => $itemData,
                            'error' => $e->getMessage()
                        ];
                    }
                } else {
                    $results['failed'][] = [
                        'item' => $itemData,
                        'error' => 'Item not found'
                    ];
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => count($results['success']) . ' item berhasil disetujui',
                'results' => $results
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Bulk approval gagal: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Bulk reject multiple items
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.type' => 'required|in:expenses,documents,invoices',
            'items.*.id' => 'required|integer',
            'reason' => 'required|string',
            'note' => 'nullable|string|max:500'
        ]);
        
        $results = [
            'success' => [],
            'failed' => []
        ];
        
        DB::beginTransaction();
        try {
            foreach ($request->items as $itemData) {
                $item = $this->getApprovalItem($itemData['type'], $itemData['id']);
                
                if ($item) {
                    try {
                        $this->processApproval($itemData['type'], $item, 'rejected', $request->note, $request->reason);
                        $results['success'][] = $itemData;
                    } catch (\Exception $e) {
                        $results['failed'][] = [
                            'item' => $itemData,
                            'error' => $e->getMessage()
                        ];
                    }
                } else {
                    $results['failed'][] = [
                        'item' => $itemData,
                        'error' => 'Item not found'
                    ];
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => count($results['success']) . ' item berhasil ditolak',
                'results' => $results
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Bulk rejection gagal: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get all pending approvals by type
     */
    private function getPendingApprovals($type = 'all')
    {
        $approvals = collect();
        
        if ($type === 'all' || $type === 'expenses') {
            $expenses = ProjectExpense::where('status', 'pending')
                ->whereHas('project', function($query) {
                    $query->where('manager_id', auth()->id());
                })
                ->with(['project', 'category'])
                ->get()
                ->map(function($expense) {
                    return [
                        'id' => $expense->id,
                        'type' => 'expenses',
                        'title' => $expense->description ?? 'Expense',
                        'subtitle' => $expense->project->name ?? '-',
                        'amount' => $expense->amount,
                        'date' => $expense->expense_date,
                        'icon' => 'wallet',
                        'color' => 'blue',
                        'url' => route('mobile.approvals.show', ['type' => 'expenses', 'id' => $expense->id])
                    ];
                });
            $approvals = $approvals->merge($expenses);
        }
        
        if ($type === 'all' || $type === 'documents') {
            $documents = Document::where('status', 'review')
                ->where('reviewer_id', auth()->id())
                ->with(['project'])
                ->get()
                ->map(function($doc) {
                    return [
                        'id' => $doc->id,
                        'type' => 'documents',
                        'title' => $doc->title,
                        'subtitle' => $doc->project->name ?? '-',
                        'date' => $doc->created_at,
                        'icon' => 'file-alt',
                        'color' => 'purple',
                        'url' => route('mobile.approvals.show', ['type' => 'documents', 'id' => $doc->id])
                    ];
                });
            $approvals = $approvals->merge($documents);
        }
        
        if ($type === 'all' || $type === 'invoices') {
            $invoices = Invoice::where('status', 'draft')
                ->whereHas('project', function($query) {
                    $query->where('manager_id', auth()->id());
                })
                ->with(['project', 'client'])
                ->get()
                ->map(function($invoice) {
                    return [
                        'id' => $invoice->id,
                        'type' => 'invoices',
                        'title' => 'Invoice #' . $invoice->invoice_number,
                        'subtitle' => $invoice->client->name ?? '-',
                        'amount' => $invoice->total_amount,
                        'date' => $invoice->created_at,
                        'icon' => 'file-invoice',
                        'color' => 'green',
                        'url' => route('mobile.approvals.show', ['type' => 'invoices', 'id' => $invoice->id])
                    ];
                });
            $approvals = $approvals->merge($invoices);
        }
        
        return $approvals->sortBy('date');
    }
    
    /**
     * Get specific approval item by type and id
     */
    private function getApprovalItem($type, $id)
    {
        switch ($type) {
            case 'expenses':
                return ProjectExpense::with(['project', 'category'])->find($id);
            case 'documents':
                return Document::with(['project'])->find($id);
            case 'invoices':
                return Invoice::with(['project', 'client'])->find($id);
            default:
                return null;
        }
    }
    
    /**
     * Process approval/rejection
     */
    private function processApproval($type, $item, $action, $note = null, $reason = null)
    {
        switch ($type) {
            case 'expenses':
                $item->update([
                    'status' => $action,
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'approval_note' => $note,
                    'rejection_reason' => $reason
                ]);
                break;
                
            case 'documents':
                $item->update([
                    'status' => $action,
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                    'review_note' => $note,
                    'rejection_reason' => $reason
                ]);
                break;
                
            case 'invoices':
                $status = $action === 'approved' ? 'sent' : 'rejected';
                $item->update([
                    'status' => $status,
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'approval_note' => $note,
                    'rejection_reason' => $reason
                ]);
                break;
        }
        
        // Send notification to requester
        $this->sendApprovalNotification($item, $action);
    }
    
    /**
     * Count pending approvals by type
     */
    private function countByType($type)
    {
        switch ($type) {
            case 'expenses':
                return ProjectExpense::where('status', 'pending')
                    ->whereHas('project', fn($q) => $q->where('manager_id', auth()->id()))
                    ->count();
            case 'documents':
                return Document::where('status', 'review')
                    ->where('reviewer_id', auth()->id())
                    ->count();
            case 'invoices':
                return Invoice::where('status', 'draft')
                    ->whereHas('project', fn($q) => $q->where('manager_id', auth()->id()))
                    ->count();
            default:
                return 0;
        }
    }
    
    /**
     * Send notification about approval decision
     */
    private function sendApprovalNotification($item, $action)
    {
        // TODO: Implement notification system
        // This would send push notification via service worker
        // notification()->send($item->user, new ApprovalNotification($item, $action));
    }
}
