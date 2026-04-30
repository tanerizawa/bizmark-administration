<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailSubscriber;
use Illuminate\Http\Request;

class EmailSubscriberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = EmailSubscriber::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('email', 'ilike', '%' . $request->search . '%')
                  ->orWhere('name', 'ilike', '%' . $request->search . '%');
            });
        }

        $subscribers = $query->orderBy('subscribed_at', 'desc')->paginate(25);

        $stats = [
            'total' => EmailSubscriber::count(),
            'active' => EmailSubscriber::where('status', 'active')->count(),
            'unsubscribed' => EmailSubscriber::where('status', 'unsubscribed')->count(),
            'bounced' => EmailSubscriber::where('status', 'bounced')->count(),
        ];

        return view('admin.email.subscribers.index', compact('subscribers', 'stats'));
    }

    public function create()
    {
        return view('admin.email.subscribers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:email_subscribers,email',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'tags' => 'nullable|array',
        ]);

        $validated['source'] = 'manual';
        $validated['status'] = 'active';

        EmailSubscriber::create($validated);

        return redirect()->route('admin.subscribers.index')
            ->with('success', 'Subscriber berhasil ditambahkan.');
    }

    public function show($id)
    {
        $subscriber = EmailSubscriber::with('emailLogs')->findOrFail($id);
        return view('admin.email.subscribers.show', compact('subscriber'));
    }

    public function edit($id)
    {
        $subscriber = EmailSubscriber::findOrFail($id);
        return view('admin.email.subscribers.edit', compact('subscriber'));
    }

    public function update(Request $request, $id)
    {
        $subscriber = EmailSubscriber::findOrFail($id);
        
        $validated = $request->validate([
            'email' => 'required|email|unique:email_subscribers,email,' . $id,
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'tags' => 'nullable|array',
            'status' => 'required|in:active,unsubscribed,bounced',
        ]);

        $subscriber->update($validated);

        return redirect()->route('admin.subscribers.index')
            ->with('success', 'Subscriber berhasil diupdate.');
    }

    public function destroy($id)
    {
        $subscriber = EmailSubscriber::findOrFail($id);
        $subscriber->delete();

        return redirect()->route('admin.subscribers.index')
            ->with('success', 'Subscriber berhasil dihapus.');
    }
}

