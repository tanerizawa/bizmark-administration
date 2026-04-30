<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\TwoFactorTrustedDevice;
use App\Services\Security\TotpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    public function setup(Request $request, TotpService $totp)
    {
        $user = \App\Models\User::query()->findOrFail(Auth::guard('web')->id());

        $secret = (string) $request->session()->get('two_factor_setup_secret', '');
        if ($secret === '') {
            $secret = $totp->generateSecret();
            $request->session()->put('two_factor_setup_secret', $secret);
        }

        $label = $user->email;
        $issuer = config('app.name', 'Bizmark');
        $otpauthUrl = $totp->otpauthUrl($label, $issuer, $secret);

        return view('admin.security.2fa-setup', compact('secret', 'otpauthUrl'));
    }

    public function enable(Request $request, TotpService $totp)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = \App\Models\User::query()->findOrFail(Auth::guard('web')->id());
        $secret = (string) $request->session()->get('two_factor_setup_secret', '');
        abort_if($secret === '', 400, 'Setup session not found');

        abort_unless($totp->verify($secret, (string) $request->input('code')), 422, 'Invalid code');

        $recoveryCodesPlain = $this->generateRecoveryCodes(10);
        $recoveryCodesHashed = array_map(fn ($c) => hash('sha256', $c), $recoveryCodesPlain);

        $user->forceFill([
            'two_factor_secret' => Crypt::encryptString($secret),
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode($recoveryCodesHashed, JSON_UNESCAPED_UNICODE)),
            'two_factor_enabled_at' => now(),
        ])->save();

        $request->session()->forget('two_factor_setup_secret');
        $request->session()->put((string) config('two_factor.session_key', 'two_factor_verified_at'), now()->toIso8601String());

        $this->audit($user->id, '2fa_enabled', $request, null, null);

        return view('admin.security.2fa-recovery-codes', [
            'codes' => $recoveryCodesPlain,
        ]);
    }

    public function challenge()
    {
        return view('admin.security.2fa-challenge');
    }

    public function verifyChallenge(Request $request, TotpService $totp)
    {
        $request->validate([
            'code' => ['required', 'string'],
            'trust_device' => ['nullable', 'boolean'],
        ]);

        $user = \App\Models\User::query()->findOrFail(Auth::guard('web')->id());
        abort_unless($user->two_factor_enabled_at, 403, '2FA is not enabled');

        $secret = Crypt::decryptString((string) $user->two_factor_secret);
        $code = (string) $request->input('code');
        $ok = $totp->verify($secret, $code);

        $usedRecovery = false;
        if (! $ok) {
            $usedRecovery = $this->consumeRecoveryCode($user, $code);
            $ok = $usedRecovery;
        }

        abort_unless($ok, 422, 'Invalid code');

        $request->session()->put((string) config('two_factor.session_key', 'two_factor_verified_at'), now()->toIso8601String());

        if ($request->boolean('trust_device')) {
            $cookieName = (string) config('two_factor.cookie_name', 'two_factor_trust');
            $token = Str::random(64);
            TwoFactorTrustedDevice::create([
                'user_id' => $user->id,
                'token_hash' => hash('sha256', $token),
                'expires_at' => now()->addDays((int) config('two_factor.trust_days', 30)),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            cookie()->queue(cookie($cookieName, $token, (int) config('two_factor.trust_days', 30) * 24 * 60));
            $this->audit($user->id, '2fa_trust_device', $request, null, null);
        }

        $this->audit($user->id, $usedRecovery ? '2fa_challenge_passed_recovery' : '2fa_challenge_passed', $request, null, null);

        return redirect()->intended('/admin/dashboard');
    }

    public function regenerateRecoveryCodes(Request $request)
    {
        $user = \App\Models\User::query()->findOrFail(Auth::guard('web')->id());
        abort_unless($user->two_factor_enabled_at, 403, '2FA is not enabled');

        $codesPlain = $this->generateRecoveryCodes(10);
        $codesHashed = array_map(fn ($c) => hash('sha256', $c), $codesPlain);

        $user->forceFill([
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode($codesHashed, JSON_UNESCAPED_UNICODE)),
        ])->save();

        $this->audit($user->id, '2fa_recovery_regenerated', $request, null, null);

        return view('admin.security.2fa-recovery-codes', [
            'codes' => $codesPlain,
        ]);
    }

    public function disable(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = \App\Models\User::query()->findOrFail(Auth::guard('web')->id());
        abort_unless(Hash::check((string) $request->input('password'), (string) $user->password), 422, 'Invalid password');

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_enabled_at' => null,
        ])->save();

        TwoFactorTrustedDevice::query()->where('user_id', $user->id)->delete();

        $this->audit($user->id, '2fa_disabled', $request, null, null);

        return redirect()->route('admin.profile.edit')->with('success', '2FA dinonaktifkan.');
    }

    private function consumeRecoveryCode($user, string $code): bool
    {
        $code = strtoupper(preg_replace('/\s+/', '', $code) ?? '');
        if (strlen($code) < 8) {
            return false;
        }

        $encrypted = (string) $user->two_factor_recovery_codes;
        if ($encrypted === '') {
            return false;
        }

        $list = json_decode(Crypt::decryptString($encrypted), true);
        if (! is_array($list)) {
            return false;
        }

        $hash = hash('sha256', $code);
        $idx = array_search($hash, $list, true);
        if ($idx === false) {
            return false;
        }

        unset($list[$idx]);
        $user->forceFill([
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode(array_values($list), JSON_UNESCAPED_UNICODE)),
        ])->save();

        return true;
    }

    private function generateRecoveryCodes(int $count): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(Str::random(10));
        }

        return $codes;
    }

    private function audit(int $userId, string $event, Request $request, ?array $old, ?array $new): void
    {
        AdminAuditLog::create([
            'user_id' => $userId,
            'event' => $event,
            'auditable_type' => \App\Models\User::class,
            'auditable_id' => $userId,
            'old_values' => $old,
            'new_values' => $new,
            'route' => optional($request->route())->getName(),
            'method' => $request->getMethod(),
            'url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
