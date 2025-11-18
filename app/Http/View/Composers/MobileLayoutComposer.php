<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class MobileLayoutComposer
{
    /**
     * Bind data to mobile layout view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (!Auth::check()) {
            return;
        }

        // Get my tasks count (assigned to current user, not completed)
        $myTasksCount = \App\Models\Task::where('assigned_user_id', Auth::id())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();

        // Get unread notifications count
        $unreadNotifCount = Auth::user()->unreadNotifications()->count();

        $view->with([
            'myTasksCount' => $myTasksCount,
            'unreadNotifCount' => $unreadNotifCount,
        ]);
    }
}
