<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\PermitApplication;
use App\Models\ApplicationNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationNoteController extends Controller
{
    /**
     * Store a new note (client side)
     */
    public function store(Request $request, $applicationId)
    {
        $request->validate([
            'note' => 'required|string|max:5000',
        ]);

        $application = PermitApplication::where('client_id', Auth::guard('client')->id())
            ->findOrFail($applicationId);

        $clientId = Auth::guard('client')->id();
        
        $note = ApplicationNote::create([
            'application_id' => $application->id,
            'author_type' => 'client',
            'author_id' => $clientId, // Now nullable, will store client_id
            'note' => $request->note,
            'is_internal' => false, // Client notes are never internal
        ]);

        // Send email notification to admins (wrapped in try-catch to prevent errors)
        try {
            $admins = \App\Models\User::all();
            if ($admins->count() > 0 && class_exists('\App\Notifications\ClientNoteNotification')) {
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\ClientNoteNotification($note));
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to send client note notification: ' . $e->getMessage());
        }

        return back()->with('success', 'Pesan berhasil dikirim');
    }

    /**
     * Mark note as read
     */
    public function markAsRead($noteId)
    {
        $note = ApplicationNote::findOrFail($noteId);
        $note->markAsRead();

        return response()->json(['success' => true]);
    }
}
