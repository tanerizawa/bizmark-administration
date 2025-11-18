<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use App\Models\Client;
use Illuminate\Support\Str;

class ClientAuthController extends Controller
{
    /**
     * Show the client login form.
     */
    public function showLoginForm()
    {
        return view('client.auth.login');
    }

    /**
     * Show the client registration form.
     */
    public function showRegistrationForm()
    {
        return view('client.auth.register');
    }

    /**
     * Handle client registration.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:clients'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Create the client
        $client = Client::create([
            'name' => $validated['name'],
            'company_name' => $validated['company_name'] ?? $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'status' => 'active',
            'client_type' => $validated['company_name'] ? 'company' : 'individual',
        ]);

        // Send email verification notification
        $client->sendEmailVerificationNotification();

        // Log the client in
        Auth::guard('client')->login($client);

        return redirect()->route('client.verification.notice')
            ->with('success', 'Akun Anda berhasil dibuat! Silakan verifikasi email Anda.');
    }

    /**
     * Handle client login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        // Attempt to authenticate using the 'client' guard
        if (Auth::guard('client')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('client.dashboard'))
                ->with('success', 'Login berhasil. Anda sudah masuk ke Portal Klien Bizmark.id.');
        }

        return back()->withErrors([
            'email' => 'Kredensial yang Anda masukkan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    /**
     * Handle client logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('client')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing')
            ->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Show the password reset request form.
     */
    public function showForgotPasswordForm()
    {
        return view('client.auth.forgot-password');
    }

    /**
     * Handle password reset request.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('clients')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show the password reset form.
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        return view('client.auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Handle password reset.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::broker('clients')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($client, $password) {
                $client->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $client->save();

                event(new PasswordReset($client));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('client.login')->with('success', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * Show email verification notice.
     */
    public function showVerifyEmailNotice()
    {
        $client = Auth::guard('client')->user();
        
        if ($client->hasVerifiedEmail()) {
            return redirect()->route('client.dashboard');
        }
        
        return view('client.auth.verify-email');
    }

    /**
     * Handle email verification.
     */
    public function verifyEmail(Request $request, $id, $hash)
    {
        $client = Client::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($client->getEmailForVerification()))) {
            return redirect()->route('client.login')
                ->withErrors(['email' => 'Link verifikasi tidak valid.']);
        }

        if ($client->hasVerifiedEmail()) {
            return redirect()->route('client.dashboard')
                ->with('success', 'Email Anda sudah terverifikasi!');
        }

        if ($client->markEmailAsVerified()) {
            event(new Verified($client));
        }

        return redirect()->route('client.dashboard')
            ->with('success', 'Email Anda berhasil diverifikasi!');
    }

    /**
     * Resend email verification notification.
     */
    public function resendVerificationEmail(Request $request)
    {
        $client = Auth::guard('client')->user();

        if ($client->hasVerifiedEmail()) {
            return redirect()->route('client.dashboard');
        }

        $client->sendEmailVerificationNotification();

        return back()->with('success', 'Link verifikasi baru telah dikirim ke email Anda!');
    }
}
