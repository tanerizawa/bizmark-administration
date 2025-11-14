<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermitApplication;
use App\Models\ApplicationNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationNoteController extends Controller
{
    /**
     * Store a new note (admin side)
     */
    public function store(Request $request, $applicationId)
    {
        $request->validate([
            'note' => 'required|string|max:5000',
            'is_internal' => 'boolean',
        ]);

        $application = PermitApplication::findOrFail($applicationId);

        $note = ApplicationNote::create([
            'application_id' => $application->id,
            'author_type' => 'admin',
            'author_id' => Auth::id(),
            'note' => $request->note,
            'is_internal' => $request->boolean('is_internal', false),
        ]);

        // Send email notification to client if not internal
        if (!$note->is_internal && $application->client) {
            $application->client->notify(new \App\Notifications\AdminNoteNotification($note));
        }

        return back()->with('success', 'Catatan berhasil ditambahkan');
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

    /**
     * Delete note
     */
    public function destroy($noteId)
    {
        $note = ApplicationNote::findOrFail($noteId);
        
        // Only allow author or admin to delete
        if ($note->author_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki izin untuk menghapus catatan ini');
        }

        $note->delete();

        return back()->with('success', 'Catatan berhasil dihapus');
    }
}
