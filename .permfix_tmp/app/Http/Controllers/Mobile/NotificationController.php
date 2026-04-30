<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Notifications list
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, unread, read
        
        $query = auth()->user()->notifications();
        
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }
        
        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Transform notifications for mobile display
        $transformedNotifications = $notifications->map(function($notif) {
            return $this->transformNotification($notif);
        });
        
        // Get stats by type
        $allNotifications = auth()->user()->notifications;
        $stats = [
            'all' => auth()->user()->unreadNotifications()->count(),
            'tasks' => $allNotifications->where('type', 'like', '%Task%')->whereNull('read_at')->count(),
            'approvals' => $allNotifications->where('type', 'like', '%Approval%')->whereNull('read_at')->count(),
            'cash' => $allNotifications->where('type', 'like', '%Payment%')->whereNull('read_at')->count(),
        ];
        
        if ($request->expectsJson()) {
            return response()->json([
                'notifications' => $transformedNotifications,
                'has_more' => $notifications->hasMorePages(),
                'stats' => $stats
            ]);
        }
        
        return view('mobile.notifications.index', [
            'notifications' => $transformedNotifications,
            'currentFilter' => $filter,
            'stats' => $stats
        ]);
    }
    
    /**
     * Transform notification for mobile display
     */
    private function transformNotification($notif)
    {
        $data = $notif->data;
        
        // Determine notification type
        $type = 'other';
        if (str_contains($notif->type, 'Task')) {
            $type = 'task';
        } elseif (str_contains($notif->type, 'Approval') || str_contains($notif->type, 'Expense')) {
            $type = 'approval';
        } elseif (str_contains($notif->type, 'Payment') || str_contains($notif->type, 'Invoice')) {
            $type = 'cash';
        } elseif (str_contains($notif->type, 'Document')) {
            $type = 'document';
        }
        
        // Build actions based on notification type
        $actions = [];
        if ($type === 'task' && isset($data['task_id'])) {
            $actions[] = [
                'label' => 'Lihat Task',
                'url' => mobile_route('tasks.show', ['task' => $data['task_id']]),
                'primary' => true
            ];
        } elseif ($type === 'approval' && isset($data['expense_id'])) {
            $actions[] = [
                'label' => 'Approve',
                'url' => mobile_route('approvals.show', ['type' => 'expense', 'id' => $data['expense_id']]),
                'primary' => true
            ];
        } elseif ($type === 'cash' && isset($data['payment_id'])) {
            $actions[] = [
                'label' => 'Lihat Detail',
                'url' => mobile_route('financial.index'),
                'primary' => false
            ];
        }
        
        return [
            'id' => $notif->id,
            'type' => $type,
            'title' => $data['title'] ?? 'Notifikasi',
            'message' => $data['message'] ?? $data['body'] ?? '',
            'project_name' => $data['project_name'] ?? null,
            'read_at' => $notif->read_at,
            'time_ago' => $notif->created_at->diffForHumans(),
            'url' => $data['url'] ?? null,
            'actions' => $actions
        ];
    }
    
    /**
     * Mark notification as read
     */
    public function markRead(Request $request, $notificationId)
    {
        $notification = auth()->user()->notifications()->find($notificationId);
        
        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }
        
        $notification->markAsRead();
        
        return response()->json([
            'success' => true,
            'notification' => $notification
        ]);
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllRead(Request $request)
    {
        $count = auth()->user()->unreadNotifications()->count();
        
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        
        return response()->json([
            'success' => true,
            'count' => $count,
            'message' => "{$count} notifications marked as read"
        ]);
    }
    
    /**
     * Get unread count (for badge)
     */
    public function unreadCount(Request $request)
    {
        $count = auth()->user()->unreadNotifications()->count();
        
        return response()->json([
            'count' => $count
        ]);
    }
}
