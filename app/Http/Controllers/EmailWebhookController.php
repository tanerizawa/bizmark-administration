<?php

namespace App\Http\Controllers;

use App\Models\EmailInbox;
use App\Models\EmailAccount;
use App\Models\EmailAssignment;
use App\Notifications\NewEmailReceivedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmailWebhookController extends Controller
{
    /**
     * Receive incoming email from Cloudflare Email Worker
     * 
     * Endpoint: POST /webhook/email/receive
     */
    public function receive(Request $request)
    {
        if (!$this->isAuthorizedWebhookRequest($request)) {
            Log::warning('Rejected email webhook request', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Unauthorized webhook request',
            ], 401);
        }

        try {
            // Log untuk debugging
            Log::info('Email webhook received', [
                'from' => $request->input('from'),
                'to' => $request->input('to'),
                'subject' => $request->input('subject'),
            ]);

            // Validate required fields
            if (!$request->filled('from') || !$request->filled('subject')) {
                Log::error('Invalid email webhook data - missing required fields', [
                    'from' => $request->input('from'),
                    'to' => $request->input('to'),
                    'subject' => $request->input('subject'),
                    'message_id' => $request->input('message_id'),
                ]);
                return response()->json(['error' => 'Missing required fields: from, subject'], 400);
            }

            // Extract from email and name
            $fromRaw = $request->input('from');
            
            // Parse "Name <email@example.com>" format
            if (preg_match('/<(.+?)>/', $fromRaw, $emailMatches)) {
                $fromEmail = strtolower(trim($emailMatches[1]));
                $fromName = trim(preg_replace('/<.+?>/', '', $fromRaw));
            } else {
                $fromEmail = strtolower(trim($fromRaw));
                $fromName = $fromRaw;
            }

            $toEmail = $this->resolveRecipientEmail($request->input('to'));

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

            $messageId = $request->input('message_id') ?? 'webhook-' . Str::uuid()->toString();

            $existing = EmailInbox::query()->where('message_id', $messageId)->first();
            if ($existing) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email already processed',
                    'data' => [
                        'id' => $existing->id,
                        'from' => $existing->from_email,
                        'to' => $existing->to_email,
                        'subject' => $existing->subject,
                        'email_account' => $emailAccount ? $emailAccount->email : null,
                        'assigned_to' => $primaryHandler ? $primaryHandler->email : null,
                        'priority' => $existing->priority,
                        'status' => $existing->status,
                    ],
                ], 200);
            }

            // Create inbox entry with auto-assignment
            $inbox = EmailInbox::create([
                'message_id' => $messageId,
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
                'from' => $request->input('from'),
                'to' => $request->input('to'),
                'subject' => $request->input('subject'),
                'message_id' => $request->input('message_id'),
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
            $replyMessage = $emailAccount->auto_reply_message ?? 'Terima kasih atas pesan Anda. Tim kami akan segera menghubungi Anda.';
            $fromName = $emailAccount->display_name ?? config('mail.from.name');

            Mail::raw($replyMessage, function ($message) use ($emailAccount, $recipientEmail, $fromName, $inbox) {
                $message->to($recipientEmail)
                    ->from($emailAccount->email, $fromName)
                    ->subject('Re: ' . ($inbox->subject ?? 'Pesan Anda'));
            });

            Log::info('Auto-reply sent', [
                'from' => $emailAccount->email,
                'to' => $recipientEmail,
            ]);

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
            $usersToNotify = EmailAssignment::where('email_account_id', $emailAccount->id)
                ->where('is_active', true)
                ->where('notify_on_receive', true)
                ->with('user')
                ->get();

            foreach ($usersToNotify as $assignment) {
                if (!$assignment->user) continue;

                $assignment->user->notify(new NewEmailReceivedNotification($inbox, $emailAccount));

                Log::info('Email notification sent to user', [
                    'user' => $assignment->user->email,
                    'email_account' => $emailAccount->email,
                    'inbox_id' => $inbox->id,
                ]);
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
        if (app()->environment('production')) {
            return response()->json([
                'success' => false,
                'message' => 'Test endpoint is disabled in production.',
            ], 403);
        }

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
        if (app()->environment('production')) {
            return response()->json([
                'success' => false,
                'message' => 'Status endpoint is disabled in production.',
            ], 403);
        }

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

    private function isAuthorizedWebhookRequest(Request $request): bool
    {
        $allowedIps = array_filter(array_map('trim', (array) config('email_webhook.allowed_ips', [])));

        if (!empty($allowedIps) && !in_array($request->ip(), $allowedIps, true)) {
            return false;
        }

        $secret = (string) config('email_webhook.secret', '');
        $requireSignature = (bool) config('email_webhook.require_signature', app()->environment('production'));

        if (!$requireSignature && $secret === '') {
            return true;
        }

        if ($secret === '') {
            // In production, missing secret means webhook should not be trusted.
            return false;
        }

        $signatureHeader = (string) config('email_webhook.signature_header', 'X-Webhook-Signature');
        $providedSignature = (string) $request->header($signatureHeader, '');

        if ($providedSignature === '') {
            return false;
        }

        $payload = (string) $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $providedSignature);
    }

    private function resolveRecipientEmail(?string $toRaw): string
    {
        $toRaw = trim((string) $toRaw);

        if ($toRaw !== '') {
            if (preg_match('/<(.+?)>/', $toRaw, $emailMatches)) {
                return strtolower(trim($emailMatches[1]));
            }

            if (str_contains($toRaw, ',')) {
                $firstRecipient = trim(explode(',', $toRaw)[0]);

                if (preg_match('/<(.+?)>/', $firstRecipient, $emailMatches)) {
                    return strtolower(trim($emailMatches[1]));
                }

                return strtolower($firstRecipient);
            }

            return strtolower($toRaw);
        }

        $defaultRecipient = strtolower(trim((string) config('email_webhook.default_recipient', '')));

        if ($defaultRecipient !== '') {
            Log::warning('Inbound email webhook missing recipient, using configured default recipient', [
                'default_recipient' => $defaultRecipient,
            ]);

            return $defaultRecipient;
        }

        $activeRecipient = EmailAccount::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->value('email');

        if (!empty($activeRecipient)) {
            $activeRecipient = strtolower(trim((string) $activeRecipient));

            Log::warning('Inbound email webhook missing recipient, using first active email account', [
                'default_recipient' => $activeRecipient,
            ]);

            return $activeRecipient;
        }

        Log::warning('Inbound email webhook missing recipient, falling back to hardcoded info address');

        return 'info@bizmark.id';
    }
}
