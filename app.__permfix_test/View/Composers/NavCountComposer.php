<?php

namespace App\View\Composers;

use App\Models\CashAccount;
use App\Models\Document;
use App\Models\Institution;
use App\Models\PermitTemplate;
use App\Models\PermitType;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class NavCountComposer
{
    /**
     * Bind data to the view.
     *
     * @return void
     */
    public function compose(View $view)
    {
        // Cache navigation counts for 5 minutes to reduce DB queries
        $navCounts = Cache::remember('nav_counts', 300, function () {
            return [
                'projects' => Project::count(),
                'tasks' => Task::count(),
                'documents' => Document::count(),
                'institutions' => Institution::count(),
                'cash_accounts' => CashAccount::where('is_active', true)->count(),
                'permit_types' => PermitType::where('is_active', true)->count(),
                'permit_templates' => PermitTemplate::count(),
            ];
        });

        $view->with('navCounts', $navCounts);
    }
}
