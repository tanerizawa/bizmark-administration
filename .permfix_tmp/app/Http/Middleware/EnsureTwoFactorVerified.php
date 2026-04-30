<?php

namespace App\Http\Middleware;

use App\Models\TwoFactorTrustedDevice;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!(bool) config('two_factor.enabled', true)) {
            return $next($request);
        }

        if (!Auth::guard('web')->check()) {
            return $next($request);
        }

        $user = Auth::guard('web')->user();
        $sessionKey = (string) config('two_factor.session_key', 'two_factor_verified_at');

        if ($request->session()->has($sessionKey)) {
            return $next($request);
        }

        $cookieName = (string) config('two_factor.cookie_name', 'two_factor_trust');
        $token = (string) $request->cookie($cookieName, '');
        if ($token !== '') {
            $hash = hash('sha256', $token);
            $trusted = TwoFactorTrustedDevice::query()
                ->where('user_id', $user->id)
                ->where('token_hash', $hash)
                ->where('expires_at', '>', now())
                ->exists();

            if ($trusted) {
                $request->session()->put($sessionKey, now()->toIso8601String());
                return $next($request);
            }
        }

        if (!$user->two_factor_enabled_at) {
            if ($user->two_factor_grace_until && now()->lessThanOrEqualTo($user->two_factor_grace_until)) {
                return $next($request);
            }

            return redirect()->route('admin.security.2fa.setup');
        }

        return redirect()->route('admin.security.2fa.challenge');
    }
}

