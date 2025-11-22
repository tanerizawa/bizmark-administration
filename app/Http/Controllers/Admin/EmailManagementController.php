<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailInbox;
use App\Models\EmailCampaign;
use App\Models\EmailSubscriber;
use App\Models\EmailTemplate;
use App\Models\EmailAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class EmailManagementController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'inbox');
        $allowedTabs = ['inbox', 'campaigns', 'subscribers', 'templates', 'settings', 'accounts'];
        if (!in_array($activeTab, $allowedTabs, true)) {
            $activeTab = 'inbox';
        }
        
        // Get notifications/counts
        $notifications = $this->getNotifications();
        
        // Preload all tab data so switching never needs a refresh
        $inboxData = $this->getInboxData($request);
        $campaignsData = $this->getCampaignsData($request);
        $subscribersData = $this->getSubscribersData($request);
        $templatesData = $this->getTemplatesData($request);
        $settingsData = $this->getSettingsData($request);
        $accountsData = $this->getAccountsData($request);
        
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
    
    private function getInboxData(Request $request)
    {
        $query = EmailInbox::with(['emailAccount', 'handler'])->latest('received_at');
        
        // Apply filters
        if ($request->filled('folder')) {
            $query->where('category', $request->folder);
        }
        
        if ($request->filled('is_read')) {
            $query->where('is_read', $request->is_read);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('body_text', 'like', "%{$search}%")
                  ->orWhere('from_email', 'like', "%{$search}%");
            });
        }
        
        // Dedicated pagination parameter prevents clashes with other tabs
        $emails = $query->paginate(20, ['*'], 'inbox_page')->withQueryString();
        $folders = ['inbox', 'sent', 'starred', 'trash'];
        
        return compact('emails', 'folders');
    }
    
    private function getCampaignsData(Request $request)
    {
        $query = EmailCampaign::with(['template'])->latest();
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }
        
        // Dedicated pagination parameter prevents clashes with other tabs
        $campaigns = $query->paginate(20, ['*'], 'campaigns_page')->withQueryString();
        $statuses = ['draft', 'scheduled', 'sending', 'sent', 'cancelled'];
        
        return compact('campaigns', 'statuses');
    }
    
    private function getSubscribersData(Request $request)
    {
        $query = EmailSubscriber::latest();
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        // Dedicated pagination parameter prevents clashes with other tabs
        $subscribers = $query->paginate(20, ['*'], 'subscribers_page')->withQueryString();
        $statuses = ['active', 'pending', 'unsubscribed', 'bounced'];
        
        return compact('subscribers', 'statuses');
    }
    
    private function getTemplatesData(Request $request)
    {
        $query = EmailTemplate::withCount('campaigns')->latest();
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }
        
        // Dedicated pagination parameter prevents clashes with other tabs
        $templates = $query->paginate(20, ['*'], 'templates_page')->withQueryString();
        $categories = ['transactional', 'marketing', 'notification', 'system'];
        
        return compact('templates', 'categories');
    }
    
    private function getSettingsData(Request $request)
    {
        // Email settings from Laravel config
        $settings = [
            'smtp_host' => config('mail.mailers.smtp.host', ''),
            'smtp_port' => config('mail.mailers.smtp.port', 587),
            'smtp_username' => config('mail.mailers.smtp.username', ''),
            'smtp_password' => '', // Don't expose password
            'smtp_encryption' => config('mail.mailers.smtp.encryption', 'tls'),
            'from_email' => config('mail.from.address', ''),
            'from_name' => config('mail.from.name', ''),
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
    
    private function getAccountsData(Request $request)
    {
        $query = EmailAccount::with(['activeUsers'])->latest();
        
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
            $query->where(function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        // Dedicated pagination parameter prevents clashes with other tabs
        $accounts = $query->paginate(20, ['*'], 'accounts_page')->withQueryString();
        
        return compact('accounts');
    }
}
