<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ApplicationNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated client.
     */
    public function index(Request $request): View
    {
        $clientId = $request->user('client')->id;

        $notifications = ApplicationNote::whereHas('application', function ($query) use ($clientId) {
                $query->where('client_id', $clientId);
            })
            ->visibleToClient()
            ->byAdmin()
            ->with(['application', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = ApplicationNote::whereHas('application', function ($query) use ($clientId) {
                $query->where('client_id', $clientId);
            })
            ->visibleToClient()
            ->byAdmin()
            ->unread()
            ->count();

        return view('client.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Mark all visible admin notes as read for authenticated client.
     */
    public function markAllRead(Request $request): RedirectResponse
    {
        $clientId = $request->user('client')->id;

        ApplicationNote::whereHas('application', function ($query) use ($clientId) {
                $query->where('client_id', $clientId);
            })
            ->visibleToClient()
            ->byAdmin()
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
