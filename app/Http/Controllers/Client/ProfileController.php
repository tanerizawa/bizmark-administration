<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $client = Auth::guard('client')->user();

        return view('client.profile.edit', compact('client'));
    }

    /**
     * Update the profile.
     */
    public function update(Request $request)
    {
        $client = Auth::guard('client')->user();

        Log::info('ProfileController@update called', [
            'client_id' => $client->id,
            'has_file' => $request->hasFile('profile_picture'),
            'all_files' => array_keys($request->allFiles()),
        ]);

        // Handle delete photo request
        if ($request->has('delete_photo')) {
            if ($client->profile_picture && \Storage::disk('public')->exists($client->profile_picture)) {
                \Storage::disk('public')->delete($client->profile_picture);
            }
            $client->update(['profile_picture' => null]);

            return back()->with('success', 'Foto profil berhasil dihapus');
        }

        // Validate profile picture separately first
        if ($request->hasFile('profile_picture')) {
            $request->validate([
                'profile_picture' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            ]);
        }

        // Base validation rules
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:clients,email,'.$client->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'npwp' => ['nullable', 'string', 'max:20'],
            'tax_name' => ['nullable', 'string', 'max:255'],
            'tax_address' => ['nullable', 'string'],
            'client_type' => ['required', 'in:individual,company'],
        ];

        // Add conditional rules for company
        if ($request->input('client_type') === 'company') {
            $rules['company_name'] = ['required', 'string', 'max:255'];
            $rules['industry'] = ['nullable', 'string', 'max:255'];
            $rules['contact_person'] = ['nullable', 'string', 'max:255'];
        } else {
            $rules['company_name'] = ['nullable', 'string', 'max:255'];
            $rules['industry'] = ['nullable', 'string', 'max:255'];
            $rules['contact_person'] = ['nullable', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture') && $request->file('profile_picture')->isValid()) {
            Log::info('Profile picture upload detected', [
                'original_name' => $request->file('profile_picture')->getClientOriginalName(),
                'size' => $request->file('profile_picture')->getSize(),
                'mime' => $request->file('profile_picture')->getMimeType(),
            ]);

            // Delete old profile picture if exists
            if ($client->profile_picture && \Storage::disk('public')->exists($client->profile_picture)) {
                \Storage::disk('public')->delete($client->profile_picture);
                Log::info('Old profile picture deleted', ['path' => $client->profile_picture]);
            }

            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            Log::info('New profile picture stored', ['path' => $path]);
            $validated['profile_picture'] = $path;
        } else {
            Log::info('No profile picture upload', [
                'has_file' => $request->hasFile('profile_picture'),
                'is_valid' => $request->hasFile('profile_picture') ? $request->file('profile_picture')->isValid() : false,
            ]);
        }

        $client->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui');
    }

    /**
     * Update notification preferences.
     */
    public function updateNotifications(Request $request)
    {
        $client = Auth::guard('client')->user();

        $client->update([
            'notif_email' => $request->boolean('notif_email'),
            'notif_whatsapp' => $request->boolean('notif_whatsapp'),
            'notif_push' => $request->boolean('notif_push'),
        ]);

        return back()->with('success', 'Preferensi notifikasi berhasil disimpan.');
    }

    /**
     * Update the client's password.
     */
    public function updatePassword(Request $request)
    {
        $client = Auth::guard('client')->user();

        $request->validate([
            'current_password' => ['required', 'current_password:client'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $client->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password berhasil diubah.');
    }

    /**
     * Redirect to 2FA setup (stub — feature not yet implemented).
     */
    public function twoFactorEnable()
    {
        return back()->with('error', 'Fitur 2FA belum tersedia. Segera hadir!');
    }

    /**
     * Disable 2FA (stub).
     */
    public function twoFactorDisable(Request $request)
    {
        $client = Auth::guard('client')->user();
        $client->update(['two_factor_secret' => null]);

        return back()->with('success', '2FA berhasil dinonaktifkan.');
    }

    /**
     * Delete the client account.
     */
    public function destroy(Request $request)
    {
        $client = Auth::guard('client')->user();

        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $client->delete();

        return redirect('/')->with('success', 'Akun Anda telah dihapus.');
    }
}
