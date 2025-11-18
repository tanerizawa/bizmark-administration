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
        
        $stats = [
            'unread' => auth()->user()->unreadNotifications()->count(),
            'total' => auth()->user()->notifications()->count()
        ];
        
        if ($request->expectsJson()) {
            return response()->json([
                'notifications' => $notifications->items(),
                'hasMore' => $notifications->hasMorePages()
            ]);
        }
        
        return view('mobile.notifications.index', [
            'notifications' => $notifications,
            'currentFilter' => $filter,
            'stats' => $stats
        ]);
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
