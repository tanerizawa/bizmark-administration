<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\AuthorizesRequests;
use App\Models\EmailCampaign;
use App\Models\EmailTemplate;
use App\Models\EmailSubscriber;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmailCampaignController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizePermission('email.manage', 'Anda tidak memiliki akses untuk mengelola campaign email.');
    }

    public function index(Request $request)
    {
        $query = EmailCampaign::with(['creator', 'template']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('subject', 'ilike', '%' . $request->search . '%');
            });
        }

        $campaigns = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total' => EmailCampaign::count(),
            'draft' => EmailCampaign::where('status', 'draft')->count(),
            'scheduled' => EmailCampaign::where('status', 'scheduled')->count(),
            'sent' => EmailCampaign::where('status', 'sent')->count(),
        ];

        return view('admin.email.campaigns.index', compact('campaigns', 'stats'));
    }

    public function create()
    {
        $templates = EmailTemplate::where('is_active', true)->get();
        $activeSubscribers = EmailSubscriber::where('status', 'active')->count();
        return view('admin.email.campaigns.create', compact('templates', 'activeSubscribers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'template_id' => 'nullable|exists:email_templates,id',
            'content' => 'required|string',
            'recipient_type' => 'required|in:all,active,tags',
            'recipient_tags' => 'nullable|string',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        // Convert comma-separated tags to array
        if (!empty($validated['recipient_tags'])) {
            $validated['recipient_tags'] = array_map('trim', explode(',', $validated['recipient_tags']));
        }

        $validated['created_by'] = Auth::id();
        
        // Determine status based on action and schedule
        if ($request->input('action') === 'send' && empty($validated['scheduled_at'])) {
            // Will be sent immediately via processSend
            $validated['status'] = 'draft'; // Create as draft first
        } elseif (!empty($validated['scheduled_at'])) {
            $validated['status'] = 'scheduled';
        } else {
            $validated['status'] = 'draft';
        }
        
        $recipientsQuery = $this->getRecipientsQuery($validated['recipient_type'], $validated['recipient_tags'] ?? null);
        $validated['total_recipients'] = $recipientsQuery->count();

        $campaign = EmailCampaign::create($validated);

        // If action is send and no schedule, redirect to send confirmation
        if ($request->input('action') === 'send' && empty($validated['scheduled_at'])) {
            return redirect()->route('admin.campaigns.send', $campaign->id);
        }

        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Campaign berhasil dibuat.');
    }

    public function show($id)
    {
        $campaign = EmailCampaign::with(['creator', 'template', 'emailLogs'])->findOrFail($id);

        return view('admin.email.campaigns.show', compact('campaign'));
    }

    public function edit($id)
    {
        $campaign = EmailCampaign::findOrFail($id);
        
        if (in_array($campaign->status, ['sending', 'sent'])) {
            return redirect()->route('admin.campaigns.show', $campaign)
                ->with('error', 'Campaign yang sudah terkirim tidak bisa diedit.');
        }

        $templates = EmailTemplate::where('is_active', true)->get();
        return view('admin.email.campaigns.edit', compact('campaign', 'templates'));
    }

    public function update(Request $request, $id)
    {
        $campaign = EmailCampaign::findOrFail($id);
        
        if (in_array($campaign->status, ['sending', 'sent'])) {
            return redirect()->route('admin.campaigns.show', $campaign)
                ->with('error', 'Campaign yang sudah terkirim tidak bisa diedit.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'template_id' => 'nullable|exists:email_templates,id',
            'content' => 'required|string',
            'recipient_type' => 'required|in:all,active,tags',
            'recipient_tags' => 'nullable|string',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        // Convert comma-separated tags to array
        if (!empty($validated['recipient_tags'])) {
            $validated['recipient_tags'] = array_map('trim', explode(',', $validated['recipient_tags']));
        }

        // Update status if scheduled_at is set
        if (!empty($validated['scheduled_at'])) {
            $validated['status'] = 'scheduled';
        }

        $recipientsQuery = $this->getRecipientsQuery($validated['recipient_type'], $validated['recipient_tags'] ?? null);
        $validated['total_recipients'] = $recipientsQuery->count();

        $campaign->update($validated);

        return redirect()->route('admin.campaigns.show', $campaign->id)
            ->with('success', 'Campaign berhasil diupdate.');
    }

    public function destroy($id)
    {
        $campaign = EmailCampaign::findOrFail($id);
        
        if ($campaign->status === 'sending') {
            return redirect()->back()
                ->with('error', 'Campaign yang sedang dikirim tidak bisa dihapus.');
        }

        $campaign->delete();

        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Campaign berhasil dihapus.');
    }

    public function send($id)
    {
        $campaign = EmailCampaign::findOrFail($id);
        
        if ($campaign->status === 'sent') {
            return redirect()->route('admin.campaigns.show', $campaign)
                ->with('error', 'Campaign ini sudah terkirim.');
        }

        $recipients = $this->getRecipientsQuery(
            $campaign->recipient_type,
            $campaign->recipient_tags
        )->get();

        return view('admin.email.campaigns.send', compact('campaign', 'recipients'));
    }

    public function processSend($id)
    {
        $campaign = EmailCampaign::findOrFail($id);
        
        if ($campaign->status === 'sent') {
            return redirect()->route('admin.campaigns.show', $campaign)
                ->with('error', 'Campaign ini sudah terkirim.');
        }

        $recipients = $this->getRecipientsQuery(
            $campaign->recipient_type,
            $campaign->recipient_tags
        )->get();

        $campaign->markAsSending();

        foreach ($recipients as $subscriber) {
            $trackingId = \Illuminate\Support\Str::random(32);
            
            $emailLog = EmailLog::create([
                'campaign_id' => $campaign->id,
                'subscriber_id' => $subscriber->id,
                'recipient_email' => $subscriber->email,
                'subject' => $campaign->subject,
                'tracking_id' => $trackingId,
                'status' => 'sent',
            ]);

            try {
                $content = str_replace(
                    ['{{name}}', '{{email}}'],
                    [$subscriber->name ?? 'Subscriber', $subscriber->email],
                    $campaign->content
                );

                Mail::html($content, function($message) use ($campaign, $subscriber) {
                    $message->to($subscriber->email)
                        ->subject($campaign->subject)
                        ->from(config('mail.from.address'), config('mail.from.name'));
                });

                $campaign->incrementSentCount();
            } catch (\Exception $e) {
                $emailLog->markAsBounced($e->getMessage());
            }
        }

        $campaign->markAsSent();

        return redirect()->route('admin.campaigns.show', $campaign)
            ->with('success', 'Campaign berhasil dikirim!');
    }

    public function cancel($id)
    {
        $campaign = EmailCampaign::findOrFail($id);
        
        if ($campaign->status !== 'scheduled') {
            return redirect()->route('admin.campaigns.show', $campaign)
                ->with('error', 'Hanya campaign yang dijadwalkan yang bisa dibatalkan.');
        }

        $campaign->update([
            'status' => 'cancelled',
            'scheduled_at' => null,
        ]);

        return redirect()->route('admin.campaigns.show', $campaign)
            ->with('success', 'Campaign berhasil dibatalkan.');
    }

    private function getRecipientsQuery($recipientType, $tags = null)
    {
        $query = EmailSubscriber::query();

        switch ($recipientType) {
            case 'active':
                $query->where('status', 'active');
                break;
            case 'tags':
                if ($tags && is_array($tags)) {
                    foreach ($tags as $tag) {
                        $query->whereJsonContains('tags', $tag);
                    }
                }
                break;
            case 'all':
            default:
                $query->where('status', '!=', 'unsubscribed');
                break;
        }

        return $query;
    }
}
