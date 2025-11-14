<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:clients,email,' . $client->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:10'],
        ]);
        
        // Update client_type automatically
        if ($request->filled('company_name')) {
            $validated['client_type'] = 'company';
        } else {
            $validated['client_type'] = 'individual';
        }
        
        $client->update($validated);
        
        return back()->with('success', 'Profil berhasil diperbarui');
    }
    
    /**
     * Show change password form.
     */
    public function editPassword()
    {
        return view('client.profile.password');
    }
    
    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $client = Auth::guard('client')->user();
        
        $request->validate([
            'current_password' => ['required', 'current_password:client'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        
        $client->update([
            'password' => Hash::make($request->password),
        ]);
        
        return back()->with('success', 'Password berhasil diubah');
    }
}
