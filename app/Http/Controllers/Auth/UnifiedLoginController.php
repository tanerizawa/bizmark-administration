<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UnifiedLoginController extends Controller
{
    /**
     * Show the unified login form.
     * Auto-detects user type and redirects accordingly.
     */
    public function showLoginForm()
    {
        // If already logged in as admin, redirect to admin dashboard
        if (Auth::guard('web')->check()) {
            return redirect('/dashboard');
        }
        
        // If already logged in as client, redirect to client dashboard
        if (Auth::guard('client')->check()) {
            return redirect()->route('client.dashboard');
        }
        
        return view('auth.unified-login');
    }

    /**
     * Handle unified login request.
     * Automatically detects whether user is admin or client.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        // Try to authenticate as admin first (web guard)
        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            return redirect()->intended('/dashboard')
                ->with('success', 'Selamat datang kembali, Admin!');
        }

        // Try to authenticate as client (client guard)
        if (Auth::guard('client')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            return redirect()->intended(route('client.dashboard'))
                ->with('success', 'Selamat datang di Portal Klien Bizmark.id!');
        }

        // Both authentication attempts failed
        throw ValidationException::withMessages([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ]);
    }

    /**
     * Handle unified logout.
     * Detects which guard is currently authenticated and logs out.
     */
    public function logout(Request $request)
    {
        // Logout from admin guard if authenticated
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }
        
        // Logout from client guard if authenticated
        if (Auth::guard('client')->check()) {
            Auth::guard('client')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')
            ->with('success', 'Anda telah berhasil keluar.');
    }

    /**
     * Show forgot password form (unified for both guards).
     */
    public function showForgotPasswordForm()
    {
        return view('auth.unified-forgot-password');
    }

    /**
     * Get the guard to be used during authentication.
     * This is a helper method for future use.
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
