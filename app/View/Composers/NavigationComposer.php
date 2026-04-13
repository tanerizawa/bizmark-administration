<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use App\Models\Project;
use App\Models\Task;
use App\Models\Document;
use App\Models\Institution;
use App\Models\Client;
use App\Models\PermitType;
use App\Models\PermitTemplate;
use App\Models\PermitApplication;
use App\Models\ApplicationNote;
use App\Models\Payment;
use App\Models\JobApplication;
use App\Models\EmailInbox;
use App\Models\BankReconciliation;

class NavigationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Cache main navigation counts for 5 minutes
        $navCounts = Cache::remember('bizmark_nav_counts', 300, function () {
            return [
                'projects' => Project::count(),
                'tasks' => Task::count(),
                'pending_tasks' => Task::where('status', 'pending')->count(),
                'documents' => Document::count(),
                'institutions' => Institution::count(),
                'clients' => Client::count(),
                'permit_types' => PermitType::count(),
                'permit_templates' => PermitTemplate::count(),
            ];
        });

        // Cache permit notifications for 1 minute (more real-time)
        $permitNotifications = Cache::remember('bizmark_permit_notifications', 60, function () {
            $submitted = PermitApplication::where('status', 'submitted')->count();
            $underReview = PermitApplication::where('status', 'under_review')->count();
            $unreadNotes = ApplicationNote::where('author_type', 'client')
                ->where('is_read', false)->count();
            $pendingPayments = Payment::where('payment_method', 'manual')
                ->where('status', 'processing')->count();

            return [
                'submitted' => $submitted,
                'under_review' => $underReview,
                'unread_notes' => $unreadNotes,
                'pending_payments' => $pendingPayments,
                'total' => $submitted + $underReview + $unreadNotes + $pendingPayments,
            ];
        });

        // Cache other notifications for 1 minute
        $otherNotifications = Cache::remember('bizmark_other_notifications', 60, function () {
            return [
                'pending_job_apps' => JobApplication::where('status', 'pending')->count(),
                'unread_emails' => EmailInbox::where('category', 'inbox')
                    ->where('is_read', false)->count(),
                'pending_reconciliations' => BankReconciliation::where('status', 'pending')->count(),
            ];
        });

        $view->with([
            'navCounts' => $navCounts,
            'permitNotifications' => $permitNotifications,
            'otherNotifications' => $otherNotifications,
        ]);
    }
}
