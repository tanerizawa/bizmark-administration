<?php

namespace App\Modules\Email\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailAccount;
use App\Models\EmailCampaign;
use App\Models\EmailInbox;
use App\Models\EmailSubscriber;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class EmailManagementController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'inbox');
        $allowedTabs = ['inbox', 'campaigns', 'subscribers', 'templates', 'settings', 'accounts'];
        if (! in_array($activeTab, $allowedTabs, true)) {
            $activeTab = 'inbox';
        }

        // Preload all tab data so switching never needs a refresh
        $inboxData = $this->getInboxData($request, $activeTab);
        $campaignsData = $this->getCampaignsData($request, $activeTab);
        $subscribersData = $this->getSubscribersData($request, $activeTab);
        $templatesData = $this->getTemplatesData($request, $activeTab);
        $settingsData = $this->getSettingsData($request, $activeTab);
        $accountsData = $this->getAccountsData($request, $activeTab);

        // Get notifications/counts after inbox data so selected email reads are reflected.
        $notifications = $this->getNotifications();

        // Get summary statistics
        $totalEmails = EmailInbox::count();
        $unreadEmails = EmailInbox::where('is_read', false)->count();
        $totalCampaigns = EmailCampaign::count();
        $totalSubscribers = EmailSubscriber::where('status', 'active')->count();
        $totalTemplates = EmailTemplate::count();
        $totalAccounts = EmailAccount::where('is_active', true)->count();

        return view('admin.email-management.index', array_merge(
            $inboxData,
            $campaignsData,
            $subscribersData,
            $templatesData,
            $settingsData,
            $accountsData,
            [
                'activeTab' => $activeTab,
                'notifications' => $notifications,
                'totalEmails' => $totalEmails,
                'unreadEmails' => $unreadEmails,
                'totalCampaigns' => $totalCampaigns,
                'totalSubscribers' => $totalSubscribers,
                'totalTemplates' => $totalTemplates,
                'totalAccounts' => $totalAccounts,
            ]
        ));
    }

    private function getNotifications()
    {
        return [
            'inbox' => EmailInbox::where('is_read', false)->count(),
            'campaigns' => EmailCampaign::where('status', 'draft')->count(),
            'subscribers' => EmailSubscriber::where('status', 'pending')->count(),
        ];
    }

    private function getInboxData(Request $request, string $activeTab = 'inbox')
    {
        $query = EmailInbox::with(['emailAccount', 'handler'])->latest('received_at');
        $activeFolder = 'inbox';
        $selectedEmail = null;
        $mailboxCounts = [
            'inbox' => EmailInbox::where('category', 'inbox')->count(),
            'sent' => EmailInbox::where('category', 'sent')->count(),
            'starred' => EmailInbox::where('is_starred', true)->where('category', '!=', 'trash')->count(),
            'trash' => EmailInbox::where('category', 'trash')->count(),
        ];
        $statusCounts = [
            'all' => 0,
            'read' => 0,
            'unread' => 0,
        ];

        if ($activeTab === 'inbox') {
            $activeFolder = $request->input('folder', 'inbox');

            $statusBaseQuery = EmailInbox::query();

            if ($activeFolder === 'starred') {
                $statusBaseQuery->where('is_starred', true)
                    ->where('category', '!=', 'trash');
            } else {
                $statusBaseQuery->where('category', $activeFolder);
            }

            $statusCounts['all'] = (clone $statusBaseQuery)->count();
            $statusCounts['read'] = (clone $statusBaseQuery)->where('is_read', true)->count();
            $statusCounts['unread'] = (clone $statusBaseQuery)->where('is_read', false)->count();

            if ($activeFolder === 'starred') {
                // Starred is a flag, not a physical folder category.
                $query->where('is_starred', true)
                    ->where('category', '!=', 'trash');
            } else {
                $query->where('category', $activeFolder);
            }

            if ($request->filled('is_read')) {
                $query->where('is_read', $request->is_read === '1');
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('subject', 'like', "%{$search}%")
                        ->orWhere('body_text', 'like', "%{$search}%")
                        ->orWhere('from_email', 'like', "%{$search}%");
                });
            }

            if ($request->filled('email')) {
                $selectedEmail = EmailInbox::with(['emailAccount', 'replyTo', 'replies'])
                    ->find($request->integer('email'));

                if ($selectedEmail && ! $selectedEmail->is_read) {
                    $selectedEmail->markAsRead();
                    $selectedEmail->refresh();
                }
            }
        }

        // Dedicated pagination parameter prevents clashes with other tabs
        $emails = $query->paginate(20, ['*'], 'inbox_page')->withQueryString();
        $folders = ['inbox', 'sent', 'starred', 'trash'];

        return compact('emails', 'folders', 'activeFolder', 'selectedEmail', 'mailboxCounts', 'statusCounts');
    }

    private function getCampaignsData(Request $request, string $activeTab = 'campaigns')
    {
        $query = EmailCampaign::with(['template'])->latest();

        if ($activeTab === 'campaigns') {
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%");
                });
            }
        }

        // Dedicated pagination parameter prevents clashes with other tabs
        $campaigns = $query->paginate(20, ['*'], 'campaigns_page')->withQueryString();
        $statuses = ['draft', 'scheduled', 'sending', 'sent', 'cancelled'];

        return compact('campaigns', 'statuses');
    }

    private function getSubscribersData(Request $request, string $activeTab = 'subscribers')
    {
        $query = EmailSubscriber::latest();

        if ($activeTab === 'subscribers') {
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            }
        }

        // Dedicated pagination parameter prevents clashes with other tabs
        $subscribers = $query->paginate(20, ['*'], 'subscribers_page')->withQueryString();
        $statuses = ['active', 'pending', 'unsubscribed', 'bounced'];

        return compact('subscribers', 'statuses');
    }

    private function getTemplatesData(Request $request, string $activeTab = 'templates')
    {
        $query = EmailTemplate::withCount('campaigns')->latest();

        if ($activeTab === 'templates') {
            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%");
                });
            }
        }

        // Dedicated pagination parameter prevents clashes with other tabs
        $templates = $query->paginate(20, ['*'], 'templates_page')->withQueryString();
        $categories = ['newsletter', 'promotional', 'transactional', 'announcement'];

        return compact('templates', 'categories');
    }

    private function getSettingsData(Request $request, string $activeTab = 'settings')
    {
        // Email settings from Laravel config
        $settings = [
            'mail_mailer' => config('mail.default', 'smtp'),
            'mail_host' => config('mail.mailers.smtp.host', ''),
            'mail_port' => config('mail.mailers.smtp.port', 587),
            'mail_username' => config('mail.mailers.smtp.username', ''),
            'mail_password' => '',
            'mail_encryption' => config('mail.mailers.smtp.encryption', 'tls'),
            'mail_from_address' => config('mail.from.address', ''),
            'mail_from_name' => config('mail.from.name', ''),
            'mailgun_domain' => config('services.mailgun.domain', ''),
            'mailgun_secret' => '',
            'mailgun_endpoint' => config('services.mailgun.endpoint', 'api.mailgun.net'),
            'rate_limit' => config('mail.rate_limit', 100),
            'batch_size' => config('mail.batch_size', 50),
            'queue_emails' => config('mail.queue', true),
            'track_opens' => config('mail.tracking.opens', true),
            'track_clicks' => config('mail.tracking.clicks', true),
            'track_unsubscribes' => config('mail.tracking.unsubscribes', true),
            'unsubscribe_url' => config('mail.unsubscribe_url', ''),
            'add_unsubscribe_link' => config('mail.add_unsubscribe_link', true),
        ];

        // Create empty paginator for consistency
        $emptyPaginator = new LengthAwarePaginator([], 0, 20);

        return [
            'settings' => $settings,
            'settingsPaginator' => $emptyPaginator,
        ];
    }

    private function getAccountsData(Request $request, string $activeTab = 'accounts')
    {
        $query = EmailAccount::with(['activeUsers'])->latest();

        if ($activeTab === 'accounts') {
            if ($request->filled('is_active')) {
                $query->where('is_active', $request->is_active);
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('department')) {
                $query->where('department', $request->department);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            }
        }

        // Dedicated pagination parameter prevents clashes with other tabs
        $accounts = $query->paginate(20, ['*'], 'accounts_page')->withQueryString();

        return compact('accounts');
    }
}
