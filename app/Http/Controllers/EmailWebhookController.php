<?php

namespace App\Http\Controllers;

use App\Models\EmailInbox;
use App\Models\EmailAccount;
use App\Models\EmailAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailWebhookController extends Controller
{
    /**
     * Receive incoming email from Cloudflare Email Worker
     * 
     * Endpoint: POST /webhook/email/receive
     */
    public function receive(Request $request)
    {
        try {
            // Log untuk debugging
            Log::info('Email webhook received', [
                'from' => $request->input('from'),
                'to' => $request->input('to'),
                'subject' => $request->input('subject'),
            ]);

            // Validate required fields
            if (!$request->filled('from') || !$request->filled('subject')) {
                Log::error('Invalid email webhook data - missing required fields', $request->all());
                return response()->json(['error' => 'Missing required fields: from, subject'], 400);
            }

            // Extract from email and name
            $fromRaw = $request->input('from');
            
            // Parse "Name <email@example.com>" format
            if (preg_match('/<(.+?)>/', $fromRaw, $emailMatches)) {
                $fromEmail = $emailMatches[1];
                $fromName = trim(preg_replace('/<.+?>/', '', $fromRaw));
            } else {
                $fromEmail = $fromRaw;
                $fromName = $fromRaw;
            }

            $toEmail = $request->input('to') ?? 'cs@bizmark.id';

            // AUTO-FIND EMAIL ACCOUNT
            $emailAccount = EmailAccount::where('email', $toEmail)
                ->where('is_active', true)
                ->first();

            $emailAccountId = null;
            $department = null;
            $primaryHandler = null;

            if ($emailAccount) {
                $emailAccountId = $emailAccount->id;
                $department = $emailAccount->department;
                
                // Get primary handler for auto-assignment
                $primaryHandler = $emailAccount->getPrimaryHandler();
                
                Log::info('Email account found', [
                    'account_id' => $emailAccountId,
                    'department' => $department,
                    'primary_handler' => $primaryHandler ? $primaryHandler->email : 'none'
                ]);
            } else {
                Log::warning('No email account found for recipient', ['to' => $toEmail]);
            }

            // Get email body
            $bodyHtml = $request->input('html');
            $bodyText = $request->input('text');
            
            // If no text body, extract from HTML
            if (!$bodyText && $bodyHtml) {
                $bodyText = strip_tags($bodyHtml);
            }

            // Create inbox entry with auto-assignment
            $inbox = EmailInbox::create([
                'message_id' => $request->input('message_id') ?? 'webhook-' . uniqid(),
                'from_email' => $fromEmail,
                'from_name' => $fromName,
                'to_email' => $toEmail,
                'subject' => $request->input('subject'),
                'body_html' => $bodyHtml,
                'body_text' => $bodyText,
                'attachments' => $request->input('attachments', []),
                'category' => 'inbox',
                'is_read' => false,
                'is_starred' => false,
                'received_at' => $request->input('date') 
                    ? \Carbon\Carbon::parse($request->input('date'))
                    : now(),
                
                // Multi-user fields
                'email_account_id' => $emailAccountId,
                'department' => $department,
                'priority' => $this->detectPriority($request->input('subject')),
                'status' => 'new',
                'handled_by' => $primaryHandler ? $primaryHandler->id : null,
            ]);

            // Update email account statistics
            if ($emailAccount) {
                $emailAccount->incrementReceived();
            }

            // Send auto-reply if enabled
            if ($emailAccount && $emailAccount->shouldAutoReply()) {
                $this->sendAutoReply($emailAccount, $fromEmail, $inbox);
            }

            // TODO: Send notification to assigned users
            if ($emailAccount) {
                $this->notifyAssignedUsers($emailAccount, $inbox);
            }

            Log::info('Email saved to inbox successfully', [
                'id' => $inbox->id,
                'from' => $fromEmail,
                'subject' => $inbox->subject,
                'account_id' => $emailAccountId,
                'assigned_to' => $primaryHandler ? $primaryHandler->email : 'unassigned'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email received and saved to inbox',
                'data' => [
                    'id' => $inbox->id,
                    'from' => $fromEmail,
                    'to' => $toEmail,
                    'subject' => $inbox->subject,
                    'email_account' => $emailAccount ? $emailAccount->email : null,
                    'assigned_to' => $primaryHandler ? $primaryHandler->email : null,
                    'priority' => $inbox->priority,
                    'status' => $inbox->status,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Email webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => config('app.debug') ? $e->getMessage() : 'Failed to process email'
            ], 500);
        }
    }

    /**
     * Detect email priority from subject
     */
    protected function detectPriority($subject)
    {
        $subject = strtolower($subject);
        
        $urgentKeywords = ['urgent', 'emergency', 'critical', 'asap', 'immediate'];
        $highKeywords = ['important', 'priority', 'high'];
        
        foreach ($urgentKeywords as $keyword) {
            if (str_contains($subject, $keyword)) {
                return 'urgent';
            }
        }
        
        foreach ($highKeywords as $keyword) {
            if (str_contains($subject, $keyword)) {
                return 'high';
            }
        }
        
        return 'normal';
    }

    /**
     * Send auto-reply email
     */
    protected function sendAutoReply($emailAccount, $recipientEmail, $inbox)
    {
        try {
            // TODO: Implement actual email sending via Brevo
            Log::info('Auto-reply would be sent', [
                'from' => $emailAccount->email,
                'to' => $recipientEmail,
                'message' => $emailAccount->auto_reply_message
            ]);

            // Mark that auto-reply was sent
            $inbox->update(['tags' => ['auto_reply_sent']]);

        } catch (\Exception $e) {
            Log::error('Auto-reply failed', [
                'error' => $e->getMessage(),
                'email_account' => $emailAccount->email
            ]);
        }
    }

    /**
     * Notify assigned users about new email
     */
    protected function notifyAssignedUsers($emailAccount, $inbox)
    {
        try {
            // Get users who should be notified
            $usersToNotify = EmailAssignment::where('email_account_id', $emailAccount->id)
                ->where('is_active', true)
                ->where('notify_on_receive', true)
                ->with('user')
                ->get();

            foreach ($usersToNotify as $assignment) {
                Log::info('Notification would be sent to user', [
                    'user' => $assignment->user->email,
                    'email_account' => $emailAccount->email,
                    'inbox_id' => $inbox->id
                ]);

                // TODO: Implement actual notification
                // Notification::send($assignment->user, new NewEmailReceived($inbox, $emailAccount));
            }

        } catch (\Exception $e) {
            Log::error('Notification failed', [
                'error' => $e->getMessage(),
                'email_account' => $emailAccount->email
            ]);
        }
    }

    /**
     * Test endpoint with dummy data
     */
    public function test(Request $request)
    {
        $dummyData = [
            'from' => 'Test User <test@example.com>',
            'to' => 'cs@bizmark.id',
            'subject' => 'Test Email via Webhook',
            'text' => 'This is a test email sent via webhook endpoint.',
            'html' => '<p>This is a <strong>test email</strong> sent via webhook endpoint.</p>',
            'message_id' => 'test-' . uniqid(),
            'date' => now()->toIso8601String(),
        ];

        // Merge with any provided data
        $data = array_merge($dummyData, $request->all());

        // Create a new request with the test data
        $testRequest = Request::create(
            '/webhook/email/receive',
            'POST',
            $data
        );

        // Call the receive method
        return $this->receive($testRequest);
    }

    /**
     * Check webhook status
     */
    public function status()
    {
        $stats = [
            'webhook_active' => true,
            'total_emails' => EmailInbox::count(),
            'today_emails' => EmailInbox::whereDate('received_at', today())->count(),
            'unread_emails' => EmailInbox::where('is_read', false)->count(),
            'email_accounts' => EmailAccount::where('is_active', true)->count(),
            'last_email' => EmailInbox::latest('received_at')->first()?->received_at,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Email webhook is active and working',
            'data' => $stats
        ]);
    }
}
