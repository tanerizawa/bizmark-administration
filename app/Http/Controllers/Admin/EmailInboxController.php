<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailInbox;
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
        $query = EmailInbox::query();

        $category = $request->get('category', 'inbox');
        $query->where('category', $category);

        if ($request->filled('is_read')) {
            $query->where('is_read', $request->is_read);
        }

        if ($request->filled('is_starred')) {
            $query->where('is_starred', true);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('subject', 'ilike', '%' . $request->search . '%')
                  ->orWhere('from_email', 'ilike', '%' . $request->search . '%')
                  ->orWhere('body_text', 'ilike', '%' . $request->search . '%');
            });
        }

        if ($request->filled('to_email')) {
            $query->where('to_email', $request->to_email);
        }

        $emails = $query->with('emailAccount')->orderBy('received_at', 'desc')->paginate(25);

        $stats = [
            'total' => EmailInbox::whereNotIn('category', ['trash', 'spam'])->count(),
            'inbox' => EmailInbox::where('category', 'inbox')->count(),
            'sent' => EmailInbox::where('category', 'sent')->count(),
            'unread' => EmailInbox::where('category', 'inbox')->where('is_read', false)->count(),
            'starred' => EmailInbox::where('is_starred', true)->whereNotIn('category', ['trash', 'spam'])->count(),
            'trash' => EmailInbox::where('category', 'trash')->count(),
            'spam' => EmailInbox::where('category', 'spam')->count(),
        ];

        return view('admin.email.inbox.index', compact('emails', 'stats', 'category'));
    }

    public function show($id)
    {
        $email = EmailInbox::with(['replyTo', 'replies'])->findOrFail($id);
        
        if (!$email->is_read) {
            $email->markAsRead();
        }

        return view('admin.email.inbox.show', compact('email'));
    }

    public function compose()
    {
        return view('admin.email.inbox.compose');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'to_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
        ]);

        try {
            Mail::html($validated['body_html'], function($message) use ($validated) {
                $message->to($validated['to_email'])
                    ->subject($validated['subject'])
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            EmailInbox::create([
                'message_id' => 'sent-' . \Illuminate\Support\Str::random(20),
                'from_email' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
                'to_email' => $validated['to_email'],
                'subject' => $validated['subject'],
                'body_html' => $validated['body_html'],
                'category' => 'sent',
                'is_read' => true,
                'received_at' => now(),
            ]);

            return redirect()->route('admin.inbox.index', ['category' => 'sent'])
                ->with('success', 'Email berhasil dikirim!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengirim email: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function reply($id)
    {
        $email = EmailInbox::findOrFail($id);
        return view('admin.email.inbox.reply', compact('email'));
    }

    public function sendReply(Request $request, $id)
    {
        $originalEmail = EmailInbox::findOrFail($id);
        
        $validated = $request->validate([
            'body_html' => 'required|string',
        ]);

        try {
            $subject = 'Re: ' . $originalEmail->subject;
            
            Mail::html($validated['body_html'], function($message) use ($originalEmail, $subject) {
                $message->to($originalEmail->from_email)
                    ->subject($subject)
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            EmailInbox::create([
                'message_id' => 'reply-' . \Illuminate\Support\Str::random(20),
                'from_email' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
                'to_email' => $originalEmail->from_email,
                'subject' => $subject,
                'body_html' => $validated['body_html'],
                'category' => 'sent',
                'is_read' => true,
                'replied_to' => $originalEmail->id,
                'received_at' => now(),
            ]);

            return redirect()->route('admin.inbox.show', $originalEmail)
                ->with('success', 'Balasan berhasil dikirim!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengirim balasan: ' . $e->getMessage())
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
        $email->moveToTrash();
        return redirect()->route('admin.inbox.index')
            ->with('success', 'Email dipindahkan ke trash.');
    }

    public function delete($id)
    {
        $email = EmailInbox::findOrFail($id);
        $email->delete();
        return redirect()->route('admin.inbox.index')
            ->with('success', 'Email berhasil dihapus.');
    }

    public function emptyTrash()
    {
        $count = EmailInbox::where('category', 'trash')->count();
        EmailInbox::where('category', 'trash')->delete();
        
        return redirect()->route('admin.inbox.index', ['category' => 'trash'])
            ->with('success', "{$count} email berhasil dihapus permanen dari trash.");
    }
}
