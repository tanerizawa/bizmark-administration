<?php

namespace App\Modules\Email\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailAccount;
use App\Models\EmailAssignment;
use App\Models\EmailInbox;
use App\Services\OpenRouterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailInboxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return redirect()->route('admin.email-management.index', $this->buildInboxHubQueryFromRequest($request));
    }

    public function show(Request $request, $id)
    {
        $email = EmailInbox::findOrFail($id);

        return redirect()->route('admin.email-management.index', $this->buildInboxHubQueryFromRequest($request, [
            'folder' => $request->input('folder', $request->input('category', $email->category ?: 'inbox')),
            'email' => $email->id,
        ]));
    }

    public function compose()
    {
        $fromAccounts = $this->getSendableAccounts(auth()->user());

        return view('admin.email.inbox.compose', compact('fromAccounts'));
    }

    public function generate(Request $request, OpenRouterService $openRouter)
    {
        $validated = $request->validate([
            'to_email' => 'nullable|email',
            'subject' => 'nullable|string|max:255',
            'body_html' => 'nullable|string',
        ]);

        $to = $validated['to_email'] ?? 'Pelanggan';
        $subjectHint = $validated['subject'] ?? '';
        $bodyHint = $validated['body_html'] ?? '';

        $system = 'Anda adalah penulis email profesional Bahasa Indonesia. Perbaiki subjek dan buat body email HTML profesional berdasarkan input. Kembalikan JSON: {"email_subject": "...", "email_html": "..." }.';
        $user = "To: {$to}\nSubjectHint: {$subjectHint}\nBodyHint: {$bodyHint}\n\nBerikan subjek yang singkat dan email HTML (paragraphs, bold, list jika perlu).";

        $aiResponse = $openRouter->chat([
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $user],
        ], ['model' => config('services.openrouter.free_primary_model')]);

        if (! ($aiResponse['success'] ?? false)) {
            return response()->json(['success' => false, 'error' => $aiResponse['error'] ?? 'AI error'], 503);
        }

        $content = trim((string) ($aiResponse['content'] ?? ''));
        $content = preg_replace('/^```json\s*/i', '', $content);
        $content = preg_replace('/```$/', '', $content);
        $decoded = json_decode($content, true);
        if (! is_array($decoded)) {
            if (preg_match('/\{[\s\S]*\}/', $content, $m)) {
                $decoded = json_decode($m[0], true);
            }
        }

        if (! is_array($decoded)) {
            return response()->json(['success' => false, 'error' => 'Invalid AI response'], 422);
        }

        return response()->json(['success' => true, 'data' => [
            'email_subject' => trim((string) ($decoded['email_subject'] ?? ($decoded['subject'] ?? ''))),
            'email_html' => trim((string) ($decoded['email_html'] ?? ($decoded['email_html_body'] ?? ($decoded['body_html'] ?? '')))),
        ]]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'to_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'from_account_id' => 'nullable|exists:email_accounts,id',
            'attachments.*' => 'file|max:10240',
        ]);

        $selectedAccount = $this->resolveSendAccount($request->user(), $validated['from_account_id'] ?? null);

        if (($validated['from_account_id'] ?? null) && ! $selectedAccount) {
            return redirect()->back()
                ->with('error', 'Akun pengirim tidak valid atau tidak punya izin kirim.')
                ->withInput();
        }

        $fromEmail = $selectedAccount?->email ?? config('mail.from.address');
        $fromName = $selectedAccount?->name ?? config('mail.from.name');

        try {
            $messageId = 'sent-'.\Illuminate\Support\Str::random(20);
            $attachmentsMeta = [];

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if (! $file || ! $file->isValid()) {
                        continue;
                    }
                    $stored = $file->storeAs("email-inbox/{$messageId}/attachments", $file->getClientOriginalName(), 'public');
                    $attachmentsMeta[] = [
                        'path' => $stored,
                        'name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime' => $file->getClientMimeType(),
                    ];
                }
            }

            Mail::html($validated['body_html'], function ($message) use ($validated, $fromEmail, $fromName, $attachmentsMeta) {
                $message->to($validated['to_email'])
                    ->subject($validated['subject'])
                    ->from($fromEmail, $fromName);

                foreach ($attachmentsMeta as $att) {
                    try {
                        $message->attachFromStorageDisk('public', $att['path'], $att['name'], ['mime' => $att['mime']]);
                    } catch (\Exception $_) {
                        // best-effort attach; continue
                    }
                }
            });

            EmailInbox::create([
                'message_id' => $messageId,
                'from_email' => $fromEmail,
                'from_name' => $fromName,
                'to_email' => $validated['to_email'],
                'subject' => $validated['subject'],
                'body_html' => $validated['body_html'],
                'category' => 'sent',
                'is_read' => true,
                'attachments' => $attachmentsMeta,
                'email_account_id' => $selectedAccount?->id,
                'department' => $selectedAccount?->department,
                'received_at' => now(),
            ]);

            if ($selectedAccount) {
                $selectedAccount->incrementSent();
            }

            return $this->redirectToInboxHub(['folder' => 'sent'])
                ->with('success', 'Email berhasil dikirim!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengirim email: '.$e->getMessage())
                ->withInput();
        }
    }

    public function reply($id)
    {
        $email = EmailInbox::findOrFail($id);
        $fromAccounts = $this->getSendableAccounts(auth()->user());

        return view('admin.email.inbox.reply', compact('email', 'fromAccounts'));
    }

    public function sendReply(Request $request, $id)
    {
        $originalEmail = EmailInbox::findOrFail($id);

        $validated = $request->validate([
            'body_html' => 'required|string',
            'from_account_id' => 'nullable|exists:email_accounts,id',
        ]);

        $defaultAccountId = $originalEmail->email_account_id;
        $requestedAccountId = $validated['from_account_id'] ?? $defaultAccountId;
        $selectedAccount = $this->resolveSendAccount($request->user(), $requestedAccountId);

        if ($requestedAccountId && ! $selectedAccount) {
            return redirect()->back()
                ->with('error', 'Akun pengirim balasan tidak valid atau tidak punya izin kirim.')
                ->withInput();
        }

        $fromEmail = $selectedAccount?->email ?? config('mail.from.address');
        $fromName = $selectedAccount?->name ?? config('mail.from.name');

        try {
            $subject = 'Re: '.$originalEmail->subject;

            Mail::html($validated['body_html'], function ($message) use ($originalEmail, $subject, $fromEmail, $fromName) {
                $message->to($originalEmail->from_email)
                    ->subject($subject)
                    ->from($fromEmail, $fromName);
            });

            EmailInbox::create([
                'message_id' => 'reply-'.\Illuminate\Support\Str::random(20),
                'from_email' => $fromEmail,
                'from_name' => $fromName,
                'to_email' => $originalEmail->from_email,
                'subject' => $subject,
                'body_html' => $validated['body_html'],
                'category' => 'sent',
                'is_read' => true,
                'replied_to' => $originalEmail->id,
                'email_account_id' => $selectedAccount?->id,
                'department' => $selectedAccount?->department,
                'received_at' => now(),
            ]);

            if ($selectedAccount) {
                $selectedAccount->incrementSent();
            }

            return $this->redirectToInboxHub([
                'folder' => $originalEmail->category ?: 'inbox',
                'email' => $originalEmail->id,
            ])
                ->with('success', 'Balasan berhasil dikirim!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengirim balasan: '.$e->getMessage())
                ->withInput();
        }
    }

    public function markAsRead($id)
    {
        $email = EmailInbox::findOrFail($id);
        $email->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAsUnread($id)
    {
        $email = EmailInbox::findOrFail($id);
        $email->markAsUnread();

        return response()->json(['success' => true]);
    }

    public function toggleStar($id)
    {
        $email = EmailInbox::findOrFail($id);
        $email->toggleStar();

        return response()->json(['success' => true, 'starred' => $email->is_starred]);
    }

    public function moveToTrash($id)
    {
        $email = EmailInbox::findOrFail($id);
        $previousCategory = $email->category;
        $email->moveToTrash();

        // Redirect back to the previous category view
        return $this->redirectToInboxHub(['folder' => $previousCategory])
            ->with('success', 'Email dipindahkan ke trash.');
    }

    public function delete($id)
    {
        $email = EmailInbox::findOrFail($id);

        // If email is in trash, delete permanently
        if ($email->category === 'trash') {
            $email->delete();

            return $this->redirectToInboxHub(['folder' => 'trash'])
                ->with('success', 'Email berhasil dihapus permanen.');
        }

        // If email is not in trash, move to trash first
        $previousCategory = $email->category;
        $email->moveToTrash();

        return $this->redirectToInboxHub(['folder' => $previousCategory])
            ->with('success', 'Email dipindahkan ke trash. Untuk menghapus permanen, buka folder Trash.');
    }

    public function emptyTrash()
    {
        $count = EmailInbox::where('category', 'trash')->count();
        EmailInbox::where('category', 'trash')->delete();

        return $this->redirectToInboxHub(['folder' => 'trash'])
            ->with('success', "{$count} email berhasil dihapus permanen dari trash.");
    }

    public function batchDelete(Request $request)
    {
        $request->validate([
            'email_ids' => 'required|array|min:1',
            'email_ids.*' => 'exists:email_inbox,id',
        ]);

        $emails = EmailInbox::whereIn('id', $request->email_ids)->get();
        $movedToTrash = 0;
        $deletedPermanently = 0;

        foreach ($emails as $email) {
            if ($email->category === 'trash') {
                $email->delete();
                $deletedPermanently++;
            } else {
                $email->moveToTrash();
                $movedToTrash++;
            }
        }

        $message = [];
        if ($movedToTrash > 0) {
            $message[] = "{$movedToTrash} email dipindahkan ke trash";
        }
        if ($deletedPermanently > 0) {
            $message[] = "{$deletedPermanently} email dihapus permanen";
        }

        return redirect()->back()->with('success', implode(' dan ', $message).'.');
    }

    private function getSendableAccounts($user)
    {
        $query = EmailAccount::query()
            ->where('is_active', true)
            ->orderBy('email');

        if (! $user || ! $user->hasRole('admin')) {
            $query->whereHas('assignments', function ($assignmentQuery) use ($user) {
                $assignmentQuery->where('user_id', $user?->id)
                    ->where('is_active', true)
                    ->where('can_send', true);
            });
        }

        return $query->get();
    }

    private function resolveSendAccount($user, $accountId): ?EmailAccount
    {
        if (! $accountId) {
            return null;
        }

        $account = EmailAccount::where('id', $accountId)
            ->where('is_active', true)
            ->first();

        if (! $account || ! $user) {
            return null;
        }

        if ($user->hasRole('admin')) {
            return $account;
        }

        $canSend = EmailAssignment::where('email_account_id', $account->id)
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->where('can_send', true)
            ->exists();

        return $canSend ? $account : null;
    }

    private function redirectToInboxHub(array $overrides = []): RedirectResponse
    {
        return redirect()->route('admin.email-management.index', $this->buildInboxHubQueryFromRequest(request(), $overrides));
    }

    private function buildInboxHubQueryFromRequest(Request $request, array $overrides = []): array
    {
        $previousQuery = [];
        $previousUrl = $request->headers->get('referer') ?: url()->previous();

        if (is_string($previousUrl) && str_contains($previousUrl, '/admin/email-management')) {
            parse_str((string) parse_url($previousUrl, PHP_URL_QUERY), $previousQuery);
        }

        $folder = $request->input('folder', $request->input('category', $previousQuery['folder'] ?? 'inbox'));

        if ($request->boolean('is_starred')) {
            $folder = 'starred';
        }

        $query = [
            'tab' => 'inbox',
            'folder' => $folder,
        ];

        foreach (['search', 'is_read', 'to_email', 'email', 'inbox_page'] as $key) {
            if ($request->filled($key)) {
                $query[$key] = $request->input($key);
            } elseif (filled($previousQuery[$key] ?? null)) {
                $query[$key] = $previousQuery[$key];
            }
        }

        if ($request->filled('redirect_folder')) {
            $query['folder'] = $request->input('redirect_folder');
        }

        $query = array_merge($query, $overrides);

        return array_filter($query, static fn ($value) => ! ($value === null || $value === ''));
    }
}
