<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\TwoFactorTrustedDevice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UnifiedLoginController extends Controller
{
    /**
     * Show the unified login form.
     * Auto-detects user type and redirects accordingly.
     */
    public function showLoginForm(Request $request)
    {
        // If already logged in as admin, redirect to admin dashboard
        if (Auth::guard('web')->check()) {
            return redirect('/dashboard');
        }

        // If already logged in as client, redirect to client dashboard
        if (Auth::guard('client')->check()) {
            return redirect()->route('client.dashboard');
        }

        // Store redirect URL so intended() picks it up after login
        if ($request->filled('redirect')) {
            $redirect = $request->input('redirect');
            // Only allow local paths or absolute URLs pointing back to this app.
            if ($this->isSafeRedirect($redirect)) {
                session()->put('url.intended', $redirect);
            }
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

            $user = User::query()->findOrFail(Auth::guard('web')->id());
            $sessionKey = (string) config('two_factor.session_key', 'two_factor_verified_at');
            $request->session()->forget($sessionKey);

            if ((bool) config('two_factor.enabled', true)) {
                if (! $user->two_factor_enabled_at && ! $user->two_factor_grace_until) {
                    $user->forceFill([
                        'two_factor_grace_until' => now()->addDays((int) config('two_factor.grace_days', 7)),
                    ])->save();
                }

                if ($user->two_factor_enabled_at) {
                    $cookieName = (string) config('two_factor.cookie_name', 'two_factor_trust');
                    $token = (string) $request->cookie($cookieName, '');
                    if ($token !== '') {
                        $trusted = TwoFactorTrustedDevice::query()
                            ->where('user_id', $user->id)
                            ->where('token_hash', hash('sha256', $token))
                            ->where('expires_at', '>', now())
                            ->exists();
                        if ($trusted) {
                            $request->session()->put($sessionKey, now()->toIso8601String());
                        }
                    }

                    if (! $request->session()->has($sessionKey)) {
                        return redirect()->route('admin.security.2fa.challenge');
                    }
                }
            }

            return redirect()->intended('/dashboard')
                ->with('success', 'Selamat datang kembali, Admin!');
        }

        // Try to authenticate as client (client guard)
        if (Auth::guard('client')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Prevent clients from being redirected to admin paths
            $intended = $request->session()->get('url.intended', '');
            if ($intended && $this->isAdminPath($intended)) {
                $request->session()->forget('url.intended');
            }

            $user = Auth::guard('client')->user();
            $firstName = \Illuminate\Support\Str::of($user->name ?? '')->before(' ')->title();
            $welcomeMsg = $firstName ? "Selamat datang kembali, {$firstName} 👋" : 'Selamat datang kembali!';

            return redirect()->intended(route('client.dashboard'))
                ->with('success', $welcomeMsg);
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

    /**
     * Check if a path points to the admin panel.
     */
    protected function isAdminPath(string $path): bool
    {
        $localPath = parse_url($path, PHP_URL_PATH) ?? $path;

        return Str::startsWith(ltrim($localPath, '/'), 'admin');
    }

    /**
     * Accept either a local path or an absolute URL for the current app host.
     */
    protected function isSafeRedirect(string $redirect): bool
    {
        if (Str::startsWith($redirect, '//')) {
            return false;
        }

        if (Str::startsWith($redirect, '/')) {
            return true;
        }

        if (! filter_var($redirect, FILTER_VALIDATE_URL)) {
            return false;
        }

        $targetHost = parse_url($redirect, PHP_URL_HOST);
        $appHost = parse_url(url('/'), PHP_URL_HOST);

        return filled($targetHost) && filled($appHost) && hash_equals($appHost, $targetHost);
    }
}
