<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MobileLayoutComposer
{
    /**
     * Bind data to mobile layout view.
     * Cached for 60 seconds to reduce DB queries
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (!Auth::check()) {
            $view->with([
                'myTasksCount' => 0,
                'unreadNotifCount' => 0,
            ]);
            return;
        }

        $userId = Auth::id();
        $cacheKey = "mobile_layout_counts_{$userId}";
        
        // Cache counts for 60 seconds
        $counts = Cache::remember($cacheKey, 60, function() use ($userId) {
            return [
                'myTasksCount' => \App\Models\Task::where('assigned_user_id', $userId)
                    ->where('status', '!=', 'done')
                    ->count(),
                'unreadNotifCount' => Auth::user()->unreadNotifications()->count(),
            ];
        });

        $view->with($counts);
    }
}
