<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show user profile
     */
    public function show()
    {
        $user = auth()->user();
        $user->load(['role']);
        
        $stats = [
            'projects_managed' => $user->managedProjects()->count(),
            'tasks_completed' => $user->assignedTasks()->where('status', 'done')->count(),
            'approvals_done' => $user->approvedExpenses()->count() + 
                               $user->reviewedDocuments()->count(),
        ];
        
        return view('mobile.profile.show', compact('user', 'stats'));
    }
    
    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20'
        ]);
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone
        ]);
        
        return response()->json([
            'success' => true,
            'user' => $user,
            'message' => 'Profile berhasil diupdate'
        ]);
    }
    
    /**
     * Upload avatar
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        $user = auth()->user();
        
        // Delete old avatar
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        
        $user->update(['avatar' => $path]);
        
        return response()->json([
            'success' => true,
            'avatar_url' => Storage::url($path),
            'message' => 'Avatar berhasil diupdate'
        ]);
    }
    
    /**
     * Update preferences (notifications, theme, etc)
     */
    public function updatePreferences(Request $request)
    {
        $user = auth()->user();
        
        $preferences = $user->preferences ?? [];
        
        // Update specific preferences
        if ($request->has('notifications_enabled')) {
            $preferences['notifications_enabled'] = $request->boolean('notifications_enabled');
        }
        
        if ($request->has('email_notifications')) {
            $preferences['email_notifications'] = $request->boolean('email_notifications');
        }
        
        if ($request->has('push_notifications')) {
            $preferences['push_notifications'] = $request->boolean('push_notifications');
        }
        
        if ($request->has('dark_mode')) {
            $preferences['dark_mode'] = $request->boolean('dark_mode');
        }
        
        $user->update(['preferences' => $preferences]);
        
        return response()->json([
            'success' => true,
            'preferences' => $preferences,
            'message' => 'Preferences berhasil diupdate'
        ]);
    }
}
