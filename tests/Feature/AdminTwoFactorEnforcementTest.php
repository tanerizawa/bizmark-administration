<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\TwoFactorTrustedDevice;
use App\Models\User;
use App\Services\Security\TotpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminTwoFactorEnforcementTest extends TestCase
{
    use RefreshDatabase;

    public function test_high_risk_route_redirects_to_setup_when_grace_expired_and_2fa_not_enabled(): void
    {
        $role = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => null,
            'is_system' => true,
        ]);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'two_factor_enabled_at' => null,
            'two_factor_grace_until' => now()->subDay(),
        ]);

        $this->actingAs($user)->get('/admin/security/webhook-metrics')
            ->assertRedirect(route('admin.security.2fa.setup'));
    }

    public function test_high_risk_route_redirects_to_challenge_when_2fa_enabled_but_not_verified(): void
    {
        $role = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => null,
            'is_system' => true,
        ]);

        $totp = app(TotpService::class);
        $secret = $totp->generateSecret();

        $user = User::factory()->create([
            'role_id' => $role->id,
            'two_factor_secret' => Crypt::encryptString($secret),
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode([hash('sha256', 'RECOVERYCODE')], JSON_UNESCAPED_UNICODE)),
            'two_factor_enabled_at' => now(),
        ]);

        $this->actingAs($user)->get('/admin/security/webhook-metrics')
            ->assertRedirect(route('admin.security.2fa.challenge'));
    }

    public function test_challenge_verification_allows_access_after_success(): void
    {
        $role = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => null,
            'is_system' => true,
        ]);

        $totp = app(TotpService::class);
        $secret = $totp->generateSecret();
        $code = $totp->at($secret, time());

        $user = User::factory()->create([
            'role_id' => $role->id,
            'two_factor_secret' => Crypt::encryptString($secret),
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode([], JSON_UNESCAPED_UNICODE)),
            'two_factor_enabled_at' => now(),
        ]);

        $this->actingAs($user)->post(route('admin.security.2fa.verify'), [
            'code' => $code,
        ])->assertRedirect('/admin/dashboard');

        $this->actingAs($user)->get('/admin/security/webhook-metrics')->assertOk();
    }

    public function test_trusted_device_cookie_allows_access_without_session_verification(): void
    {
        $role = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => null,
            'is_system' => true,
        ]);

        $totp = app(TotpService::class);
        $secret = $totp->generateSecret();

        $user = User::factory()->create([
            'role_id' => $role->id,
            'two_factor_secret' => Crypt::encryptString($secret),
            'two_factor_recovery_codes' => Crypt::encryptString(json_encode([], JSON_UNESCAPED_UNICODE)),
            'two_factor_enabled_at' => now(),
        ]);

        $token = Str::random(64);
        TwoFactorTrustedDevice::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addDays(30),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'phpunit',
        ]);

        $cookieName = (string) config('two_factor.cookie_name', 'two_factor_trust');
        $this->actingAs($user)
            ->withCookie($cookieName, $token)
            ->get('/admin/security/webhook-metrics')
            ->assertOk();
    }
}

